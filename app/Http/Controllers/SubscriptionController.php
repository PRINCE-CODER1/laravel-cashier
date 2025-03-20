<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $subscriptions = $user->subscriptions;

        return view('subscriptions.index', compact('subscriptions'));
    }
    
}
