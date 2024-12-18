<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;

class TransactionController extends Controller
{
    public function index()
    {
        return view('back.pages.admin.all_transactions');
    }

    public function show($transaction_code)
    {
        $transactionItems = Transaction::where('transaction_code', $transaction_code)->first();

        $rows = TransactionItem::leftJoin('inventories', 'transaction_items.inventory_id', '=', 'inventories.id')
            ->select('inventories.name', 'inventories.picture', 'inventories.selling_price', 'inventories.size', 'inventories.qty', 'inventories.sku', 'transaction_items.*')
            ->where('transaction_items.code', $transaction_code)
            ->get();

        return view('back.pages.admin.transaction-details', [
            'transactionItems' => $transactionItems,
            'rows' => $rows
        ]);
    }
}
