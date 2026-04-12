<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\MembersResource;
use App\Models\Powas;
use App\Models\PowasMembers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembersController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $powasID = $request->query('powas-id');

        $powas = Powas::find($powasID);

        if (is_null($powas)) {
            return $this->sendError('Not Found', ['error' => 'POWAS not found!']);
        }

        $membersQuery = PowasMembers::with(['applicationinfo']);

        if ($powasID !== null) {
            $membersQuery->whereHas('applicationinfo', function($query) use ($powasID) {
                $query->where('powas_id', $powasID);
            });
        }

        $members = $membersQuery->get();

        return $this->sendResponse(MembersResource::collection($members), 'Members list retrieved successfully!');
    }
}
