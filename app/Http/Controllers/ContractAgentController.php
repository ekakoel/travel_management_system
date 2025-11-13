<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContractAgent;

class ContractAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }
    public function index()
    {   
        $contract_agent = ContractAgent::where('status','Active')->get();
        return view('contract.contract-agent',compact('contract_agent'),[

        ]);
    }
}
