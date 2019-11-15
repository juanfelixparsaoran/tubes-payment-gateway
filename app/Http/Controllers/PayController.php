<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayController extends Controller
{

    function pay(Request $request){
        $account = DB::table('account')->where('secret_key',$request->secret_key)->get();
        if ($account->isEmpty()){
            return response()->json([
                'message' => 'Secret key is incorrect'
            ]);
        }else{
            if ($request->method == "Bank Transfer"){
                $vn = rand(100000000,999999999);
                $pay_req = DB::table('pay_request')->where('virtual_number',$vn)->get();
                while (!$pay_req->isEmpty()){
                    $vn = rand(1000000000,999999999);
                    $pay_req = DB::table('pay_request')->where('virtual_number',$vn)->get();
                }
                DB::table('pay_request')->insert([
                    'virtual_number' => $vn,
                    'bank' => $request->bank,
                    'amount' => $request->amount,
                ]);
                DB::table('history')->insert([
                    'account_id' => $account[0]->id,
                    'date' => now(),
                    'status' => 'Pending',
                    'nominal' => $request->amount,
                    'method' => $request->method,
                    'number' => $vn
                ]);
                return response()->json([
                    'message' => 'Virtual Number Created',
                    'virtual_number' => $vn,
                    'amount' => $request->amount
                ]);
            }else{
                $credit_card = DB::table('credit_card')->where('number',$request->cc_number)->get();
                if (!$credit_card->isEmpty()){
                    if ($credit_card[0]->expiry_date > now()){
                        DB::table('history')->insert([
                            'account_id' => $account[0]->id,
                            'date' => now(),
                            'status' => 'Success',
                            'nominal' => $request->amount,
                            'method' => $request->method,
                            'number' => $request->cc_number
                        ]);
                        return response()->json([
                            'message' => 'Pembayaran berhasil',
                        ]);
                    }else{
                        DB::table('history')->insert([
                            'account_id' => $account[0]->id,
                            'date' => now(),
                            'status' => 'Failed',
                            'nominal' => $request->amount,
                            'method' => $request->method,
                            'number' => $request->cc_number
                        ]);
                        return response()->json([
                            'message' => 'Kartu kredit telah expire. Pembayaran gagal',
                        ],400);
                    }
                }else{
                    DB::table('history')->insert([
                        'account_id' => $account[0]->id,
                        'date' => now(),
                        'status' => 'Failed',
                        'nominal' => $request->amount,
                        'method' => $request->method,
                        'number' => $request->cc_number
                    ]);
                    return response()->json([
                        'message' => 'Kartu kredit tidak ditemukan',
                    ],404);
                }
            }
        }
    }

    function payVirtualNumber(Request $request){
        $pay_req = DB::table('pay_request')->where('virtual_number',$request->virtual_number)->get();
        if(!$pay_req->isEmpty()){
            if (($pay_req[0]->amount == $request->amount) && ($pay_req[0]->bank == $request->bank)){
                $pay_req = DB::table('pay_request')->where('virtual_number',$request->virtual_number)->delete();
                DB::table('history')->where('number',$request->virtual_number)->update([
                    'status' => 'Success',
                ]);
                return response()->json([
                    'message' => 'Pembayaran berhasil',
                ]);
            }else{
                $pay_req = DB::table('pay_request')->where('virtual_number',$request->virtual_number)->delete();
                DB::table('history')->where('number',$request->virtual_number)->update([
                    'status' => 'Failed',
                ]);
                return response()->json([
                    'message' => 'Pembayaran gagal',
                ]);
            }
        }else{
            return response()->json([
                'message' => 'Virtual number tidak ditemukan',
            ]);
        }
    }
}
