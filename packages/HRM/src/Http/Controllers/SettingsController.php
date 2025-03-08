<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Oshnisoft\HRM\Models\BasicSettings;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('add hr_basic-setting');
        $data = [];

        $data = BasicSettings::whereIn("name", ["in_time", "out_time", "weekend", "salary_structure"])
            ->get()
            ->pluck("value", "name")
            ->toArray();

        $data['weekend'] = isset($data['weekend']) ? json_decode($data['weekend']) : [];
        $data['salary_structure'] = isset($data['salary_structure']) ? json_decode($data['salary_structure'], true) : [];

        return view('hrm::settings', compact('data'));
    }

    public function store(Request $request)
    {
        $this->authorize('add hr_basic-setting');
        // login time
        BasicSettings::updateOrInsert(
            ["name" => "in_time"],
            [
                "name" => "in_time",
                "value" => $request->in_time
            ]);

        // logout time
        BasicSettings::updateOrInsert([
            "name" => "out_time"
        ], [
            "name" => "out_time",
            "value" => $request->out_time,
        ]);

        // weekend
        BasicSettings::updateOrInsert([
            "name" => "weekend"
        ], [
            "name" => "weekend",
            "value" => json_encode($request->weekend),
        ]);

        // salary structure
        BasicSettings::updateOrInsert([
            "name" => "salary_structure"
        ], [
            "name" => "salary_structure",
            "value" => json_encode($request->salary_structure),
        ]);

        return redirect()->action([self::class, 'index'])->with('successMessage', 'Setting updated successfully.');
    }
}
