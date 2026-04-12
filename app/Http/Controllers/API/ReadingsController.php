<?php

namespace App\Http\Controllers\API;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Http\Resources\ReadingsResource;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\Readings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReadingsController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $powasID = $request->query('powas-id');
        $perPage = $request->query('per-page', 10);
        $powas = Powas::find($powasID);

        if (is_null($powas)) {
            return $this->sendError('Not Found', ['error' => 'POWAS not found!']);
        }

        $readingsQuery = Readings::where('readings.powas_id', $powasID)
        ->join('powas_members', 'readings.member_id', '=', 'powas_members.member_id')
        ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
        ->orderBy('powas_applications.lastname', 'asc');

        $readings = $readingsQuery->paginate($perPage);

        return $this->sendResponse(ReadingsResource::collection($readings), 'Readings list retrieved successfully!');
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $powasID = $request->query('powas-id');
        $memberID = $request->query('member-id');

        $input['member_id'] = $memberID;
        $input['powas_id'] = $powasID;

        $validator = Validator::make($input, [
            'member_id' => ['required'],
            'powas_id' => ['required'],
            'reading' => ['required', 'numeric'],
            'reading_date' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $lastReadingCount = Readings::where('member_id', $memberID)->orderBy('reading_count', 'desc')->first()->reading_count;

        $selectedMember = PowasMembers::find($memberID);

        $memberName = $selectedMember->applicationinfo->lastname . ', ' . $selectedMember->applicationinfo->firstname;

        $readingID = CustomNumberFactory::getRandomID();

        $input['reading_id'] = $readingID;
        $input['reading_count'] = $lastReadingCount + 1;
        $input['recorded_by'] = Auth::user()->user_id;

        $reading = Readings::create($input);

        $logMessage = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created reading record for <b><i>' . strtoupper($memberName) . '</i></b>.';

        ActionLogger::dispatch('create', $logMessage, Auth::user()->user_id, 'reading', $powasID);

        return $this->sendResponse(new ReadingsResource($reading), 'New reading successfully added for ' . $memberID . '!');
    }

    public function show($readingID): JsonResponse
    {
        $reading = Readings::find($readingID);

        if (is_null($reading)) {
            return $this->sendError('Not Found', ['error' => 'Reading ID ' . $readingID . ' not found!']);
        }

        return $this->sendResponse(new ReadingsResource($reading), "Reading ID " . $readingID . " retrieved successfully");
    }

    public function update(Request $request, $readingID): JsonResponse
    {
        $input = $request->all();
        $reading = Readings::with(['memberreading.applicationinfo'])->find($readingID);

        $existingValues = [
            'reading' => floatval($reading->reading),
        ];

        $isChanged = false;

        if ($existingValues['reading'] !== floatval($input['reading'])) {
            $isChanged = true;
        }

        $rules = [
            'reading' => 'required',
        ];

        if (!$isChanged) {
            return $this->sendResponse([], 'No changes made in reading with ID: ' . $readingID);
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input['updated_by'] = Auth::user()->user_id;

        $reading['reading'] = $input['reading'];
        $reading['updated_by'] = $input['updated_by'];

        $reading->save();

        $logMessage = "<b><u>";
        $logMessage .= Auth::user()->userinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= Auth::user()->userinfo->firstname;
        $logMessage .= "</u></b> updated reading record for <b><i>";
        $logMessage .= $reading->memberreading->applicationinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= $reading->memberreading->applicationinfo->firstname;
        $logMessage .= "</i></b> with ID ";
        $logMessage .= $readingID;
        $logMessage .= " from <i><b>";
        $logMessage .= $existingValues['reading'];
        $logMessage .= "</i></b> to  <i><b>";
        $logMessage .= $input['reading'];
        $logMessage .= "</i></b>.";

        ActionLogger::dispatch('update', $logMessage, Auth::user()->user_id, 'reading', $reading->powas_id);

        return $this->sendResponse(new ReadingsResource($reading), 'Reading with ID ' . $readingID . ' updated successfully!');
    }

    public function destroy($readingID): JsonResponse
    {
        $reading = Readings::withTrashed()->find($readingID);

        if ($reading && $reading->trashed()) {
            return $this->sendError('Not Found', ['error' => 'Reading ID ' . $readingID . ' not found!']);
        }

        $currentTimestamp = date('Y-m-d H:i:s');

        $reading->deleted_at = $currentTimestamp;
        $reading->updated_by = Auth::user()->user_id;
        $reading->save();

        $logMessage = "<b><u>";
        $logMessage .= Auth::user()->userinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= Auth::user()->userinfo->firstname;
        $logMessage .= "</u></b> deleted reading record for <b><i>";
        $logMessage .= $reading->memberreading->applicationinfo->lastname;
        $logMessage .= ", ";
        $logMessage .= $reading->memberreading->applicationinfo->firstname;
        $logMessage .= "</i></b> with ID </u></b>";
        $logMessage .= $readingID;
        $logMessage .= "</i></b>.";

        ActionLogger::dispatch('delete', $logMessage, Auth::user()->user_id, 'reading', $reading->powas_id);

        return $this->sendResponse([], 'Reading with ID ' . $readingID . ' deleted successfully!');
    }
}
