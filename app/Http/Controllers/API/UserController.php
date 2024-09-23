<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user(Request $request)
    {

        $id = auth()->user()->id;
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function users()
    {
        $users = User::all();

        // return new UserCollection($users);
        return response([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users)
        ]);
    }
}
