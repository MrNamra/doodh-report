<?php

namespace App\Http\Controllers;

use App\Interfaces\AccoutingRepositoryInterface;
use App\Models\Accounting;
use App\Models\LogsModel;
use App\Models\Trangactions;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccoutingController extends Controller
{
    private $accountRepo;
    public function __construct(AccoutingRepositoryInterface $accountRepo){
        $this->accountRepo = $accountRepo;
    }
    public function index(Request $request){
        $accouting = Accounting::where('user_id', Auth::user()->id)->get();
        if($request->id){
            $accouting->logsData = Trangactions::findOrFail($request->id);
        }
        return view("account", ['users' => $accouting]);
    }
    public function apiIndex(Request $request){
        $accouting = Accounting::where('user_id', Auth::user()->id)->get();
        if($request->id){
            $accouting->logsData = Trangactions::findOrFail($request->id);
        }
        return $accouting;
    }
    public function addMoney(Request $request){
        try{
            $vadtior = Validator::make($request->all(),[
                "money" => "required|numeric",
                "note" => "nullable"
            ]);
            if($vadtior->fails()){
                return response()->json([
                    "status" => false,
                    "error"=> $vadtior->errors(),
                ], 401);
            }

            DB::beginTransaction();
            if($request->id){
                $this->accountRepo->updateAccounting($request);
                DB::commit();
                return response()->json(['status'=>true, 'message' => 'update Successful!'], 200);
            }
            $data = $this->accountRepo->addMoney($request);
            DB::commit();
            return $data;

        } catch(Exception $e){
            DB::rollBack();
            Log::info("AccoutingController/addMoney: ". $e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "Error: ".$e->getMessage()
            ],500);
        }
    }

    public function addTrangcation(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                "date" => "nullable|date",
                "preson_id" => "nullable|numeric",
                "name" => "required|string",
                "qty" => "required|numeric|min:1",
                "price" => "required|numeric|min:1",
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "status"=> false,
                    "errors" => $validator->errors(),
                ], 401);
            }

            return $this->accountRepo->addTrangcation($request);
        } catch(Exception $e){
            Log::info("AccoutingController/addTrangcation: ". $e->getMessage());
            return response()->json(["status"=> false,"message"=> "Error: " . $e->getMessage()],500);
        }
    }

    public function removeAccout(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                "id" => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "status"=> false,
                    "errors" => $validator->errors(),
                ], 401);
            }

            return $this->accountRepo->removeAccout($request);

        } catch(Exception $e){
            Log::info("AccoutingController/addTrangcation: ". $e->getMessage());
            return response()->json(["status"=> false,"message"=> "Error: " . $e->getMessage()],500);
        }
    }
}
