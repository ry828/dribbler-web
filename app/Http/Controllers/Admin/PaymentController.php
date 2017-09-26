<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $transactions = DB::table('transactions')
            ->leftJoin('users', 'users.id', 'transactions.user_id')
            ->leftJoin('categories', 'categories.category_id', 'transactions.category_id')
            ->select('transactions.transaction_id', 'transactions.value', 'transactions.created_at', 'users.name', 'categories.category_title', 'transactions.user_id')
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        return View('admin.pages.transactions', compact('transactions'));
    }
}
