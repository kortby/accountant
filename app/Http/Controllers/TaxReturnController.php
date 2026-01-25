<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxInformationRequest;
use App\Models\ClientProfile;
use App\Models\Dependent;
use App\Models\TaxReturn;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaxReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaxReturn::where('user_id', auth()->id());

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
                ->withQueryString(), // Keeps search params in pagination links
            'filters' => $request->only(['search', 'status']), // Pass state back to frontend
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxInformationRequest $request)
    {
        try {
            DB::beginTransaction();

            // 1. Update/Create Profile (Existing logic...)
            $profile = ClientProfile::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'social_security_number' => $request->ssn,
                    'date_of_birth' => $request->date_of_birth,
                    'occupation' => $request->occupation,
                    'marital_status' => $request->marital_status,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'has_dependents' => !empty($request->dependents),
                ]
            );

            // 2. Create Tax Return (Existing logic...)
            $taxReturn = TaxReturn::create([
                'user_id' => Auth::id(),
                'tax_year' => $request->tax_year,
                'status' => 'draft',
                'total_income' => 0,
                'taxable_income' => 0,
                'tax_liability' => 0,
                'total_deductions' => 0,
                'total_credits' => 0,
                'amount_due' => 0,
                'refund_amount' => 0,
            ]);

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
            $message .= "- " . $this->getIncomeSourceName($type) . "\n";
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
        // 1. Use load() to eager load relationships on the existing model instance
        // 2. Use 'media' (standard Spatie relationship), not 'Documents'
        $taxReturn->load(['user', 'deductions', 'incomeSources', 'media']);

        return Inertia::render('TaxReturnDetail', [
            'taxReturn' => $taxReturn,
            // Ensure you load dependents if they aren't already loaded on the user
            'clientProfile' => auth()->user()->clientProfile()->with('dependents')->first(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxReturn $taxReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxReturn $taxReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxReturn $taxReturn)
    {
        //
    }
}
