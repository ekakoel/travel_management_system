<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'location' => 'required',
            'bank' => 'required',
            'name' => 'required',
        ]);
        $bankAccount = new BankAccount ([
            "bank"=>$request->bank,
            "currency"=>$request->currency,
            "account_name"=>$request->account_name,
            "account_number"=>$request->account_number,
            "location"=>$request->location,
            "address"=>$request->address,
            "telephone"=>$request->telephone,
            "swift_code"=>$request->swift_code,
            "bank_code"=>$request->bank_code,
        ]);
        //@dd($bankAccount);
        $bankAccount->save();
        return redirect()->back()->with('success','Bank Account has been added');
        return redirect()->back()->with('error','Bank Account cannot be added');
    }

    public function func_update_bank_account(Request $request,$id)
    {
        $bankaccount=BankAccount::findOrFail($id);
        
        $bankaccount->update([
            "bank"=>$request->bank,
            "currency"=>$request->currency,
            "account_name"=>$request->account_name,
            "account_number"=>$request->account_number,
            "location"=>$request->location,
            "address"=>$request->address,
            "telephone"=>$request->telephone,
            "swift_code"=>$request->swift_code,
            "bank_code"=>$request->bank_code,
        ]);
        return redirect()->back()->with('success','Bank Account has been updated');
        return redirect()->back()->with('error','Bank Account cannot be update');
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
}
