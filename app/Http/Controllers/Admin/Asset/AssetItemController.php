<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetItemController extends Controller
{
    protected $type = "Purchase";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list asset-item');
        $sql = AssetItem::orderBy('id', 'DESC')->with('asset');

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('date', 'LIKE', '%'. $request->q.'%')
                ->orWhere('note', 'LIKE', '%'. $request->q.'%')
                ->orWhere('quantity', 'LIKE', '%'. $request->q.'%')
                ->orWhere('price', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('assets', function($q) use($request) {
                $q->where('name', $request->q);
            });
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.asset.entry', compact('result'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add asset-item');
        $items = [
            (object)[
                'id' => null,
                'asset_id' => null
            ]
        ];

        $assets = Asset::where('status','Active')->get();

        return view('admin.asset.entry', compact('items', 'assets'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add asset-item');
       $this->validate($request, [
        'date' => 'required|date',
        'note' => 'nullable|string|max:255',
        'asset_id' => 'required|array|min:1',
        'asset_id.*' => 'required|integer',
        'quantity' => 'required|array|min:1',
        'quantity.*' => 'required|numeric',
        'price' => 'required|array|min:1',
        'price.*' => 'required|numeric',
        'amount' => 'required|array|min:1',
        'amount.*' => 'required|numeric',
    ]);

    try {
        DB::beginTransaction();

        if (count($request->asset_id) > 0 ) {  
           foreach ($request->asset_id as $key => $asset) {
                $storeData = [
                    'date' => dbDateFormat($request->date),
                    'note' => $request->note,
                    'asset_id' => $request->asset_id[$key],
                    'quantity' => $request->quantity[$key],
                    'price' => $request->price[$key],
                    'total_amount' =>  $request->quantity[$key] * $request->price[$key],
                    'created_by' => auth()->user()->id
                ];
                AssetItem::create($storeData);
            }
        }
      
        DB::commit();
        $request->session()->flash('successMessage', 'Asset Entry was successfully added!');
        return redirect()->route('admin.asset.asset-items.index');
    } catch (Exception $e) {
        DB::rollBack();
        throw $e;
        $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
        return redirect()->route('admin.asset.asset-items.index');
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->authorize('show asset-item');
        $data = AssetItem::with('asset')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.asset-items.index', qArray());
        }

        return view('admin.asset.entry', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit asset-item');
        $data = AssetItem::with('asset')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.asset-items.index', qArray());
        }
        // dd($data);
        return view('admin.asset.entry', compact('data'))->with('edit', $id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit asset-item');
        $data = AssetItem::with('asset')->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Asset Entry not found!');
            return redirect()->route('admin.asset.asset-items.index', qArray());
        }

        $this->validate($request, [
            'date' => 'required|date',   
            'note' => 'nullable|string|max:255',
            'asset_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $storeData = [
                'date' => dbDateFormat($request->date),
                'note' => $request->note,
                'asset_id' => $request->asset_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total_amount' => $request->quantity * $request->price,
                'updated_by' => auth()->user()->id
            ];

    
            $data->update($storeData);

            DB::commit();

            $request->session()->flash('successMessage', 'Asset Entry was successfully updated!');
            return redirect()->route('admin.asset.asset-items.index');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.asset.asset-items.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete asset-item');
        $data = AssetItem::with('asset')->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.asset-items.index', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Asset Entry was successfully deleted!');
        return redirect()->route('admin.asset.asset-items.index', qArray());
    }
}