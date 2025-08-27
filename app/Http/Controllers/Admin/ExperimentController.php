<?php

namespace App\Http\Controllers\Admin;

use App\Models\Experiment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class ExperimentController extends Controller
{
    public function index()
    {
        $experiments = Experiment::latest('id')->paginate(10);
        $devices = \App\Models\Device::all(); // أرسل الأجهزة
        return view('experiments.index', compact('experiments', 'devices'));
    }


    public function create()
    {
        return view('experiments.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
          
            'status'      => 'required|in:available,in_use,maintenance',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);


        $data = $request->except('_token');

        // حفظ الـ user_id الخاص بالأدمن
        $data['user_id'] = Auth::guard('admin')->id();

        // رفع الصورة إذا وجدت
        if ($request->hasFile('image')) {
            $imgName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images'), $imgName);
            $data['image'] = $imgName;
        }

        $experiment = Experiment::create($data);

        if ($request->filled('device_ids')) {
            $experiment->devices()->sync($request->device_ids);
        }

        return redirect()->route('admin.experiments.index')
            ->with('msg', 'Experiment added successfully')->with('type', 'success');
    }

    public function edit($id)
    {
        $experiment = Experiment::findOrFail($id);
        return view('experiments.edit', compact('experiment'));
    }

    public function update(Request $request, $id)
    {
        $experiment = Experiment::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'device_ids'   => ['nullable', 'array'],
            'device_ids.*' => ['integer', 'exists:devices,id'],
            'status'      => 'required|in:available,in_use,maintenance',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->except('_token', '_method', 'device_ids');
        $data['user_id'] = Auth::guard('admin')->id();

        if ($request->hasFile('image')) {
            if ($experiment->image && file_exists(public_path('images/' . $experiment->image))) {
                File::delete(public_path('images/' . $experiment->image));
            }
            $imgName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images'), $imgName);
            $data['image'] = $imgName;
        }

        $experiment->update($data);

        // مهم: تحديث العلاقة مع الأجهزة
        $experiment->devices()->sync($request->input('device_ids', []));

        return redirect()->route('admin.experiments.index')
            ->with('msg', 'Experiment updated successfully')->with('type', 'info');
    }

    public function destroy($id)
    {
        Experiment::destroy($id);
        return redirect()->route('admin.experiments.index')
            ->with('msg', 'Experiment deleted successfully')->with('type', 'danger');
    }

    public function trash()
    {
        $experiments = Experiment::onlyTrashed()->latest('id')->paginate(10);
        return view('experiments.trash', compact('experiments'));
    }

    public function restore($id)
    {
        $exp = Experiment::onlyTrashed()->findOrFail($id);
        $exp->restore();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Restored successfully']);
        }
        return redirect()->route('admin.experiments.trash')->with('msg', 'Restored')->with('type', 'info');
    }

    public function forceDelete($id)
    {
        $exp = Experiment::onlyTrashed()->findOrFail($id);

        if ($exp->image && file_exists(public_path('images/' . $exp->image))) {
            File::delete(public_path('images/' . $exp->image));
        }
        $exp->forceDelete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Deleted permanently']);
        }
        return redirect()->route('admin.experiments.trash')->with('msg', 'Deleted permanently')->with('type', 'info');
    }

    /*public function getdata(Request $request)
    {
        return DataTables::of(Experiment::query())
            ->addIndexColumn()
            ->editColumn('image', function ($row) {
                return $row->image
                    ? '<img src="' . asset('images/' . $row->image) . '" width="50" height="50" class="rounded"/>'
                    : '';
            })
            ->addColumn('action', function ($row) {
                return view('experiments.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }*/
}
