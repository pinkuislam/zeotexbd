<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sudip\MediaUploader\Facades\MediaUploader;

class SiteSettingController extends Controller {

    public function create(Request $request)
    {
        $this->authorize('add site-setting');

        $data = SiteSetting::first();
        return view('admin.ecommerce.site-setting', compact('data')); 
    }    

    public function store(Request $request)
    {
        $this->authorize('add site-setting');

        $this->validate($request, [
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,bmp',
            'favicon' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,bmp,ico',
            'map_iframe' => 'nullable|string|max:1000',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'google' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'moto' => 'nullable|string',
        ]);

        $storeData = [
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'map_iframe' => $request->map_iframe,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'google' => $request->google,
            'organization_name' => $request->organization_name,
            'moto' => $request->moto,
        ];
        if ($request->hasFile('logo')) {
            $file = MediaUploader::imageUpload($request->logo, 'sitesetting', true, null, [250, 70]);
            if ($file) {
                $storeData['logo'] = $file['name'];
            }
        }
        if ($request->hasFile('favicon')) {
            $file = MediaUploader::imageUpload($request->favicon, 'sitesetting', true, null, [80, 80]);
            if ($file) {
                $storeData['favicon'] = $file['name'];
            }
        }
        $data = SiteSetting::first();
        if ($data) {
            $data->update($storeData);
        } else {
            $data = SiteSetting::create($storeData);
        }
        $request->session()->flash('successMessage', 'Site Setting was successfully update.');

        return back();
    }    
}