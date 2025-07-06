<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class BkashController extends Controller
{
    // Show the checkout form

    // Create payment and generate token
    public function createPayment(Request $request)
    {
        // Generate token first
        $authToken = $this->generateToken();

        if (!$authToken) {
            return redirect()->back()->with('error', 'Token generation failed!');
        }

        // Fetch payment details
        $amount = $request->amount;
        $payerReference = $request->payerReference;

        // Create the payment
        $requestBody = [
            'mode' => '0011',
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'payerReference' => $payerReference,
            'merchantInvoiceNumber' => 'commonPayment001',
            'callbackURL' => route('bkash.execute')
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authToken,
            'X-APP-Key' => env('BKASH_APP_KEY'),
        ])->post('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout/create', $requestBody);

        $data = $response->json();

        if (isset($data['bkashURL'])) {
            // Redirect to bKash payment page
            return redirect($data['bkashURL']);
        }

        return redirect()->back()->with('error', 'Payment creation failed!');
    }

    // Execute the payment after bKash redirects back
    public function executePayment(Request $request)
    {
        $paymentID = $request->paymentID;
        $authToken = Session::get('token');

        $requestBody = [
            'paymentID' => $paymentID
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authToken,
            'X-APP-Key' => env('BKASH_APP_KEY'),
        ])->post('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout/execute', $requestBody);

        $data = $response->json();

        if (isset($data['paymentID'])) {
            return redirect()->route('bkash.status', ['paymentID' => $data['paymentID']]);
        }

        return redirect()->back()->with('error', 'Payment execution failed!');
    }

    // Query the payment status
    public function paymentStatus(Request $request)
    {
        $paymentID = $request->paymentID;
        $authToken = Session::get('token');

        $requestBody = [
            'paymentID' => $paymentID
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $authToken,
            'X-APP-Key' => env('BKASH_APP_KEY'),
        ])->post('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout/payment/status', $requestBody);

        $data = $response->json();
        \Log::info('bKash Payment Status Response:', $data);
        if ($data['statusMessage'] === 'Successful') {
            // Handle success, show success page
            return view('payment.success', ['data' => $data]);
        } else {
            // Handle failure, show failure page
            return view('payment.failed', ['data' => $data]);
        }
    }

    // Generate token for authentication
    private function generateToken()
    {
        $response = Http::withHeaders([
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
        ])->post('https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant', [
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
        ]);

        $data = $response->json();

        if (isset($data['id_token'])) {
            Session::put('token', $data['id_token']);
            return $data['id_token'];
        }

        return null;
    }

    public function index()
    {
        return view('payment.bkash-payment');
    }
}
