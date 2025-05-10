<?php

namespace App\Http\Controllers;

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
    public function index(Request $request){
        $accouting = Accounting::where('user_id', Auth::user()->id)->get();
        if($request->id){
            $accouting->logsData = Trangactions::findOrFail($request->id);
        }
        return view("account", ['users' => $accouting]);
    }
    public function addMoney(Request $request){
        try{
            $vadtior = Validator::make($request->all(),[
                // "person_id" => "required_without:person_name|string",
                // "preson_name" => "required_without:person_id|string",
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
                $this->updateAccounting($request);
                return response()->json(['status'=>true, 'message' => 'update Successful!'], 200);
            }

            $person_id = $request->person_id;
            $accounting =null;
            if($request->person_id){
                $accounting = Accounting::find($person_id);
            } else {
                $accounting = new Accounting();
                $accounting->preson_name = $request->preson_name;
                $accounting->user_id = Auth::user()->id;
            }
            $accounting->money = ($request->person_id == null) ? $request->money : $request->money + $accounting->money;
            $accounting->save();

            $trangactin = Trangactions::create([
                'user_id' => Auth::user()->id,
                'preson_id' => $accounting->id,
                'price' => $request->money,
                'total' => $request->money,
                'subTotal' => $accounting->money,
                'date' => Carbon::parse($request->date)->format('Y-m-d H:i:s')
            ]);

            LogsModel::create([
                "trangaction_id" => $trangactin->id,
                "preson_id" => $accounting->id,
                "trangcation" => 'credit',
                "ammount" => $request->money,
                "note" => "Ammout Credited\n".$request->note,
            ]);
            DB::commit();
            return response()->json([
                "status"=> true,
                "message" => "trangcation Successful!"
            ]);

        } catch(Exception $e){
            DB::rollBack();
            Log::info("AccoutingController/addMoney: ". $e->getMessage());
            return response()->json([
                "status" => false,
                "message" => "Error: ".$e->getMessage()
            ],500);
        }
    }

    private function updateAccounting(Request $request){
        $trangaction = Trangactions::findOrFail($request->id);
        $logData = $trangaction->logs;
        $accountData = Accounting::findOrFail($logData->preson_id);

        $newTrangactions = Trangactions::where(['user_id' => Auth::id(), 'preson_id' => $trangaction->preson_id])->where('id', '>=', $request->id)->get();

        if($logData->trangcation == 'credit'){
            $accountData->money = $accountData->money - $logData->ammount;
            $logData->ammount = ($accountData->money - $logData->ammount) + $request->money;

            foreach($newTrangactions as $trang){
                $trang->price = $request->money;
                $trang->total = $request->money;
                $trang->subTotal = ($trang->subTotal - $trang->price) + $request->money;
                $trang->save();
            }

        } else {
            $accountData->money = $accountData->money + $logData->ammount;
            $logData->ammount = ($accountData->money + $logData->ammount) - $request->money;

            foreach($newTrangactions as $trang){
                $trang->name = $request->name;
                $trang->qty = $request->qty;
                $trang->price = $request->price;
                $trang->total = ($request->price * $request->qty);
                $trang->subTotla = ($trang->subTotal + $trang->price) - $request->money;
                $trang->save();
            }
        }
        $accountData->save();
        $logData->save();

        return true;
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

            $total = $request->qty * $request->price;

            Trangactions::create([
                "user_id"=> Auth::user()->id,
                "preson_id" => ($request->preson_id != null) ? $request->preson_id : Auth::user()->id,
                "date" => Carbon::parse($request->date)->format('Y-m-d H:i:s'),
                "name" => $request->name,
                "qty" => $request->qty,
                "price" => $request->price,
                "total" => $total
            ]);

            LogsModel::create([
                "user_id" => $request->user_id,
                "preson_id" => ($request->preson_id != null) ? $request->preson_id : Auth::user()->id,
                "trangcation" => 'debit',
                "money" => $total
            ]);

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

            Accounting::find($request->id)->delete();
            response()->json(["status"=> true,"message"=> "User Data removed successfull!"],200);

        } catch(Exception $e){
            Log::info("AccoutingController/addTrangcation: ". $e->getMessage());
            return response()->json(["status"=> false,"message"=> "Error: " . $e->getMessage()],500);
        }
    }
}
