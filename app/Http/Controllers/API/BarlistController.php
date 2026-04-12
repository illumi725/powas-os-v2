<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;

class BarlistController extends BaseController
{
    public function index(): JsonResponse
    {
        $filePath = storage_path('app/bar_list.json');

        if (!file_exists($filePath)) {
            return $this->sendError('Not Found', ['error' => 'Barlist file not found!']);
        }

        $jsonData = file_get_contents($filePath);

        $data = json_decode($jsonData, true);

        return $this->sendResponse($data, 'Barlist retrieved successfully!');
    }
}
