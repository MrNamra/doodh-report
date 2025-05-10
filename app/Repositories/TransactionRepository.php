<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Accounting;
use App\Models\LogsModel;
use App\Models\Trangactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function index($id){
        return Trangactions::find($id);
    }
    public function addTrangaction($request){
        
            $total = $request->price * $request->qty;

            $account = Accounting::find($request->preson_id);
            $account->money = $account->money - $total;
            $account->save();

            $trangaction_details = new Trangactions();
            $trangaction_details->user_id = Auth::user()->id;
            $trangaction_details->preson_id = $request->preson_id;
            $trangaction_details->date = Carbon::parse($request->date)->format('y-m-d H:i:s');
            $trangaction_details->qty = $request->qty;
            $trangaction_details->name = $request->name;
            $trangaction_details->price = $request->price;
            $trangaction_details->total = $total;
            $trangaction_details->subTotal = $account->money;
            $trangaction_details->save();
            
            LogsModel::create([
                'trangaction_id' => $trangaction_details->id,
                'preson_id' => $request->preson_id,
                'trangcation' => 'debit',
                'ammount' => $total,
                'note' => "Ammount Debited\n".$request->note
            ]);
            return response()->json(["status"=> true,"message"=> "Data Added SuccessFull!"], 200);
    }
    public function updateTrangaction($request){
        $trangaction = Trangactions::findOrFail($request->id);
        $logData = $trangaction->logs;
        $accountData = Accounting::findOrFail($logData->preson_id);

        $total = $request->qty * $request->price;

        $allTrangactions = Trangactions::where(['user_id' => Auth::id(), 'preson_id' => $trangaction->preson_id])->where('id', '>', $request->id)->get();

        foreach($allTrangactions as $trang){
            if($trangaction->total < $total){
                $trang->subTotal = $trang->subTotal - ($total - $trangaction->total);
                $trang->save();
            } else if($trangaction->total > $total){
                $trang->subTotal = ($trangaction->total - $total) + $trang->subTotal;
                $trang->save();
            }
        }

        $trangaction->name = $request->name;
        $trangaction->qty = $request->qty;
        $trangaction->price = $request->price;
        $trangaction->total = $total;
        $trangaction->date = Carbon::parse($request->date)->format('y-m-d H:i:s');

        if($logData->trangcation == 'credit'){
            $trangaction->subTotal = ($accountData->money - $logData->ammount) + $request->price;
            $trangaction->save();
            $logData->ammount = $request->price;
            $accountData->money = ($accountData->money - $logData->ammount) + $total;
        } else {
            $accountData->money = ($accountData->money + $logData->ammount) - $total;
            $trangaction->subTotal = ($trangaction->subTotal + $logData->ammount) - $total;
            $trangaction->save();
            $logData->ammount = $total;
        }

        $accountData->save();
        $logData->save();

        return true;
    }
    public function list($request){
        // $logs = LogsModel::with('preson:id,preson_name')->select('id', 'preson_id', 'trangcation', 'ammount', 'note', 'created_at')->where('preson_id', $request->id)->get();
        $query = Trangactions::with('logs', 'person:id,preson_name')->select('id', 'preson_id', 'name', 'qty', 'price', 'total', 'subTotal', 'created_at')->where('preson_id', $request->id)->orderBy('id','desc');

        if ($request->has('month') && !empty($request->month)) {
            try {
                $date = \DateTime::createFromFormat('m-Y', $request->month);
                $query->whereMonth('date', $date->format('m'))
                        ->whereYear('date', $date->format('Y'));
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
            }
        }

        $logs = $query->orderBy('date', 'asc')->get();

        $logs = $logs->map(function($log) {
            $log->preson_name = $log->person?->preson_name;
            return $log;
        });
        $logs = $logs->map(function($log) {
            $log->note = $log->logs?->note;
            return $log;
        });

        return DataTables::of($logs)->addColumn('actions', function($log) {
            if($log->logs->trangcation=='credit'){
                return '<button class="edit btn btn-info" data-url="'.route("user.account", ["id" => $log->id]).'">Edit</button><button class="delete btn btn-danger" data-id="' . $log->id . '">Delete</button>';
            }else if($log->logs->trangcation=='debit'){
                return '<button class="edit btn btn-info" data-url="'.route("user.record", ["id" => $log->id]).'">Edit</button><button class="delete btn btn-danger" data-id="' . $log->id . '">Delete</button>';
            }
        })->rawColumns(['actions'])->make(true);
    }
    public function removeTrangaction($request){
        $id = $request->id;
        $trangaction = Trangactions::with('logs')->findOrFail($id);
        $user = Accounting::findOrFail($trangaction->preson_id);
        if($trangaction->logs->trangcation == 'credit'){
            // - jama
            $ammount = $trangaction->logs->ammount;
            $user->money = $user->money - $ammount;
        }else if($trangaction->logs->trangcation == 'debit'){
            // + bad thaya
            $ammount = $trangaction->logs->ammount;
            $user->money = $user->money + $ammount;
        }
        $user->save();
        $trangaction->delete();
        return response()->json(['status' => true,'message'=> 'Delete SuccessFull!'], 200);
    }
}
