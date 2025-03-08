<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @bodyParam email string required The email of the user. Example: m.sobuj.cse@gmail.com
     * @response {
        * "status": true,
        * "message": "Login Successfully.",
        * "role": "Field Force"
     * }
     */
    public function loginRequest(Request $request)
    {
        $credentials = $request->only('email');
        $validator = Validator::make($credentials, [
            'email' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all())], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        if ($user->status == 'Deactivated') {
            return response()->json(['status' => false, 'message' => 'Your account is deactivated!'], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login Request Successfully.',
            'role' => $user->role,
        ]);
    }

    /**
     * @bodyParam email string required The email of the user. Example: m.sobuj.cse@gmail.com
     * @bodyParam password string required The password of the user. Example: 12345678
     * @response {
        * "status": true,
        * "message": "Login Successfully.",
        * "token": "API Token",
        * "data": {
            * "id": 1,
            * "code": "A001",
            * "name": "User",
            * "mobile": "01712960833",
            * "email": "m.sobuj.cse@gmail.com",
            * "role": "Super Admin",
            * "status": "Active"
        * }
     * }
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all())], 401);
        }

        $user = User::select('id', 'code', 'name', 'mobile', 'email', 'role', 'status')->where('email', $request->email)->whereIn('role',['Seller','Reseller'])->first();

        if (empty($user)) {
            
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        if ($user->status == 'Deactivated') {
            return response()->json(['status' => false, 'message' => 'Your account is deactivated!'], 401);
        }

        if (Hash::check($request->pass_code, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Password does not match!'], 401);
        }

        $token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login Successfully.',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @authenticated
     * @response {
        * "success": true,
        * "message": "Successfully logged out."
     * }
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['success'=> true, 'message' => 'Successfully logged out.'], 200);
    }


    /**
     * @authenticated
     * @response {
        * "success": true,
        * "data": {
            * "id": 1,
            * "name": "Sudip Palash",
            * "mobile": "01711111111",
            * "email": "user@gmail.com",
            * "role": "Customer",
            * "service_point_id": null,
            * "store_id": null,
            * "status": "Active"
        * }
     * }
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    }

    /**
     * @authenticated
     * @bodyParam fcm_token string required
     * @response {
        * "status": true,
        * "message": "FCM Token Updated",
        * "data": null
     * }
     */
    public function userFcmUpdate(Request $request)
    {
        $user = $request->user();

        $parameters = $request->only('fcm_token');
        $validator = Validator::make($parameters, [
            'fcm_token'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> implode(", " , $validator->messages()->all())], 401);
        }

        $fcm = $user->update(['fcm_token' => $request->fcm_token]);

        if ($fcm) {
            return response()->json([
                'status' => true,
                'message' => 'FCM Token Updated',
            ], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'FCM Token Updating Failed.'], 200);
        }
    }


    /**
     * @bodyParam old_password string required
     * @bodyParam password string required
     * @bodyParam password_confirmation string required
     * @response {
        * "status": true,
        * "message": "Password updated successfully!"
     * }
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $credentials = $request->only('old_password', 'password', 'password_confirmation');
        $rules = [
            'old_password' => 'required',
            'password' => 'required|max:20|min:8|confirmed',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message'=> implode(", " , $validator->messages()->all())], 401);
        }

        $data = User::findOrFail($user->id);

        if (!Hash::check($request->old_password, $data->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The specified password does not match the database password',
            ], 200);
        } else {
            $data->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => "Password updated successfully!",
            ], 200);
        }
    }
}
