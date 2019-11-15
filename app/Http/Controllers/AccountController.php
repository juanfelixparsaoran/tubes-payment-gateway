<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    //
    function read(){
        $account = DB::table('account')->get();
        return response()->json([
            'data' => $account
        ]);
    }

    function edit($id){
        $account = DB::table('account')->where('account_id',$id)->get();
        if (!$account->isEmpty()){
            return response()->json([
                'message' => 'account found',
                'data' => $account,
            ]);
        }else{
            return response()->json([
                'message' => 'account not found',
                'data' => []
            ]);
        }
    }
    function update(Request $request){
        $account = DB::table('account')->where('id',$request->account_id)->get();
        DB::table('account')->where('id',$request->account_id)->update([
            'email' => $request->email != NULL ? $request->email : $account[0]->email,
            'password' => $request->password != NULL ? $request->password : $account[0]->password,
            'status' => $request->status != NULL ? $request->status : $account[0]->status,
        ]);
        return response()->json([
            'message' => 'account Updated'
        ]);
    }
    function delete($id){
        DB::table('account')->where('id',$id)->delete();
        return response()->json([
            'message' => 'account Deleted'
        ]);
    }

    function create(Request $request){
        $sk = rand(100000000,999999999);
        $account = DB::table('account')->where('secret_key',$sk)->get();
        while (!$account->isEmpty()){
            $vn = rand(1000000000,999999999);
            $account = DB::table('account')->where('secret_key',$sk)->get();
        }
        DB::table('account')->insert([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => 'Active',
            'secret_key' => $sk,
        ]);
        return response()->json([
            'message' => 'Account Created',
            'secret_key' => $sk,
        ]);
    }

    function create1($email,$password){
        $sk = rand(100000000,999999999);
        $account = DB::table('account')->where('secret_key',$sk)->get();
        while (!$account->isEmpty()){
            $vn = rand(1000000000,999999999);
            $account = DB::table('account')->where('secret_key',$sk)->get();
        }
        DB::table('account')->insert([
            'email' => $email,
            'password' => bcrypt($password),
            'status' => 'Active',
            'secret_key' => $sk,
        ]);
        return response()->json([
            'message' => 'Account Created',
            'secret_key' => $sk,
        ]);
    }

    function auth(Request $request){
        $login = false;
        $accounts = DB::table('account')->get();
        foreach ($accounts as $account)
        {
            if ($account->email == $request->email && (Hash::check($request->password, $account->password)))
            {
                $request->session()->put('hash',$account->password);
                $account_logged = $account;
                $login = true;
                break;
            }
        }
        if ($login){
            return response()->json([
                'message' => 'Successfully Login',
                'data_account' => $account_logged,
            ]);
        }else{
            return response()->json([
                'message' => 'Failed to Login',
                'data_account' => [],
            ]);
        }
    }
}
