<?php

namespace App\Http\Controllers;

use App\Interfaces\SharingRepositoryInterface;
use App\Models\Accounting;
use App\Models\ShareLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SharingController extends Controller
{
    private $sharingRepo;
    public function __construct(SharingRepositoryInterface $sharingRepo){
        $this->sharingRepo = $sharingRepo;
    }
    public function index(){
        $persons = Accounting::where('user_id', Auth::id())->get();
        return view("shering", compact("persons"));
    }
    public function apiIndex(){
        $persons = Accounting::where('user_id', Auth::id())->get();
        return response()->json(['persons' => $persons]);
    }
    public function getReport(Request $request, $uuid){
        $personData = $this->sharingRepo->getReport($uuid);
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

        return $this->sharingRepo->getSharedLink($request);
    }
    public function removeSharedLink(Request $request){
        return $this->sharingRepo->removeSharedLink($request);
    }
}
