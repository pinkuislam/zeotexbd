<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list faq');

        $sql = Faq::with('creator');
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('question', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('answer', 'LIKE', '%'. $request->q . '%');
                $q->orWhereHas('creator', function($r) use($request) {
                    $r->where('name', 'LIKE', '%'. $request->q . '%');
                });
            });
        }
        if ($request->from) {
            $sql->where('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $sql->where('created_at', '<=', $request->to);
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }
        $records = $sql->orderBy('id', 'DESC')->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.ecommerce.faq.index', compact('records'));
    }

    public function create()
    {
        $this->authorize('add faq');
        return view('admin.ecommerce.faq.create');
    }

    public function store(Request $request)
    {
        $this->authorize('add faq');

        $this->validate($request, [
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:1000',
            'status' => 'required|in:Active,Deactivated',
        ]);
        $storeData = [
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        $data = Faq::create($storeData);
        if ($data) {
            $request->session()->flash('successMessage', 'Faq was successfully added.');
        } else {
            $request->session()->flash('errorMessage', 'Faq saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show faq');

        $data = Faq::with(['creator', 'updater'])->findOrFail($id);
        return view('admin.ecommerce.faq.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit faq');
        $data = Faq::findOrFail($id);
        return view('admin.ecommerce.faq.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit faq');
        $this->validate($request, [
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:1000',
            'status' => 'required|in:Active,Deactivated',
        ]);
        $data = Faq::findOrFail($id);
        $storeData = [
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];
        $data->update($storeData);

        if ($data) {
            $request->session()->flash('successMessage', 'Faq was successfully updated.');
        } else {
            $request->session()->flash('errorMessage', 'Faq updating failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete faq');

        try {
            $data = Faq::findOrFail($id);
            $data->delete();
            $request->session()->flash('successMessage', 'Faq was successfully deleted.');
        } catch (\Exception $e) {
            $request->session()->flash('errorMessage', 'Faq deleting failed! Reason: ' . $e->getMessage());
        }
        return redirect()->action([self::class, 'index'], qArray());
    }
}
