<?php

namespace App\Http\Controllers\PMS;

use App\Http\Controllers\Controller;
use App\Models\PMS\Expense;
use App\Models\PMS\Project;
use App\Models\PMS\Vendor;
use App\Models\PMS\ExpenseCategory;
use Illuminate\Http\Request;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['project', 'category', 'vendor', 'creator']);

        // Apply filters
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('payment_mode') && $request->payment_mode) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $expenses = $query->orderBy('payment_date', 'desc')->paginate(20);

        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $vendors = Vendor::all();
        $paymentModes = ['cash', 'bank_transfer', 'cheque', 'upi', 'other'];

        return view('pms.expenses.index', compact(
            'expenses',
            'projects',
            'categories',
            'vendors',
            'paymentModes'
        ));
    }

    public function create()
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $vendors = Vendor::all();
        $paymentModes = ['cash', 'bank_transfer', 'cheque', 'upi', 'other'];

        return view('pms.expenses.create', compact(
            'projects',
            'categories',
            'vendors',
            'paymentModes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:expense_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi,other',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        // Check if expense exceeds project budget (for non-admins)
        if (!auth()->user()->hasRole('admin')) {
            $project = Project::with('proposal')->find($validated['project_id']);
            $totalExpenses = Expense::where('project_id', $validated['project_id'])->sum('total_amount');
            $newTotal = $totalExpenses + $validated['amount'] + $validated['tax'];

            if ($project->proposal && $newTotal > $project->proposal->budget) {
                return back()->withErrors([
                    'amount' => 'This expense would exceed the project budget. Please contact an administrator.'
                ])->withInput();
            }
        }

        $validated['created_by'] = auth()->id();
        $validated['total_amount'] = $validated['amount'] + $validated['tax'];

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        return view('pms.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $vendors = Vendor::all();
        $paymentModes = ['cash', 'bank_transfer', 'cheque', 'upi', 'other'];

        return view('pms.expenses.edit', compact(
            'expense',
            'projects',
            'categories',
            'vendors',
            'paymentModes'
        ));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:expense_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi,other',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        // Check if expense exceeds project budget (for non-admins)
        if (!auth()->user()->hasRole('admin')) {
            $project = Project::with('proposal')->find($validated['project_id']);
            $totalExpenses = Expense::where('project_id', $validated['project_id'])
                ->where('id', '!=', $expense->id)
                ->sum('total_amount');
            $newTotal = $totalExpenses + $validated['amount'] + $validated['tax'];

            if ($project->proposal && $newTotal > $project->proposal->budget) {
                return back()->withErrors([
                    'amount' => 'This expense would exceed the project budget. Please contact an administrator.'
                ])->withInput();
            }
        }

        $validated['total_amount'] = $validated['amount'] + $validated['tax'];

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ExpensesExport($request), 'expenses.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Expense::with(['project', 'category', 'vendor', 'creator']);

        // Apply the same filters as index method
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('payment_mode') && $request->payment_mode) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $expenses = $query->orderBy('payment_date', 'desc')->get();

        $pdf = PDF::loadView('expenses.pdf', compact('expenses'));

        return $pdf->download('expenses.pdf');
    }
}
