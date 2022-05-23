<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlatform;
use App\Models\Plan;
use App\Models\Subscription;
use App\Resolvers\PaymentPlatformResolver;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $paymentPlatformResolver;

    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function show()
    {
        $plans = Plan::all();
        $paypal = PaymentPlatform::where('name', 'paypal')->first();
        $stripe = PaymentPlatform::where('name', 'stripe')->first();

        return view('subscribe', compact('plans', 'paypal', 'stripe'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'plan' => ['required', 'exists:plans,slug'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ]);

        $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);
        session()->put('subscriptionPlatformId', $request->payment_platform);

        return $paymentPlatform->handleSubscription($request);
    }
    public function approval(Request $request)
    {
        $request->validate([
            'plan' => ['required', 'exists:plans,slug']
        ]);

        if (session()->has('subscriptionPlatformId')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('subscriptionPlatformId'));
            if ($paymentPlatform->validateSubscription($request)) {
                $plan = Plan::where('slug', $request->plan)->firstOrFail();
                $user = $request->user();

                $subscription = Subscription::create([
                    'active_until' => now()->addDays($plan->duration_in_days),
                    'user_id' => $user->id,
                    'plan_id' => $plan->id
                ]);

                return to_route('dashboard')->with('status', "Thanks, {$user->name}. You have a {$plan->slug} subscription.");
            }
        }
        return to_route('subscribe.show')->withErrors('We cannot check your subscription');
    }
    public function cancelled()
    {
        return to_route('subscribe.show')->withErrors('You cancelled, try again');
    }
}
