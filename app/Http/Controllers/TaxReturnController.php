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
    public function index()
    {
        return Inertia::render('TaxReturnIndex', [
            'taxReturns' => TaxReturn::where('user_id', auth()->id())->get(),
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

            // Update or create client profile
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

            // Create new tax return
            $taxReturn = TaxReturn::create([
                'user_id' => Auth::id(),
                'tax_year' => $request->tax_year,
                'status' => 'draft',
                'total_income' => 0, // Will be updated when documents are processed
                'taxable_income' => 0,
                'tax_liability' => 0,
                'total_deductions' => 0,
                'total_credits' => 0,
                'amount_due' => 0,
                'refund_amount' => 0,
            ]);

            // Handle dependents
            if ($request->has('dependents')) {
                // Delete existing dependents
                $profile->dependents()->delete();

                // Create new dependents
                foreach ($request->dependents as $dependentData) {
                    $dependent = new Dependent($dependentData);
                    $profile->dependents()->save($dependent);
                }
            }

            // Create income sources placeholders based on income types
            foreach ($request->income_types as $type) {
                $taxReturn->incomeSources()->create([
                    'type' => $type,
                    'source_name' => $this->getIncomeSourceName($type),
                    'amount' => 0, // Will be updated when documents are uploaded
                ]);
            }

            // Create initial message for accountant
            // Message::create([
            //     'tax_return_id' => $taxReturn->id,
            //     'sender_id' => Auth::id(),
            //     'recipient_id' => $this->getAvailableAccountant(), // Implement accountant assignment logic
            //     'subject' => "New Tax Return Request for {$request->tax_year}",
            //     'content' => $this->generateInitialMessage($request),
            //     'category' => 'tax_question',
            //     'priority' => 'normal',
            // ]);

            DB::commit();


            $request->session()->flash('flash.banner', 'Tax information submitted successfully. An accountant will be assigned to your case shortly.');
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while submitting your tax information. Please try again.']);
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
        return Inertia::render('TaxReturnDetail', [
            'taxReturn' => $taxReturn->with('user', 'deductions', 'incomeSources', 'Documents')->first(),
            'clientProfile' => auth()->user()->clientProfile->first(),
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
