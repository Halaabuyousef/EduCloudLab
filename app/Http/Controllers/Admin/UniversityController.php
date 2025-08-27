<?php

namespace App\Http\Controllers\Admin;

use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UniversityController extends Controller
{public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $universities = University::withCount('supervisors')
            ->when($q, fn($qq)=>$qq->where('name','like',"%$q%"))
            ->latest('id')->paginate(10)->withQueryString();

        return view('universities.index', compact('universities','q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:190','unique:universities,name'],
            'status' => ['required','in:active,inactive'],
        ]);
        University::create($data);
        return back()->with(['msg'=>'تمت إضافة الجامعة','type'=>'success']);
    }

    public function update(Request $request, University $university)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:190', Rule::unique('universities','name')->ignore($university->id)],
            'status' => ['required','in:active,inactive'],
        ]);
        $university->update($data);
        return back()->with(['msg'=>'تم تحديث الجامعة','type'=>'success']);
    }

    public function destroy(University $university)
    {
        // ملاحظة: لو عنده مشرفين مرتبطين، يفضّل منع الحذف أو مطالبة بنقلهم أولًا
        if ($university->supervisors()->exists()) {
            return back()->with(['msg'=>'لا يمكن الحذف وهناك مشرفون مرتبطون','type'=>'danger']);
        }
        $university->delete();
        return back()->with(['msg'=>'تم حذف الجامعة','type'=>'success']);
    }
}
