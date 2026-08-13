<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SystemTestRun;
use App\Services\PaymentGatewayDiagnostics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $gateways = [
            'stripe' => [
                'enabled' => Setting::get('payment_stripe_enabled', false),
                'key' => Setting::get('payment_stripe_key'),
                'secret' => Setting::get('payment_stripe_secret'),
                'mode' => Setting::get('payment_stripe_mode', 'sandbox'),
            ],
            'paypal' => [
                'enabled' => Setting::get('payment_paypal_enabled', false),
                'client_id' => Setting::get('payment_paypal_client_id'),
                'secret' => Setting::get('payment_paypal_secret'),
                'mode' => Setting::get('payment_paypal_mode', 'sandbox'),
            ],
            'raftarpay' => [
                'gateway' => config('raftarpay.default', 'jazzcash'),
                'environment' => config('raftarpay.environment', 'sandbox'),
            ],
        ];

        return view('admin.payments.index', compact('gateways'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stripe_enabled' => 'nullable|in:on',
            'stripe_key' => 'nullable|string|max:500',
            'stripe_secret' => 'nullable|string|max:500',
            'stripe_mode' => 'nullable|in:sandbox,live',
            'paypal_enabled' => 'nullable|in:on',
            'paypal_client_id' => 'nullable|string|max:500',
            'paypal_secret' => 'nullable|string|max:500',
            'paypal_mode' => 'nullable|in:sandbox,live',
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        Setting::set('payment_stripe_enabled', $request->has('stripe_enabled') ? 'on' : 'off');
        Setting::set('payment_stripe_key', $request->stripe_key);
        Setting::set('payment_stripe_secret', $request->stripe_secret);
        Setting::set('payment_stripe_mode', $request->stripe_mode ?? 'sandbox');
        Setting::set('payment_paypal_enabled', $request->has('paypal_enabled') ? 'on' : 'off');
        Setting::set('payment_paypal_client_id', $request->paypal_client_id);
        Setting::set('payment_paypal_secret', $request->paypal_secret);
        Setting::set('payment_paypal_mode', $request->paypal_mode ?? 'sandbox');

        return back()->with('success', 'Payment settings updated. Run a safe connection test before going live.');
    }

    public function testStripe(PaymentGatewayDiagnostics $diagnostics)
    {
        return $this->diagnosticResponse('stripe', $diagnostics);
    }

    public function testPayPal(PaymentGatewayDiagnostics $diagnostics)
    {
        return $this->diagnosticResponse('paypal', $diagnostics);
    }

    public function getGatewayStatus()
    {
        return response()->json([
            'stripe' => Setting::isPaymentEnabled('stripe'),
            'paypal' => Setting::isPaymentEnabled('paypal'),
            'raftarpay' => [
                'gateway' => config('raftarpay.default', 'jazzcash'),
                'environment' => config('raftarpay.environment', 'sandbox'),
            ],
        ]);
    }

    private function diagnosticResponse(string $gateway, PaymentGatewayDiagnostics $diagnostics)
    {
        $result = $diagnostics->test($gateway);
        try {
            SystemTestRun::create([
                'type' => 'payment.'.$gateway,
                'status' => $result['status'],
                'message' => $result['message'],
                'context' => $result['context'] ?? [],
                'tested_by' => auth()->id(),
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => $result['ok'],
            'message' => $result['message'],
            'context' => $result['context'] ?? [],
        ], $result['ok'] ? 200 : 422);
    }
}
