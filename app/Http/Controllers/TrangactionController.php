<?php

namespace App\Http\Controllers;

use App\Interfaces\TransactionRepositoryInterface;
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
use Yajra\DataTables\DataTables;

class TrangactionController extends Controller
{
    private $trangactionRepo;
    public function __construct(TransactionRepositoryInterface $trangactionRepo){
        $this->trangactionRepo = $trangactionRepo;
    }
    public function index(Request $request){
        $data = null;
        $oneData = null;
        if($request->id){
            $oneData = $this->trangactionRepo->index($request->id);
        }
        $data = Accounting::where('user_id', Auth::user()->id)->get();
        return view("trangcation", ['users'=> $data, 'data'=>$oneData]);
    }
    public function apiIndex(Request $request){
        $data = null;
        $oneData = null;
        if($request->id){
            $oneData = $this->trangactionRepo->index($request->id);
        }
        $data = Accounting::where('user_id', Auth::user()->id)->get();
        return response()->json(['users'=> $data, 'data'=>$oneData]);
    }
    public function addTrangaction(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'preson_id' => 'required|numeric',
                'name' => 'required|string|max:250',
                'qty' => 'required|numeric|min:1',
                'price' => 'required|numeric|min:1'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            if($request->id){
                $this->trangactionRepo->updateTrangaction($request);
                DB::commit();
                return response()->json(['status' => true, 'message' => "Update SuccesFull!"], 200);
            }

            $data = $this->trangactionRepo->addTrangaction($request);
            DB::commit();
            return $data;            

        }catch(Exception $e){
            DB::rollBack();
            Log::info("TrangactionController/addTrangaction".$e->getMessage());
            return response()->json(
                [
                    "status"=> false,
                    "message"=> "Error: ".$e->getMessage()
                ]
            );
        }
    }
    public function list(Request $request){
        try{
            if($request->ajax()){
                return $this->trangactionRepo->list($request);
            }
            $users = Accounting::where('user_id', Auth::user()->id)->get();
            return view('reportList', ['users'=> $users]);

        }catch(Exception $e){
            Log::info("TrangactionController/addTrangaction".$e->getMessage());
            return response()->json(
                [
                    "status"=> false,
                    "message"=> "Error: ".$e->getMessage()
                ]
            );
        }
    }
    public function apiList(Request $request){
        try{
            $data = $this->trangactionRepo->list($request);
            $users = Accounting::where('user_id', Auth::user()->id)->get();
            return response()->json(['users'=> $users, 'data'=>$data]);

        }catch(Exception $e){
            Log::info("TrangactionController/addTrangaction".$e->getMessage());
            return response()->json(
                [
                    "status"=> false,
                    "message"=> "Error: ".$e->getMessage()
                ]
            );
        }
    }
    public function removeTrangaction(Request $request){
        try{
            return $this->trangactionRepo->removeTrangaction($request);
        } catch(Exception $e){
            Log::info("TrangactionController/removeTrangaction".$e->getMessage());
            return response()->json(["status"=> false,"message"=> "Error: ".$e->getMessage()], 500);
        }
    }
}
