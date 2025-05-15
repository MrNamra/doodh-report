<?php

namespace App\Repositories;

use App\Interfaces\AccoutingRepositoryInterface;
use App\Models\Accounting;
use App\Models\LogsModel;
use App\Models\Trangactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingRepository implements AccoutingRepositoryInterface
{
    public function addMoney($request){
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
        return response()->json([
            "status"=> true,
            "message" => "trangcation Successful!"
        ]);
    }
    public function updateAccounting($request){
        $trangaction = Trangactions::findOrFail($request->id);
        $logData = $trangaction->logs;
        $accountData = Accounting::findOrFail($logData->preson_id);

        $newTrangactions = Trangactions::where(['user_id' => Auth::id(), 'preson_id' => $trangaction->preson_id])->where('id', '>=', $request->id)->get();

        if($logData->ammount > $request->money){
            $effected_money = $logData->ammount - $request->money;
            $accountData->money = $accountData->money - $effected_money;
            $logData->ammount = $logData->ammount - $effected_money;
            $accountData->save();
            $logData->save();
            
            $trangaction->price = $trangaction->price - $effected_money;
            $trangaction->total = $trangaction->total - $effected_money;
            $trangaction->subTotal = $trangaction->subTotal - $effected_money;
            $trangaction->save();
            
            foreach($newTrangactions as $trang){
                $trang->subTotal = ($trang->subTotal - $effected_money);
                $trang->save();
            }

        } else if ($logData->ammount < $request->money){
            $effected_money = $request->money - $logData->ammount;
            $accountData->money = $accountData->money + $effected_money;
            $logData->ammount = $logData->ammount + $effected_money;
            $accountData->save();
            $logData->save();

            $trangaction->price = $trangaction->price + $effected_money;
            $trangaction->total = $trangaction->total + $effected_money;
            $trangaction->subTotal = $trangaction->subTotal + $effected_money;
            $trangaction->save();

            foreach($newTrangactions as $trang){
                $trang->subTotal = ($trang->subTotal + $effected_money);
                $trang->save();
            }
        }

        return true;
    }
    public function addTrangcation($request){

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

            return response()->json(['status' => true, 'message' => 'Trangaction Added!']);
    }
    public function removeAccout($request){
        Accounting::find($request->id)->delete();
        return response()->json(["status"=> true,"message"=> "User Data removed successfull!"],200);
    }
}
