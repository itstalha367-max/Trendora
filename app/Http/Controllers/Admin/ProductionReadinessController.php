<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemTestRun;
use App\Services\PaymentGatewayDiagnostics;
use App\Services\ProductionReadinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class ProductionReadinessController extends Controller
{
    public function index(ProductionReadinessService $service)
    {
        return view('admin.operations.readiness', [
            'report' => $service->report(),
            'tests' => Schema::hasTable('system_test_runs') ? SystemTestRun::with('tester')->latest()->take(25)->get() : collect(),
        ]);
    }

    public function testMail(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        try {
            Mail::raw('Trendora production email test sent at '.now()->toDateTimeString().'. If you received this, the configured Laravel mail transport is working.', function ($message) use ($data) {
                $message->to($data['email'])->subject('Trendora email delivery test');
            });
            $this->log('mail', 'passed', 'Test email handed to the configured mail transport.', ['recipient' => $data['email'], 'mailer' => config('mail.default')]);
            return back()->with('success', 'Test email sent through the configured '.config('mail.default').' mailer.');
        } catch (\Throwable $e) {
            $message = str($e->getMessage())->limit(500)->toString();
            $this->log('mail', 'failed', $message, ['mailer' => config('mail.default')]);
            return back()->with('error', 'Email test failed: '.$message);
        }
    }

    public function testGateway(string $gateway, PaymentGatewayDiagnostics $diagnostics)
    {
        abort_unless(in_array($gateway, ['stripe', 'paypal', 'raftarpay'], true), 404);
        $result = $diagnostics->test($gateway);
        $this->log('payment.'.$gateway, $result['status'], $result['message'], $result['context'] ?? []);
        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    private function log(string $type, string $status, string $message, array $context = []): void
    {
        try {
            SystemTestRun::create([
                'type' => $type,
                'status' => $status,
                'message' => $message,
                'context' => $context,
                'tested_by' => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            // Diagnostics should still work before the Phase 7 migration is applied.
        }
    }
}
