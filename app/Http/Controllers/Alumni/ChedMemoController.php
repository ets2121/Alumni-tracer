<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\ChedMemo;
use Illuminate\Http\Request;

class ChedMemoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $memos = ChedMemo::when($search, function ($q) use ($search) {
            return $q->where('title', 'like', "%{$search}%")
                ->orWhere('memo_number', 'like', "%{$search}%");
        })->latest()->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('alumni.memos.partials._list', compact('memos'));
        }

        return view('alumni.memos.index', compact('memos', 'search'));
    }
}
