<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class PaymentGatewayController extends Controller
{
    protected string $filePath = 'payment_gateways.json';

    /**
     * Get default settings for payment gateways.
     */
    private function getDefaultSettings(): array
    {
        return [
            'stripe' => [
                'enabled' => false,
                'mode' => 'sandbox',
                'publishable_key' => '',
                'secret_key' => '',
            ],
            'razorpay' => [
                'enabled' => false,
                'mode' => 'sandbox',
                'key_id' => '',
                'key_secret' => '',
            ],
            'bank_transfer' => [
                'enabled' => true,
                'account_name' => 'EduCore ERP Solutions',
                'account_number' => '120938475623',
                'bank_name' => 'State Bank of India',
                'ifsc_code' => 'SBIN0001234',
                'instructions' => 'Please mention your school code in the transfer remarks/comments and send screenshot to support@schoolcloud.com.',
            ],
        ];
    }

    /**
     * Show payment gateways config panel.
     */
    public function index(): View
    {
        $settings = $this->getDefaultSettings();

        if (Storage::disk('local')->exists($this->filePath)) {
            $fileContent = json_decode(Storage::disk('local')->get($this->filePath), true);
            if (is_array($fileContent)) {
                // Merge loaded configurations to avoid missing indexes
                $settings = array_replace_recursive($settings, $fileContent);
            }
        }

        return view('superadmin.gateways.index', compact('settings'));
    }

    /**
     * Update payment gateway configurations in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Stripe validation
            'stripe.enabled' => 'nullable|string',
            'stripe.mode' => 'required|in:sandbox,live',
            'stripe.publishable_key' => 'nullable|string|max:255',
            'stripe.secret_key' => 'nullable|string|max:255',
            
            // Razorpay validation
            'razorpay.enabled' => 'nullable|string',
            'razorpay.mode' => 'required|in:sandbox,live',
            'razorpay.key_id' => 'nullable|string|max:255',
            'razorpay.key_secret' => 'nullable|string|max:255',
            
            // Bank Transfer validation
            'bank_transfer.enabled' => 'nullable|string',
            'bank_transfer.account_name' => 'nullable|string|max:255',
            'bank_transfer.account_number' => 'nullable|string|max:255',
            'bank_transfer.bank_name' => 'nullable|string|max:255',
            'bank_transfer.ifsc_code' => 'nullable|string|max:255',
            'bank_transfer.instructions' => 'nullable|string|max:1000',
        ]);

        $settings = [
            'stripe' => [
                'enabled' => isset($request->stripe['enabled']) && $request->stripe['enabled'] == '1',
                'mode' => $validated['stripe']['mode'],
                'publishable_key' => $request->stripe['publishable_key'] ?? '',
                'secret_key' => $request->stripe['secret_key'] ?? '',
            ],
            'razorpay' => [
                'enabled' => isset($request->razorpay['enabled']) && $request->razorpay['enabled'] == '1',
                'mode' => $validated['razorpay']['mode'],
                'key_id' => $request->razorpay['key_id'] ?? '',
                'key_secret' => $request->razorpay['key_secret'] ?? '',
            ],
            'bank_transfer' => [
                'enabled' => isset($request->bank_transfer['enabled']) && $request->bank_transfer['enabled'] == '1',
                'account_name' => $request->bank_transfer['account_name'] ?? '',
                'account_number' => $request->bank_transfer['account_number'] ?? '',
                'bank_name' => $request->bank_transfer['bank_name'] ?? '',
                'ifsc_code' => $request->bank_transfer['ifsc_code'] ?? '',
                'instructions' => $request->bank_transfer['instructions'] ?? '',
            ],
        ];

        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('superadmin.gateways.index')
            ->with('success', 'Payment gateway configurations saved successfully.');
    }
}
