<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::query()->with(['category', 'order', 'user']);

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('expense_category_id', $request->category_id);
        }

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Filter berdasarkan pesanan
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('order_id', $request->order_id);
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        $categories = ExpenseCategory::where('is_active', true)->get();
        
        // Ambil pesanan untuk dropdown filter
        $orders = Order::select('id', 'order_number')->get();

        return Inertia::render('admin/expenses/Index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'orders' => $orders,
            'filters' => $request->only(['search', 'category_id', 'status', 'date_from', 'date_to', 'order_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $orders = Order::select('id', 'order_number')->get();

        return Inertia::render('admin/expenses/Create', [
            'categories' => $categories,
            'orders' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'order_id' => 'nullable|exists:orders,id',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_method' => 'required|in:cash,transfer,credit_card,other',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // Handle file upload
        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        }
        
        // Tambahkan user_id dari user yang login
        $data['user_id'] = auth()->id();

        Expense::create($data);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load(['category', 'order', 'user']);
        
        return Inertia::render('admin/expenses/Show', [
            'expense' => $expense
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $expense->load(['category', 'order']);
        
        $categories = ExpenseCategory::where('is_active', true)->get();
        $orders = Order::select('id', 'order_number')->get();

        return Inertia::render('admin/expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'orders' => $orders,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'order_id' => 'nullable|exists:orders,id',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_method' => 'required|in:cash,transfer,credit_card,other',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // Handle file upload
        if ($request->hasFile('receipt_image')) {
            // Hapus gambar lama jika ada
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            
            $data['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Hapus file gambar terkait jika ada
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }
        
        $expense->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
