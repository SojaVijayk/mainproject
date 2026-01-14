<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Finance\BankTransaction;
use App\Models\Finance\FinanceBankAccount;
use App\Models\Finance\DailyBankBalance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BankTransactionController extends Controller
{
  public function index(Request $request)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];

    $query = BankTransaction::with('bankAccount')->latest();

    // Optional: Filter by Account if provided
    if ($request->has('account_id')) {
      $query->where('finance_bank_account_id', $request->account_id);
    }

    $transactions = $query->paginate(20);

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

  public function dashboard(Request $request)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];

    $date = $request->input('date', Carbon::today()->format('Y-m-d'));

    $balances = DailyBankBalance::with('bankAccount')
      ->whereDate('date', $date)
      ->get();

    $totalOpening = $balances->sum('opening_balance');
    $totalReceipts = $balances->sum('receipts');
    $totalPayments = $balances->sum('payments');
    $totalClosing = $balances->sum('closing_balance');

    // Chart Data: Last 7 Days Trend (Receipts vs Payments)
    $startDate = Carbon::today()->subDays(6);
    $endDate = Carbon::today();

    $trendData = DailyBankBalance::selectRaw(
      'DATE(date) as date, SUM(receipts) as total_receipts, SUM(payments) as total_payments'
    )
      ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    // Prepare chart arrays
    $chartDates = [];
    $chartReceipts = [];
    $chartPayments = [];

    // Fill in missing dates with 0
    for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
      $dayStr = $d->format('Y-m-d');
      $dayData = $trendData->firstWhere('date', $dayStr);

      $chartDates[] = $d->format('d M');
      $chartReceipts[] = $dayData ? $dayData->total_receipts : 0;
      $chartPayments[] = $dayData ? $dayData->total_payments : 0;
    }

    return view(
      'finance.dashboard',
      compact(
        'balances',
        'date',
        'totalOpening',
        'totalReceipts',
        'totalPayments',
        'totalClosing',
        'chartDates',
        'chartReceipts',
        'chartPayments'
      ),
      [
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
      'file' => 'required|mimes:xlsx,csv',
    ]);

    try {
      \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DailyBankBalanceImport(), $request->file('file'));
      return redirect()
        ->route('pms.finance.bank-dashboard')
        ->with('success', 'Daily balances imported successfully.');
    } catch (\Exception $e) {
      \Illuminate\Support\Facades\Log::error('Import Error: ' . $e->getMessage());
      return redirect()
        ->back()
        ->withErrors(['file' => 'Error importing file: ' . $e->getMessage()]);
    }
  }
}
