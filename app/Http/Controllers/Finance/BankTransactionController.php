<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Finance\BankTransaction;
use App\Models\Finance\FinanceBankAccount;
use Illuminate\Support\Facades\DB;

class BankTransactionController extends Controller
{
  public function index()
  {
      $pageConfigs = ['myLayout' => 'horizontal'];
    $transactions = BankTransaction::with('bankAccount')
      ->latest()
      ->paginate(20);
    return view('finance.transactions.index', compact('transactions'), [
      'pageConfigs' => $pageConfigs,
    ]);
  }

  public function create()
  {
     $accounts = FinanceBankAccount::where('is_active', true)->get();
      $pageConfigs = ['myLayout' => 'horizontal'];
    return view('finance.transactions.create', compact('accounts'), [
      'pageConfigs' => $pageConfigs,
    ]);
  }

  public function dashboard()
  {
      $pageConfigs = ['myLayout' => 'horizontal'];
    $accounts = FinanceBankAccount::where('is_active', true)->get();
    $totalBalance = $accounts->sum('current_balance');
    $recentTransactions = BankTransaction::with('bankAccount')
      ->latest()
      ->take(10)
      ->get();

    $todayInflow = BankTransaction::whereDate('transaction_date', today())
      ->where('type', 'credit')
      ->sum('amount');

    $todayOutflow = BankTransaction::whereDate('transaction_date', today())
      ->where('type', 'debit')
      ->sum('amount');

    return view(
      'finance.dashboard',
      compact('accounts', 'totalBalance', 'recentTransactions', 'todayInflow', 'todayOutflow'), [
      'pageConfigs' => $pageConfigs,
    ]
    );
  }

  public function store(Request $request)
  {
    $request->validate([
      'finance_bank_account_id' => 'required|exists:finance_bank_accounts,id',
      'type' => 'required|in:credit,debit',
      'amount' => 'required|numeric|min:0.01',
      'transaction_date' => 'required|date',
      'category' => 'required',
    ]);

    DB::transaction(function () use ($request) {
      $account = FinanceBankAccount::findOrFail($request->finance_bank_account_id);
      $balanceAfter = $account->current_balance;

      if ($request->type == 'credit') {
        $balanceAfter += $request->amount;
      } else {
        $balanceAfter -= $request->amount;
      }

      $account->update(['current_balance' => $balanceAfter]);

      BankTransaction::create([
        'finance_bank_account_id' => $request->finance_bank_account_id,
        'type' => $request->type,
        'amount' => $request->amount,
        'balance_after' => $balanceAfter,
        'transaction_date' => $request->transaction_date,
        'category' => $request->category,
        'description' => $request->description,
      ]);
    });

    return redirect()
      ->route('pms.finance.transactions.index')
      ->with('success', 'Transaction recorded successfully.');
  }

  public function import(Request $request)
  {
    $request->validate([
      'finance_bank_account_id' => 'required|exists:finance_bank_accounts,id',
      'file' => 'required|mimes:xlsx,csv',
    ]);

    \Maatwebsite\Excel\Facades\Excel::import(
      new \App\Imports\BankTransactionImport($request->finance_bank_account_id),
      $request->file('file')
    );

    return redirect()
      ->back()
      ->with('success', 'Transactions imported successfully.');
  }
}
