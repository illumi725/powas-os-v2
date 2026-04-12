<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillingsResource;
use App\Models\Billings;
use App\Models\Powas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingsController extends BaseController
{
    // public function unpaidBills($powasID = '') {
    //     $data = 0;

    //     if ($powasID == '') {
    //         $data = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
    //             ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
    //             ->where('billings.bill_status', 'UNPAID')
    //             ->orderBy('powas_applications.lastname', 'asc')
    //             ->orderBy('powas_applications.firstname', 'asc')
    //             ->orderBy('powas_applications.middlename', 'asc')
    //             ->get();
    //     } else {
    //         $data = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
    //             ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
    //             ->where('billings.bill_status', 'UNPAID')
    //             ->where('billings.powas_id', $powasID)
    //             ->orderBy('powas_applications.lastname', 'asc')
    //             ->orderBy('powas_applications.firstname', 'asc')
    //             ->orderBy('powas_applications.middlename', 'asc')
    //             ->get();
    //     }

    //     return response()->json($data);
    // }

    public function index(Request $request): JsonResponse
    {
        $powasID = $request->query('powas-id');
        $perPage = $request->query('per-page', 10);
        $powas = Powas::find($powasID);

        if (is_null($powas)) {
            return $this->sendError('Not Found', ['error' => 'POWAS not found!']);
        }

        $billingsQuery = Billings::where('billings.powas_id', $powasID)
        ->join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
        ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
        ->orderBy('powas_applications.lastname', 'asc');

        $billings = $billingsQuery->paginate($perPage);

        return $this->sendResponse(BillingsResource::collection($billings), 'Billings list retrieved successfully!');
    }
}
