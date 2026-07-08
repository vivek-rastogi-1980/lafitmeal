<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallet = $request->user()->getOrCreateWallet();

        return view('wallet', [
            'wallet' => $wallet,
            'transactions' => $wallet->transactions()->paginate(20),
        ]);
    }
}
