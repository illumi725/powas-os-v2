<?php

namespace App\Http\Controllers\API;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Http\Resources\POWASResource;
use App\Models\Powas;
use App\Models\PowasSettings;
use App\Rules\UniquePOWAS;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class POWASController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $powas = Powas::all();

        return $this->sendResponse(POWASResource::collection($powas), 'POWAS list retrieved successfully!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'zone' => 'required',
            'phase' => ['required', new UniquePOWAS(
                region: $input['region'],
                province: $input['province'],
                municipality: $input['municipality'],
                barangay: $input['barangay'],
                phase: $input['phase']
            )],
            'inauguration_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $powasID = CustomNumberFactory::powasID($input['province'], $input['municipality'], $input['barangay']);

        $input['powas_id'] = $powasID;

        if ($input['status'] == null) {
            $input['status'] = 'ACTIVE';
        }

        $powas = Powas::create($input);

        $logMessage = "<b><u>";
        $logMessage .= Auth::user()->userinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= Auth::user()->userinfo->firstname;
        $logMessage .= "</u></b> created <b><i>";
        $logMessage .= $input['barangay'];
        $logMessage .= " POWAS ";
        $logMessage .= $input['phase'];
        $logMessage .= "</i></b> with ID ";
        $logMessage .= $powasID;
        $logMessage .= ".";

        ActionLogger::dispatch('create', $logMessage, Auth::user()->user_id, 'powas-coop', $powasID);

        $logMessage = "<b><u>";
        $logMessage .= Auth::user()->userinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= Auth::user()->userinfo->firstname;
        $logMessage .= "</u></b> created <b><i>";
        $logMessage .= $input['barangay'];
        $logMessage .= " POWAS ";
        $logMessage .= $input['phase'];
        $logMessage .= " Settings</i></b> with POWAS ID ";
        $logMessage .= $powasID;
        $logMessage .= ".";

        ActionLogger::dispatch('create', $logMessage, Auth::user()->user_id, 'powas-coop', $powasID);

        PowasSettings::create([
            'powas_id' => $powasID,
        ]);

        return $this->sendResponse(new POWASResource($powas), 'New POWAS successfully added!');
    }

    /**
     * Display the specified resource
     *
     * @param string $powasID
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($powasID): JsonResponse
    {
        $powas = Powas::find($powasID);

        if (is_null($powas)) {
            return $this->sendError('Not Found', ['error' => 'POWAS not found!']);
        }

        return $this->sendResponse(new POWASResource($powas), "POWAS retrieved successfully");
    }

    /**
     * Update the specified resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param string $powasID
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $powasID): JsonResponse
    {
        $input = $request->all();
        $powas = Powas::find($powasID);

        $existingValues = [
            'region' => $powas->region,
            'province' => $powas->province,
            'municipality' => $powas->municipality,
            'barangay' => $powas->barangay,
            'zone' => $powas->zone,
            'phase' => $powas->phase,
            'inauguration_date' => $powas->inauguration_date,
            'status' => $powas->status,
        ];

        $isChanged = false;

        foreach ($existingValues as $key => $value) {
            if (!array_key_exists($key, $input) || trim($value) !== trim($input[$key])) {
                $isChanged = true;
                break;
            }
        }

        $rules = [
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'zone' => 'required',
            'phase' => 'required',
            'inauguration_date' => 'nullable|date',
        ];

        if ($isChanged) {
            $rules['phase'] = ['required', new UniquePOWAS(
                region: $input['region'],
                province: $input['province'],
                municipality: $input['municipality'],
                barangay: $input['barangay'],
                phase: $input['phase']
            )];
        }else{
            return $this->sendResponse([], 'No changes made in POWAS with ID: ' . $powasID);
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $powasID = $powas->powas_id;

        $powas->region = $input['region'];
        $powas->province = $input['province'];
        $powas->municipality = $input['municipality'];
        $powas->barangay = $input['barangay'];
        $powas->zone = $input['zone'];
        $powas->phase = $input['phase'];
        $powas->inauguration_date = $input['inauguration_date'];
        $powas->status = $input['status'];

        $powas->save();

        $logMessage = "<b><u>";
        $logMessage .= Auth::user()->userinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= Auth::user()->userinfo->firstname;
        $logMessage .= "</u></b> updated <b><i>";
        $logMessage .= $input['barangay'];
        $logMessage .= " POWAS ";
        $logMessage .= $input['phase'];
        $logMessage .= "</i></b> with ID ";
        $logMessage .= $powasID;
        $logMessage .= ".";

        ActionLogger::dispatch('update', $logMessage, Auth::user()->user_id, 'powas-coop', $powasID);

        return $this->sendResponse(new POWASResource($powas), 'POWAS with ID ' . $powasID . ' updated successfully');
    }

    public function destroy($powasID)
    {
        $powas = Powas::withTrashed()->find($powasID);

        if ($powas && $powas->trashed()) {
            return $this->sendError('Not Found', ['error' => 'POWAS not found!']);
        }

        $currentTimestamp = date('Y-m-d H:i:s');

        // $powas->delete();
        $powas->deleted_at = $currentTimestamp;
        $powas->updated_by = Auth::user()->user_id;
        $powas->save();

        $logMessage = "<b><u>";
        $logMessage .= Auth::user()->userinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= Auth::user()->userinfo->firstname;
        $logMessage .= "</u></b> deleted <b><i>";
        $logMessage .= $powas->barangay;
        $logMessage .= " POWAS ";
        $logMessage .= $powas->phase;
        $logMessage .= "</i></b> with ID ";
        $logMessage .= $powasID;
        $logMessage .= ".";

        ActionLogger::dispatch('delete', $logMessage, Auth::user()->user_id, 'powas-coop', $powasID);

        return $this->sendResponse([], 'POWAS deleted successfully!');
    }
}
