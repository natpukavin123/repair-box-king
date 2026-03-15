<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\LedgerTransaction;
use App\Http\Requests\ExpenseRequest;

class ExpenseController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Expense::with('category', 'creator')
                ->when(request('search'), fn($q, $s) => $q->where('description', 'like', "%{$s}%"))
                ->when(request('category_id'), fn($q, $id) => $q->where('category_id', $id))
                ->when(request('date_from'), fn($q, $d) => $q->where('expense_date', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->where('expense_date', '<=', $d))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.expenses.index');
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('modules.expenses.create', compact('categories'));
    }

    public function store(ExpenseRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $expense = Expense::create($data);

        LedgerTransaction::create([
            'transaction_type' => 'expense',
            'reference_module' => 'expenses',
            'reference_id' => $expense->id,
            'amount' => $expense->amount,
            'payment_method' => $expense->payment_method ?? 'cash',
            'direction' => 'OUT',
            'description' => $expense->description ?? 'Expense',
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $expense->load('category'), 'message' => 'Expense recorded']);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->validated());
        return response()->json(['success' => true, 'data' => $expense->load('category'), 'message' => 'Expense updated']);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['success' => true, 'message' => 'Expense deleted']);
    }

    // ── Expense Categories ─────────────────────────────────────────────

    public function categories()
    {
        return response()->json(ExpenseCategory::withCount('expenses')->orderBy('name')->get());
    }

    public function storeCategory()
    {
        $data = request()->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);
        $cat = ExpenseCategory::create($data);
        return response()->json(['success' => true, 'data' => $cat]);
    }

    public function updateCategory(ExpenseCategory $category)
    {
        $data = request()->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);
        $category->update($data);
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function destroyCategory(ExpenseCategory $category)
    {
        if ($category->expenses()->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: category has expenses'], 422);
        }
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
}
