<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditCardController extends Controller
{
    //
    function create(Request $request){
        DB::table('credit_card')->insert([
            'number' => $request->cc_number,
            'bank' => $request->bank,
            'expiry_date' => $request->expiry_date
        ]);
        return response()->json([
            'message' => 'Credit Card Created'
        ]);
    }

    function read(){
        $credit_card = DB::table('credit_card')->get();
        return response()->json([
            'data' => $credit_card
        ]);
    }
}
