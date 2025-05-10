<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\ShareLink;
use App\Models\Trangactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;

class SharingController extends Controller
{
    public function index(){
        $persons = Accounting::where('user_id', Auth::id())->get();
        return view("shering", compact("persons"));
    }
    public function getReport(FacadesRequest $request, $uuid){
        $report = ShareLink::findOrFail($uuid);
        $personData = DB::table('trangactions')
        ->leftJoin('logs', 'logs.trangaction_id', '=', 'trangactions.id')
        ->where('trangactions.preson_id', $report->preson_id)
        ->whereMonth('trangactions.date', $report->month)
        ->whereYear('trangactions.date', $report->year)
        ->select(
            'trangactions.name',
            'trangactions.date',
            'trangactions.qty',
            'trangactions.price',
            'trangactions.total',
            'trangactions.subTotal',
            'logs.trangcation as trangaction_type'
        )->orderBy('date', 'asc')->get();
        return view("shered",compact('personData'));
    }
    public function getSharedLink(Request $request){
        $validator = Validator::make($request->all(), [
            "person_id"=> "required|exists:accounting,id",
            "date"=> "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $id = $request->person_id;
        
        $date = explode('-', $request->date);
        if (!preg_match('/^(0[1-9]|1[0-2])-\d{4}$/', $request->date)) {
            return response()->json([
                'status' => false,
                'message' => 'Date is invalid, It must be in MM-YYYY format'
            ]);
        }
        
        $sharedLink = ShareLink::where(['month' => $date[0], 'year' => $date[1]])->where('preson_id', $id)->first();

        if(!$sharedLink){
            $sharedLink = ShareLink::create([
                'preson_id' => $request->person_id,
                'month' => $date[0],
                'year' => $date[1],
            ]);
        }
        return response()->json(['status' => true, 'data' => $sharedLink]);
    }
    public function removeSharedLink(Request $request){
        if(!$request->id) return response()->json(['status' => false, 'message'=>"invalid Data"],404);
        $sharedLink = ShareLink::find($request->id);
        if(!$sharedLink) return response()->json(['status' => false, 'message'=>"Data note Found"],404);
        $sharedLink->delete();
        return response()->json(["status"=> true, "data"=> "Deleted Successfully"]);
    }
}
