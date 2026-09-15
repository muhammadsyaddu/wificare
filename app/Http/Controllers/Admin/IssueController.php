<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerIssue;
use App\Models\IssueCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request): View
    {
        $issues = CustomerIssue::with(['user:id,name', 'category:id,name', 'order:id,order_number,status'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('category'), fn($q) => $q->where('issue_category_id', $request->category))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = IssueCategory::orderBy('name')->get(['id', 'name']);

        return view('admin.issues.index', compact('issues', 'categories'));
    }
}
