<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }
    
    public function index()
    {
        //
    }

    // ADD BANK ACCOUNT ==================================================================================================================================================================================
    public function func_add_bank_account(Request $request)
    {
        $validated = $this->validateBankAccount($request);

        $bankAccount = new BankAccount($this->bankAccountPayload($validated));

        $bankAccount->save();
        return redirect()->back()->with('success','Bank Account has been added');
    }

    public function func_update_bank_account(Request $request,$id)
    {
        $bankaccount=BankAccount::findOrFail($id);
        $validated = $this->validateBankAccount($request);
        
        $bankaccount->update($this->bankAccountPayload($validated));
        return redirect()->back()->with('success','Bank Account has been updated');
    }

    // Function Delete REMARK =============================================================================================================>
    public function destroy_bank_account(Request $request, $id)
    {
            $bankaccount=BankAccount::findOrFail($id);
            $bankaccount->delete();
            return redirect()->back()->with('success','Bank account has been removed');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBankAccountRequest  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBankAccountRequest $request, BankAccount $bankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
    }

    private function validateBankAccount(Request $request): array
    {
        return $request->validate([
            'bank' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'in:IDR,USD,CNY,TWD'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:255'],
            'swift_code' => ['nullable', 'string', 'max:255'],
            'bank_code' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function bankAccountPayload(array $validated): array
    {
        $payload = [
            'bank' => $validated['bank'],
            'currency' => $validated['currency'],
            'location' => $validated['location'],
            'address' => $validated['address'] ?? null,
            'telephone' => $validated['telephone'] ?? null,
            'swift_code' => $validated['swift_code'] ?? null,
            'bank_code' => $validated['bank_code'] ?? null,
        ];

        if (Schema::hasColumn('bank_accounts', 'account_name')) {
            $payload['account_name'] = $validated['account_name'];
            $payload['account_number'] = $validated['account_number'];

            return $payload;
        }

        $payload['name'] = $validated['account_name'];
        $payload['account_idr'] = null;
        $payload['account_usd'] = null;
        $payload['account_cny'] = null;
        $payload['account_twd'] = null;
        $payload['account_' . strtolower($validated['currency'])] = $validated['account_number'];

        return $payload;
    }
}
