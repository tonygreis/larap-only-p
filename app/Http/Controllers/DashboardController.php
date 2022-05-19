<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Services\PaypalService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();
        $paypal = PaymentPlatform::where('name', 'paypal')->first();
        $stripe = PaymentPlatform::where('name', 'stripe')->first();

        return view('dashboard', compact('currencies', 'paypal', 'stripe'));
    }

    public function pay(Request $request)
    {

        $request->validate([
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ]);

        $paymentPlatform = resolve(PaypalService::class);

        return $paymentPlatform->handlePayment($request);
    }

    public function approval()
    {
        $paymentPlatform = resolve(PaypalService::class);

        return $paymentPlatform->handleApproval();
    }

    public function cancelled()
    {
        return to_route('dashboard')->withErrors('You cancelled the payment');
    }
}
