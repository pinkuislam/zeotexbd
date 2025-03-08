<?php

namespace App\Traits;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\CodeService;
use Sudip\MediaUploader\Facades\MediaUploader;

trait UserTrait
{
    public function users($role = null, $request)
    {
        $sql = User::orderBy('status', 'ASC')->orderBy('id', 'DESC');

        if ($role) {
            $sql->where('role', $role);
        }

        if ($request->q) {
            $sql->where('name', 'LIKE', '%'. $request->q . '%')
                ->orWhere('mobile', 'LIKE', '%'. $request->q . '%')
                ->orWhere('address', 'LIKE', '%'. $request->q . '%')
                ->orWhere('code', 'LIKE', '%'. $request->q . '%')
                ->orWhere('email', 'LIKE', '%'. $request->q . '%');
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }
        
        return $sql;
    }
    
    public function user($role)
    {
        $sql = User::where('role', $role);
        return $sql;
    }

    public function userRules($role, $request, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255|unique:users,mobile,' . $id . ',id',
            'status' => 'required|in:Active,Deactivated',
        ];
        $rules['email'] = 'required|string|max:255|unique:users,email,' . $id . ',id';

        return $rules;
    }

    public function checkMobileExists($mobile)
    {
        return User::where('mobile', $mobile)->first();
    }
    
    public function userCreate($request, $role)
    {
        $code = null;
        $code = CodeService::generateUserCode($role, User::class, 'code');

        
        $storeData = [
            'code' => $code,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'mobile_2' => $request->mobile_2,
            'email' => $request->email,
            'password' => ($request->password != null ? Hash::make($request->password) : Hash::make('12345678')),
            'status' => $request->status,
            'address' => $request->address,
            'color' => $request->color,
            'fb_page_link' => $request->fb_page_link,
            'role' => $role,
            'opening_due' => $request->opening_due,
            'nid_no' => $request->nid_no,
            'gender' => $request->gender,
            'created_by' => Auth()->user()->id,
        ];
        if ($request->hasFile('image')) {
            $file = MediaUploader::imageUpload($request->image, 'users', 1);
            $storeData['image'] = $file['name'];
        }
        $user = User::create($storeData);


        $user->syncRoles([$request->access_role]);
        
        return $user;
    }
    
    public function userUpdate($role, $request, $id)
    {
        $user = $this->user($role)->findOrFail($id);

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'mobile_2' => $request->mobile_2,
            'email' => $request->email,
            'status' => $request->status,
            'address' => $request->address,
            'color' => $request->color,
            'fb_page_link' => $request->fb_page_link,
            'role' => $role,
            'opening_due' => $request->opening_due,
            'nid_no' => $request->nid_no,
            'gender' => $request->gender,
            'updated_by' => Auth()->user()->id,
        ];

        if ($request->password) {
            $storeData['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('image')) {
            if($user->image) {
                MediaUploader::delete('users', $user->image, true);
            }

            $file = MediaUploader::imageUpload($request->image, 'users', 1);
            $storeData['image'] = $file['name'];
        }
        $user->update($storeData);
        $user->syncRoles([$request->access_role]);
        
        return $user;
    }
    
    public function userDelete($role, $id)
    {
        $user = $this->user($role)->findOrFail($id);
        $user->delete();
        
        return true;
    }

    
}
