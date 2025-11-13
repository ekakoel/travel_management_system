<?php

namespace App\Http\Controllers;

use App\Models\Attention;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAttentionRequest;
use App\Http\Requests\UpdateAttentionRequest;

class AttentionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function attentions(Request $request){
        $attentions = Attention::select('id', 'page', 'name', 'attention_zh', 'attention_en')
                            ->get(); // Pagination agar lebih ringan

    return view('admin.attentions.attentions', compact('attentions'));
    }

    // FUNCTION ADD ORDER WEDDING INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_update_attention(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'page' => 'required|string|max:255',
            'attention_en' => 'required|string',
            'attention_zh' => 'required|string',
        ]);
    
        $attention = Attention::find($id);
        if (!$attention) {
            return redirect()->back()->with('error', 'Attention not found!');
        }
    
        // Debugging untuk cek data masuk
        
        $attention->update([
            "name" => $request->name,
            "page" => $request->page,
            "attention_zh" => $request->attention_zh,
            "attention_en" => $request->attention_en,
        ]);
        // dd($request->all(), $attention);
        return redirect("/attentions")->with('success', 'Attention has been updated!');
    }
    
    // FUNCTION ADD ORDER WEDDING INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_delete_attention(Request $request,$id){
        $attention = Attention::find($id);
        $attention->delete();
        return redirect("/attentions")->with('success','Attention has been deleted!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'page' => 'required|string|max:255',
            'attention_en' => 'required|string',
            'attention_zh' => 'required|string',
        ]);

        Attention::create([
            'name' => $request->name,
            'page' => $request->page,
            'attention_en' => $request->attention_en,
            'attention_zh' => $request->attention_zh,
        ]);

        return redirect()->back()->with('success', 'Attention added successfully!');
    }
    
}
