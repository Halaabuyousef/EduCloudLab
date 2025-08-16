<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\Experiment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::latest('id')->paginate(10);
        $experiments = Experiment::all(); // <--- جلب كل التجارب
        return view('devices.index', compact('devices', 'experiments'));
    }

    public function create()
    {
        $experiments = Experiment::all();
        return view('devices.create', compact('experiments'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'status' => 'required|in:online,offline',
            'experiment_id' => 'nullable|exists:experiments,id',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $data = $request->except('_token');
        // $data['user_id'] = Auth::guard('admin')->id();

        // رفع الصورة إذا وجدت
        if ($request->hasFile('image')) {
            $imgName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/devices'), $imgName);
            $data['image'] = $imgName;
        }

        Device::create($data);

        return redirect()->route('admin.devices.index')
            ->with('msg', 'Device added successfully')->with('type', 'success');
    }

    public function edit($id)
    {
        $device = Device::findOrFail($id);
        $experiments = \App\Models\Experiment::all();
        return view('devices.edit', compact('device', 'experiments'));
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'status' => 'required|in:online,offline',
            'experiment_id' => 'nullable|exists:experiments,id',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);


        $data = $request->except('_token', '_method');
        // $data['user_id'] = Auth::guard('admin')->id();

        if ($request->hasFile('image')) {
            if ($device->image && file_exists(public_path('images/devices/' . $device->image))) {
                File::delete(public_path('images/devices/' . $device->image));
            }
            $imgName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/devices'), $imgName);
            $data['image'] = $imgName;
        }

        $device->update($data);

        return redirect()->route('admin.devices.index')
            ->with('msg', 'Device updated successfully')->with('type', 'info');
    }

    public function destroy($id)
    {
        Device::destroy($id);
        return redirect()->route('admin.devices.index')
            ->with('msg', 'Device deleted successfully')->with('type', 'danger');
    }

    public function trash()
    {
        $devices = Device::onlyTrashed()->latest('id')->paginate(10);
        return view('devices.trash', compact('devices'));
    }

    public function restore($id)
    {
        $exp = Device::onlyTrashed()->findOrFail($id);
        $exp->restore();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Restored successfully']);
        }
        return redirect()->route('admin.devices.trash')->with('msg', 'Restored')->with('type', 'info');
    }

    public function forceDelete($id)
    {
        $exp = Device::onlyTrashed()->findOrFail($id);

        if ($exp->image && file_exists(public_path('images/' . $exp->image))) {
            File::delete(public_path('images/' . $exp->image));
        }
        $exp->forceDelete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Deleted permanently']);
        }
        return redirect()->route('admin.devices.trash')->with('msg', 'Deleted permanently')->with('type', 'info');
    }
}
