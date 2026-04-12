<?php

namespace App\Http\Controllers\API;

use App\Factory\CustomNumberFactory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /**
     * Register API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $input['user_id'] = CustomNumberFactory::getRandomID();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('powas-os-api')->plainTextToken;
        $success['name'] = $user->username;

        return $this->sendResponse($success, 'User successfully added!');
    }

    /**
     * Login API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $success['token'] = $user->createToken('powas-os-api')->plainTextToken;
            $success['name'] = $user->username;

            return $this->sendResponse($success, 'User successfully logged in!');
        } else {
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized']);
        }
    }
}
