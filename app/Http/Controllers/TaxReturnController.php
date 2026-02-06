<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxInformationRequest;
use App\Models\ClientProfile;
use App\Models\TaxReturn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TaxReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TaxReturn::with(['user', 'accountant']);

        // Role-based filtering
        if ($user->role === 'client') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'accountant') {
            // Accountants see their assigned returns or unassigned returns
            $query->where(function ($q) use ($user) {
                $q->where('accountant_id', $user->id)
                    ->orWhereNull('accountant_id');
            });
        }
        // Admins see all returns

        // 1. Search Logic (by ID or Tax Year)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('tax_year', 'like', "%{$search}%");
            });
        }

        // 2. Filter Logic (by Status)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return Inertia::render('TaxReturnIndex', [
            'taxReturns' => $query->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource (Client filing their own taxes)
     */
    public function create()
    {
        // Clients can only file for themselves
        return Inertia::render('TaxReturnForm', [
            'clients' => [],
            'isAccountantFiling' => false,
        ]);
    }

    /**
     * Show the form for accountant to file on behalf of a client
     */
    public function createForClient()
    {
        $user = Auth::user();

        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $clients = User::where('role', 'client')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->last_name.', '.$user->first_name.' ('.$user->email.')',
            ]);

        return Inertia::render('TaxReturnForm', [
            'clients' => $clients,
            'isAccountantFiling' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxInformationRequest $request)
    {
        try {
            DB::beginTransaction();

            $currentUser = Auth::user();
            $userId = Auth::id();
            $accountantId = null;
            $status = TaxReturn::STATUS_SUBMITTED;

            // If accountant/admin is filing for someone else
            if (in_array($currentUser->role, ['accountant', 'admin']) && $request->filled('user_id')) {
                $userId = $request->user_id;
                $accountantId = $currentUser->id;
                $status = TaxReturn::STATUS_ASSIGNED; // Auto-assign to the accountant filing
            }

            // 1. Update/Create Profile
            $profileData = [
                'user_id' => $userId,
                'social_security_number' => $request->ssn,
                'date_of_birth' => $request->date_of_birth,
                'occupation' => $request->occupation,
                'marital_status' => $request->marital_status,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'has_dependents' => ! empty($request->dependents),
            ];

            // Add spouse data if married filing jointly
            if ($request->marital_status === 'married_filing_jointly') {
                $profileData = array_merge($profileData, [
                    'spouse_first_name' => $request->spouse_first_name,
                    'spouse_middle_name' => $request->spouse_middle_name,
                    'spouse_last_name' => $request->spouse_last_name,
                    'spouse_social_security_number' => $request->spouse_social_security_number,
                    'spouse_date_of_birth' => $request->spouse_date_of_birth,
                    'spouse_occupation' => $request->spouse_occupation,
                ]);
            }

            $profile = ClientProfile::updateOrCreate(
                ['user_id' => $userId],
                $profileData
            );

            // 2. Create Tax Return
            $taxReturn = TaxReturn::create([
                'user_id' => $userId,
                'accountant_id' => $accountantId,
                'tax_year' => $request->tax_year,
                'status' => $status,
                'submitted_at' => now(),
                'assigned_at' => $accountantId ? now() : null,
                'total_income' => 0,
                'taxable_income' => 0,
                'tax_liability' => 0,
                'total_deductions' => 0,
                'total_credits' => 0,
                'amount_due' => 0,
                'refund_amount' => 0,
            ]);

            Mail::to($profile->user->email)->send(new \App\Mail\TaxReturnSubmitted($taxReturn));

            Mail::to('boutout.ea@gmail.com')->send(new \App\Mail\NewTaxReturnNotification($taxReturn, $this->generateInitialMessage($request)));

            // 3. Handle Dependents (Existing logic...)
            if ($request->has('dependents')) {
                $profile->dependents()->delete();
                foreach ($request->dependents as $dependentData) {
                    $profile->dependents()->create($dependentData);
                }
            }

            // 4. Handle Income Sources (Existing logic...)
            foreach ($request->income_types as $type) {
                $taxReturn->incomeSources()->create([
                    'type' => $type,
                    'source_name' => $this->getIncomeSourceName($type),
                    'amount' => 0,
                ]);
            }

            // --- 5. SPATIE MEDIA UPLOAD LOGIC ---
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $taxReturn->addMedia($file)
                        ->toMediaCollection('documents');
                }
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('flash.banner', 'Tax information and documents submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging: Log::error($e);
            return back()->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    private function getIncomeSourceName($type)
    {
        return match ($type) {
            'w2' => 'Employment Income',
            '1099_k' => 'Third Party Transaction Income',
            '1099_nec' => 'Contractor Income',
            '1099_int' => 'Interest Income',
            '1099_div' => 'Investment Income',
            'business' => 'Business Income',
            'rental' => 'Rental Income',
            'retirement' => 'Retirement Income',
            'other' => 'Other Income',
        };
    }

    private function generateInitialMessage(Request $request)
    {
        $message = "New tax return request for {$request->tax_year}.\n\n";
        $message .= "Income Types:\n";
        foreach ($request->income_types as $type) {
            $message .= '- '.$this->getIncomeSourceName($type)."\n";
        }

        if ($request->notes) {
            $message .= "\nClient Notes:\n{$request->notes}";
        }

        return $message;
    }

    private function getAvailableAccountant()
    {
        // Implement your accountant assignment logic here
        // This could be based on various factors like workload, specialization, etc.
        return User::where('role', 'accountant')
            ->orderBy(DB::raw('(SELECT COUNT(*) FROM tax_returns WHERE accountant_id = users.id)'))
            ->first()
            ->id;
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxReturn $taxReturn)
    {
        // Authorization check
        $user = auth()->user();
        if ($user->role === 'client' && $taxReturn->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        } elseif ($user->role === 'accountant' && $taxReturn->accountant_id !== $user->id && $taxReturn->accountant_id !== null) {
            abort(403, 'Unauthorized access.');
        }

        $taxReturn->load(['user', 'accountant', 'deductions', 'incomeSources', 'media']);

        // Load client profile based on the tax return's user, not auth user
        $clientProfile = ClientProfile::where('user_id', $taxReturn->user_id)
            ->with(['dependents', 'user'])
            ->first();

        return Inertia::render('TaxReturnDetail', [
            'taxReturn' => $taxReturn,
            'clientProfile' => $clientProfile,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxReturn $taxReturn)
    {
        $user = auth()->user();

        // Only accountants and admins can edit
        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        // Accountants can only edit their assigned returns
        if ($user->role === 'accountant' && $taxReturn->accountant_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $taxReturn->load(['user', 'accountant', 'deductions', 'incomeSources', 'media']);

        // Load client profile
        $clientProfile = ClientProfile::where('user_id', $taxReturn->user_id)
            ->with('dependents')
            ->first();

        // Get all accountants for assignment dropdown (admin only)
        $accountants = [];
        if ($user->role === 'admin') {
            $accountants = User::where('role', 'accountant')
                ->select('id', 'first_name', 'last_name', 'email')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
                ->map(fn ($accountant) => [
                    'id' => $accountant->id,
                    'name' => $accountant->last_name.', '.$accountant->first_name,
                ]);
        }

        return Inertia::render('TaxReturnEdit', [
            'taxReturn' => $taxReturn,
            'clientProfile' => $clientProfile,
            'accountants' => $accountants,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxReturn $taxReturn)
    {
        $user = auth()->user();

        // Only accountants and admins can update
        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        // Accountants can only update their assigned returns
        if ($user->role === 'accountant' && $taxReturn->accountant_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'income_sources' => 'nullable|array',
            'income_sources.*.id' => 'required|exists:income_sources,id',
            'income_sources.*.amount' => 'required|numeric|min:0',
            'deductions' => 'nullable|array',
            'deductions.*.id' => 'nullable|exists:deductions,id',
            'deductions.*.category' => 'required|string|max:255',
            'deductions.*.amount' => 'required|numeric|min:0',
            'deductions.*.description' => 'nullable|string|max:1000',
            'taxable_income' => 'nullable|numeric|min:0',
            'tax_liability' => 'nullable|numeric|min:0',
            'total_credits' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:'.implode(',', [
                TaxReturn::STATUS_DRAFT,
                TaxReturn::STATUS_SUBMITTED,
                TaxReturn::STATUS_ASSIGNED,
                TaxReturn::STATUS_UNDER_REVIEW,
                TaxReturn::STATUS_NEEDS_ACTION,
                TaxReturn::STATUS_COMPLETED,
            ]),
            'accountant_notes' => 'nullable|string|max:5000',
            'documents' => 'nullable|array',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Update income sources
            if ($request->has('income_sources')) {
                foreach ($request->income_sources as $sourceData) {
                    $taxReturn->incomeSources()
                        ->where('id', $sourceData['id'])
                        ->update(['amount' => $sourceData['amount']]);
                }
            }

            // Update or create deductions
            if ($request->has('deductions')) {
                foreach ($request->deductions as $deductionData) {
                    if (isset($deductionData['id'])) {
                        // Update existing deduction
                        $taxReturn->deductions()
                            ->where('id', $deductionData['id'])
                            ->update([
                                'category' => $deductionData['category'],
                                'amount' => $deductionData['amount'],
                                'description' => $deductionData['description'] ?? null,
                            ]);
                    } else {
                        // Create new deduction
                        $taxReturn->deductions()->create([
                            'category' => $deductionData['category'],
                            'amount' => $deductionData['amount'],
                            'description' => $deductionData['description'] ?? null,
                        ]);
                    }
                }
            }

            // Recalculate totals
            $totalIncome = $taxReturn->incomeSources()->sum('amount');
            $totalDeductions = $taxReturn->deductions()->sum('amount');
            $taxableIncome = $request->filled('taxable_income') ? $request->taxable_income : max(0, $totalIncome - $totalDeductions);
            $taxLiability = $request->filled('tax_liability') ? $request->tax_liability : 0;
            $totalCredits = $request->filled('total_credits') ? $request->total_credits : 0;
            $finalAmount = $taxLiability - $totalCredits;

            $updates = [
                'total_income' => $totalIncome,
                'total_deductions' => $totalDeductions,
                'taxable_income' => $taxableIncome,
                'tax_liability' => $taxLiability,
                'total_credits' => $totalCredits,
                'amount_due' => $finalAmount > 0 ? $finalAmount : 0,
                'refund_amount' => $finalAmount < 0 ? abs($finalAmount) : 0,
            ];

            // Update status if provided
            if ($request->filled('status')) {
                $updates['status'] = $request->status;

                if ($request->status === TaxReturn::STATUS_UNDER_REVIEW && ! $taxReturn->reviewed_at) {
                    $updates['reviewed_at'] = now();
                } elseif ($request->status === TaxReturn::STATUS_COMPLETED && ! $taxReturn->completed_at) {
                    $updates['completed_at'] = now();
                }
            }

            // Update accountant notes
            if ($request->filled('accountant_notes')) {
                $updates['accountant_notes'] = $request->accountant_notes;
            }

            $taxReturn->update($updates);

            // Handle document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $taxReturn->addMedia($file)
                        ->toMediaCollection('documents');
                }
            }

            DB::commit();

            return redirect()->route('tax-information.show', $taxReturn)
                ->with('flash.banner', 'Tax return updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to update tax return.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxReturn $taxReturn)
    {
        //
    }

    /**
     * Assign tax return to accountant
     */
    public function assign(Request $request, TaxReturn $taxReturn)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        // Accountant can assign to themselves, admin can assign to any accountant
        $accountantId = $user->role === 'accountant' ? $user->id : $request->accountant_id;

        $taxReturn->update([
            'accountant_id' => $accountantId,
            'assigned_at' => now(),
            'status' => TaxReturn::STATUS_ASSIGNED,
        ]);

        return redirect()->back()->with('flash.banner', 'Tax return assigned successfully.');
    }

    /**
     * Update status of tax return
     */
    public function updateStatus(Request $request, TaxReturn $taxReturn)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:'.implode(',', [
                TaxReturn::STATUS_DRAFT,
                TaxReturn::STATUS_SUBMITTED,
                TaxReturn::STATUS_ASSIGNED,
                TaxReturn::STATUS_UNDER_REVIEW,
                TaxReturn::STATUS_NEEDS_ACTION,
                TaxReturn::STATUS_COMPLETED,
            ]),
            'notes' => 'nullable|string|max:5000',
        ]);

        $updates = ['status' => $request->status];

        if ($request->status === TaxReturn::STATUS_UNDER_REVIEW) {
            $updates['reviewed_at'] = now();
        } elseif ($request->status === TaxReturn::STATUS_COMPLETED) {
            $updates['completed_at'] = now();
        }

        if ($request->filled('notes')) {
            $updates['accountant_notes'] = $request->notes;
        }

        $taxReturn->update($updates);

        return redirect()->back()->with('flash.banner', 'Status updated successfully.');
    }

    /**
     * Update income source amounts
     */
    public function updateIncome(Request $request, TaxReturn $taxReturn)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'income_sources' => 'required|array',
            'income_sources.*.id' => 'required|exists:income_sources,id',
            'income_sources.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->income_sources as $sourceData) {
                $taxReturn->incomeSources()
                    ->where('id', $sourceData['id'])
                    ->update(['amount' => $sourceData['amount']]);
            }

            // Recalculate totals
            $totalIncome = $taxReturn->incomeSources()->sum('amount');
            $totalDeductions = $taxReturn->deductions()->sum('amount');

            $taxReturn->update([
                'total_income' => $totalIncome,
                'total_deductions' => $totalDeductions,
            ]);

            DB::commit();

            return redirect()->back()->with('flash.banner', 'Income updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to update income.']);
        }
    }

    /**
     * Add deduction to tax return
     */
    public function addDeduction(Request $request, TaxReturn $taxReturn)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $taxReturn->deductions()->create([
            'category' => $request->category,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Recalculate totals
        $totalDeductions = $taxReturn->deductions()->sum('amount');
        $taxReturn->update(['total_deductions' => $totalDeductions]);

        return redirect()->back()->with('flash.banner', 'Deduction added successfully.');
    }

    /**
     * Upload additional documents to tax return
     */
    public function uploadDocuments(Request $request, TaxReturn $taxReturn)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['accountant', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $taxReturn->addMedia($file)
                    ->toMediaCollection('documents');
            }
        }

        return redirect()->back()->with('flash.banner', 'Documents uploaded successfully.');
    }
}
