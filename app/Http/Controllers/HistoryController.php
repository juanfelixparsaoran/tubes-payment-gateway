<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    //
    function read(){
        $history = DB::table('history')->get();
        return response()->json([
            'data' => $history
        ]);
    }

    function edit($id){
        $history = DB::table('history')->where('id',$id)->get();
        if (!$history->isEmpty()){
            return response()->json([
                'message' => 'history found',
                'data' => $history,
            ]);
        }else{
            return response()->json([
                'message' => 'history not found',
                'data' => []
            ]);
        }
    }
    function update(Request $request){
        $history = DB::table('history')->where('id',$request->history_id)->get();
        DB::table('history')->where('id',$request->history_id)->update([
            'date' => $request->date != NULL ? $request->date : $history[0]->date,
            'nominal' => $request->nominal != NULL ? $request->nominal : $history[0]->nominal,
            'account_id' => $request->account_id != NULL ? $request->account_id : $history[0]->account_id,
        ]);
        return response()->json([
            'message' => 'history Updated'
        ]);
    }
    function delete($id){
        DB::table('history')->where('id',$id)->delete();
        return response()->json([
            'message' => 'history Deleted'
        ]);
    }

    function create(Request $request){
        DB::table('history')->insert([
            'date' => $request->date,
            'nominal' => $request->nominal,
            'account_id' => $request->account_id
        ]);
        return response()->json([
            'message' => 'history Created'
        ]);
    }
}
