<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Oshnisoft\HRM\Models\BasicSettings;
use Oshnisoft\HRM\Models\Calendar;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_calendar');

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $records = Calendar::whereYear('date', $year)->whereMonth('date', $month)->orderBy('date', 'ASC')->get();

        return view('hrm::calendar', compact('year', 'month', 'records'));
    }

    public function store(Request $request)
    {
        $this->authorize('add hr_calendar');
        $setting = (object)[
            'in_time' => '10:00',
            'out_time' => '18:00',
            'weekends' => '["Fri", "Sat"]',
        ];

        $year = date('Y');
        $weekends = json_decode($setting->weekends, true);
        $calendarData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthDays = monthDays($year, $month);
            for ($day = 1; $day <= $monthDays; $day++) {
                $dayName = date("D", mktime(0, 0, 0, $month, $day, $year));
                if (in_array($dayName, $weekends)) {
                    $calendarData[] = [
                        'date' => $year . '-' . $month . '-' . $day,
                        'in_time' => null,
                        'out_time' => null,
                        'working_hours' => null,
                        'note' => 'Weekend',
                        'status' => 'Closed',
                        'created_by' => Auth::user()->id,
                        'created_at' => now(),
                    ];
                } else {
                    $calendarData[] = [
                        'date' => $year . '-' . $month . '-' . $day,
                        'in_time' => $setting->in_time,
                        'out_time' => $setting->out_time,
                        'working_hours' => timeDiff($setting->in_time, $setting->out_time),
                        'note' => 'Working Day',
                        'status' => 'Open',
                        'created_by' => Auth::user()->id,
                        'created_at' => now(),
                    ];
                }
            }
        }
        Calendar::insert($calendarData);

        $request->session()->flash('successMessage', 'Calendar was successfully added!');
        return redirect()->route('oshnisoft-hrm.calendars.index');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit hr_calendar');

        $validator = Validator::make($request->except('_token'), [
            'id' => 'required|integer',
            'status' => 'required|in:Open,Closed',
            'note' => 'nullable|string',
            'in_time' => 'required_if:status,==,Open',
            'out_time' => 'required_if:status,==,Open',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('errorMessage', implode("<br>", $validator->messages()->all()));
            return redirect()->back();
        }

        $data = Calendar::findOrFail($request->id);
        $storeData = [
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
            'working_hours' => timeDiff($request->in_time, $request->out_time),
            'note' => $request->note,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Calendar was successfully updated!');
        return redirect()->route('oshnisoft-hrm.calendars.index', qArray());
    }

    public function generateCalendar(Request $request, $year)
    {
        $this->authorize('add hr_calendar');

        $data = BasicSettings::whereIn("name", ["in_time", "out_time", "weekend", "salary_structure"])
            ->get()
            ->pluck("value", "name")
            ->toArray();

        $data['weekend'] = isset($data['weekend']) ? json_decode($data['weekend']) : [];
        if (isset($data['weekend']) && isset($data['in_time']) && isset($data['out_time'])) {
            $calendarData = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthDays = monthDays($year, $month);
                for ($day = 1; $day <= $monthDays; $day++) {
                    $dayName = date("D", mktime(0, 0, 0, $month, $day, $year));
                    if (in_array($dayName, $data['weekend'])) {
                        $calendarData[] = [
                            'date' => $year . '-' . $month . '-' . $day,
                            'in_time' => null,
                            'out_time' => null,
                            'working_hours' => null,
                            'note' => 'Weekend',
                            'status' => 'Closed',
                            'created_by' => Auth::user()->id,
                            'created_at' => now(),
                        ];
                    } else {
                        $calendarData[] = [
                            'date' => $year . '-' . $month . '-' . $day,
                            'in_time' => $data['in_time'],
                            'out_time' => $data['out_time'],
                            'working_hours' => timeDiff($data['in_time'], $data['out_time']),
                            'note' => 'Working Day',
                            'status' => 'Open',
                            'created_by' => Auth::user()->id,
                            'created_at' => now(),
                        ];
                    }
                }
            }
            Calendar::insert($calendarData);

            $request->session()->flash('successMessage', 'Calendar was successfully added!');
            return redirect()->route('oshnisoft-hrm.calendars.index');
        } else {
            $request->session()->flash('errorMessage', 'Please setup basic settings!');
            return redirect()->route('oshnisoft-hrm.calendars.index');
        }


    }
}
