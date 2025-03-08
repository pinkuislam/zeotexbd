<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list activity_log');

        $sql = Activity::orderBy('id','DESC');

        // $records = $sql->get();
        // return $records;
        $records = $sql->paginate($request->limit ?? 50);

        return view('admin.activity-log', compact('records'))->with('list', 1);
    }
}