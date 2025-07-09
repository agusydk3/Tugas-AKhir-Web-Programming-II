<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QRISPaymentController extends Controller
{
    /**
     * Display QRIS payment page
     *
     * @param  string  $transaction
     * @return \Illuminate\View\View
     */
    public function showPayment($transaction)
    {
        // Find transaction by transaction_id
        $transaction = Transaction::where('transaction_id', $transaction)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('payment_method', 'qris')
            ->firstOrFail();
        
        // Check if transaction is expired
        if ($transaction->expired_at < now()) {
            $transaction->status = 'failed';
            $transaction->save();
            return redirect()->route('user.product')->with('error', 'Pembayaran telah kedaluwarsa.');
        }

        // If we don't have a QRIS payment yet, create one
        if (!session()->has('qris_payment_' . $transaction->transaction_id)) {
            try {
                $client = new Client();
                $response = $client->post('http://147.139.163.61:8080/api/payments', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'amount' => (int) $transaction->total_amount,
                        'payment_id' => $transaction->transaction_id
                    ],
                ]);
                
                $paymentData = json_decode($response->getBody(), true);
                
                // Store payment data in session
                session([
                    'qris_payment_' . $transaction->transaction_id => [
                        'api_transaction_id' => $paymentData['transaction_id'],
                        'amount' => $paymentData['amount'],
                        'total_amount' => $paymentData['total_amount'],
                        'service_fee' => $paymentData['service_fee'],
                        'qris_image_url' => $paymentData['qris_image_url'],
                        'status' => $paymentData['status'],
                    ]
                ]);
                
            } catch (\Exception $e) {
                Log::error('QRIS payment creation failed: ' . $e->getMessage());
                return redirect()->route('user.product')->with('error', 'Gagal membuat pembayaran QRIS: ' . $e->getMessage());
            }
        }
        
        $qrisPayment = session('qris_payment_' . $transaction->transaction_id);
        
        return view('payment.qris', [
            'transaction' => $transaction,
            'product' => $transaction->product,
            'timeLeft' => now()->diffInSeconds($transaction->expired_at),
            'qrisImageUrl' => true, // Just pass a flag that QR image exists
            'apiTransactionId' => $qrisPayment['api_transaction_id']
        ]);
    }
    
    /**
     * Check payment status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'api_transaction_id' => 'required|string'
        ]);
        
        $transactionId = $request->transaction_id;
        $apiTransactionId = $request->api_transaction_id;
        
        // Find transaction
        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();
        
        // Check if transaction is expired
        if ($transaction->expired_at < now()) {
            // Cancel the payment
            try {
                $client = new Client();
                $client->post("http://147.139.163.61:8080/api/payments/{$apiTransactionId}/cancel");
                
                // Update transaction status
                $transaction->status = 'failed';
                $transaction->save();
                
                // Clear payment session
                session()->forget('qris_payment_' . $transactionId);
                
                return response()->json([
                    'status' => 'expired',
                    'redirect' => route('user.product')
                ], 200);
            } catch (\Exception $e) {
                Log::error('Failed to cancel expired QRIS payment: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        
        // Check payment status
        try {
            $client = new Client();
            $response = $client->get("http://147.139.163.61:8080/api/payments/{$apiTransactionId}");
            $paymentData = json_decode($response->getBody(), true);
            
            // Update session data
            $qrisPayment = session('qris_payment_' . $transactionId);
            $qrisPayment['status'] = $paymentData['status'];
            session(['qris_payment_' . $transactionId => $qrisPayment]);
            
            // If payment is completed, process the order
            if ($paymentData['status'] === 'completed') {
                // Mark stocks as sold
                $product = $transaction->product;
                $availableStocks = $product->stocks()
                    ->where('is_sold', false)
                    ->limit($transaction->quantity)
                    ->get();
                
                foreach ($availableStocks as $stock) {
                    $stock->is_sold = true;
                    $stock->transaction_id = $transaction->id;
                    $stock->save();
                }
                
                // Generate receipt number
                $transaction->receipt_number = 'RCP-' . strtoupper(substr(md5($transaction->id . time()), 0, 10));
                $transaction->status = 'completed';
                $transaction->save();
                
                return response()->json([
                    'status' => 'completed',
                    'redirect' => route('user.order.success', $transaction->transaction_id)
                ], 200);
            }
            
            return response()->json([
                'status' => $paymentData['status'],
                'timeLeft' => now()->diffInSeconds($transaction->expired_at)
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Failed to check QRIS payment status: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Cancel payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelPayment(Request $request, $transaction)
    {
        // Find transaction
        $transaction = Transaction::where('transaction_id', $transaction)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();
        
        $qrisPayment = session('qris_payment_' . $transaction->transaction_id);
        
        if ($qrisPayment) {
            try {
                $client = new Client();
                $client->post("http://147.139.163.61:8080/api/payments/{$qrisPayment['api_transaction_id']}/cancel");
                
                // Clear payment session
                session()->forget('qris_payment_' . $transaction->transaction_id);
            } catch (\Exception $e) {
                Log::error('Failed to cancel QRIS payment: ' . $e->getMessage());
            }
        }
        
        // Update transaction status
        $transaction->status = 'cancelled';
        $transaction->save();
        
        return redirect()->route('user.product')->with('info', 'Pesanan telah dibatalkan.');
    }
    
    /**
     * Get QRIS image from API and return it
     *
     * @param  string  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function getQrisImage($transactionId)
    {
        // Find transaction by transaction_id
        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('payment_method', 'qris')
            ->firstOrFail();
            
        // Get QRIS payment data from session
        $qrisPayment = session('qris_payment_' . $transaction->transaction_id);
        
        if (!$qrisPayment || !isset($qrisPayment['qris_image_url'])) {
            abort(404, 'QRIS image not found');
        }
        
        try {
            // Get image from API
            $client = new Client();
            $response = $client->get($qrisPayment['qris_image_url']);
            
            // Return image with appropriate headers
            return response($response->getBody())
                ->header('Content-Type', 'image/png');
                
        } catch (\Exception $e) {
            Log::error('Failed to fetch QRIS image: ' . $e->getMessage());
            abort(500, 'Failed to fetch QRIS image');
        }
    }
} 