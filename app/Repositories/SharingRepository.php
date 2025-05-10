<?php

namespace App\Repositories;

use App\Interfaces\SharingRepositoryInterface;
use App\Models\ShareLink;
use Illuminate\Support\Facades\DB;

class SharingRepository implements SharingRepositoryInterface
{
    public function getReport($uuid){
        try{
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
            return $personData;
        }catch(\Exception $e){
            return response()->json(['status' => false, 'message' => "Error: ".$e->getMessage()], 500);
        }
    }
    public function getSharedLink($request){
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

        return response()->json(['status' => true, 'data' => $sharedLink, 'url'=>url($sharedLink->id)]);
    }
    public function removeSharedLink($request){
        if(!$request->id) return response()->json(['status' => false, 'message'=>"invalid Data"],404);
        $sharedLink = ShareLink::find($request->id);
        if(!$sharedLink) return response()->json(['status' => false, 'message'=>"Data note Found"],404);
        $sharedLink->delete();
        return response()->json(["status"=> true, "data"=> "Deleted Successfully"]);
    }
}
