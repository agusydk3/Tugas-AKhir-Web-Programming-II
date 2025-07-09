<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    /**
     * Menampilkan halaman deposit
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('deposit');
    }

    /**
     * Memproses permintaan deposit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function processDeposit(Request $request)
    {
        // Validasi input
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        // Generate payment ID
        $paymentId = 'DEP-' . Auth::id() . '-' . time();
        
        // Call payment API
        $client = new Client();
        
        try {
            $response = $client->post('http://147.139.163.61:8080/api/payments', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'amount' => (int) $request->amount,
                    'payment_id' => $paymentId
                ],
            ]);
            
            $paymentData = json_decode($response->getBody(), true);
            
            // Store payment data in session
            session([
                'deposit_payment' => [
                    'transaction_id' => $paymentData['transaction_id'],
                    'amount' => $paymentData['amount'],
                    'total_amount' => $paymentData['total_amount'],
                    'service_fee' => $paymentData['service_fee'],
                    'qris_image_url' => $paymentData['qris_image_url'],
                    'payment_id' => $paymentId,
                    'created_at' => now(),
                    'expires_at' => now()->addMinutes(15),
                ]
            ]);
            
            return view('deposit', [
                'qrCode' => true // Just pass a flag that QR image exists
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('user.deposit')->with('error', 'Gagal membuat permintaan deposit: ' . $e->getMessage());
        }
    }
    
    /**
     * Memeriksa status pembayaran deposit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        $depositPayment = session('deposit_payment');
        
        if (!$depositPayment) {
            return response()->json(['error' => 'Tidak ada pembayaran yang sedang diproses'], 404);
        }
        
        // Check if payment is expired
        if (now() > $depositPayment['expires_at']) {
            // Cancel the payment
            try {
                $client = new Client();
                $client->post("http://147.139.163.61:8080/api/payments/{$depositPayment['transaction_id']}/cancel");
                session()->forget('deposit_payment');
                return response()->json(['status' => 'expired'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        
        // Check payment status
        try {
            $client = new Client();
            $response = $client->get("http://147.139.163.61:8080/api/payments/{$depositPayment['transaction_id']}");
            $paymentData = json_decode($response->getBody(), true);
            
            // If payment is completed, add balance to user
            if ($paymentData['status'] === 'completed') {
                $user = Auth::user();
                $user->balance += $depositPayment['amount'];
                $user->save();
                
                // Clear payment session
                session()->forget('deposit_payment');
                
                return response()->json([
                    'status' => 'completed',
                    'message' => 'Pembayaran berhasil! Saldo telah ditambahkan.'
                ], 200);
            }
            
            return response()->json([
                'status' => $paymentData['status'],
                'timeLeft' => now()->diffInSeconds($depositPayment['expires_at'])
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Membatalkan pembayaran deposit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelDeposit(Request $request)
    {
        $depositPayment = session('deposit_payment');
        
        if (!$depositPayment) {
            return redirect()->route('user.deposit')->with('error', 'Tidak ada pembayaran yang sedang diproses');
        }
        
        try {
            $client = new Client();
            $client->post("http://147.139.163.61:8080/api/payments/{$depositPayment['transaction_id']}/cancel");
            session()->forget('deposit_payment');
            return redirect()->route('user.deposit')->with('info', 'Pembayaran telah dibatalkan');
        } catch (\Exception $e) {
            return redirect()->route('user.deposit')->with('error', 'Gagal membatalkan pembayaran: ' . $e->getMessage());
        }
    }
    
    /**
     * Get QRIS image from API and return it
     *
     * @return \Illuminate\Http\Response
     */
    public function getQrisImage()
    {
        $depositPayment = session('deposit_payment');
        
        if (!$depositPayment || !isset($depositPayment['qris_image_url'])) {
            abort(404, 'QRIS image not found');
        }
        
        try {
            // Get image from API
            $client = new Client();
            $response = $client->get($depositPayment['qris_image_url']);
            
            // Return image with appropriate headers
            return response($response->getBody())
                ->header('Content-Type', 'image/png');
                
        } catch (\Exception $e) {
            Log::error('Failed to fetch QRIS image: ' . $e->getMessage());
            abort(500, 'Failed to fetch QRIS image');
        }
    }
}
