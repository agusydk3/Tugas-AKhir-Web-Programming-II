<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $transactions = Transaction::with(['user', 'product'])
            ->latest()
            ->paginate(15);
            
        return view('admin.transactions', compact('transactions'));
    }
    
    /**
     * Display the specified transaction.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\View\View
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'product', 'stocks']);
        
        return view('admin.transactions.show', compact('transaction'));
    }
    
    /**
     * Update the status of a transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            $oldStatus = $transaction->status;
            $newStatus = $validated['status'];
            
            // Update transaction status
            $transaction->status = $newStatus;
            
            // If completing a previously non-completed transaction
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                // If payment method is QRIS, we need to process the stocks
                if ($transaction->payment_method === 'qris') {
                    // Mark stocks as sold
                    $availableStocks = $transaction->product->stocks()
                        ->where('is_sold', false)
                        ->limit($transaction->quantity)
                        ->get();
                    
                    if ($availableStocks->count() < $transaction->quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock available'
                        ], 400);
                    }
                    
                    foreach ($availableStocks as $stock) {
                        $stock->is_sold = true;
                        $stock->transaction_id = $transaction->id;
                        $stock->save();
                    }
                    
                    // Generate receipt number if not exists
                    if (!$transaction->receipt_number) {
                        $transaction->receipt_number = 'RCP-' . strtoupper(substr(md5($transaction->id . time()), 0, 10));
                    }
                }
            }
            // If cancelling or failing a previously completed transaction
            else if (($newStatus === 'cancelled' || $newStatus === 'failed') && $oldStatus === 'completed') {
                // Return stocks to inventory
                $transaction->stocks()->update([
                    'is_sold' => false,
                    'transaction_id' => null
                ]);
                
                // If payment method was saldo, refund the user
                if ($transaction->payment_method === 'saldo') {
                    $user = $transaction->user;
                    $user->balance += $transaction->total_amount;
                    $user->save();
                }
            }
            
            $transaction->save();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction status updated successfully',
                'transaction' => $transaction
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get transaction statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $stats = [
            'total' => Transaction::count(),
            'completed' => Transaction::where('status', 'completed')->count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'failed' => Transaction::where('status', 'failed')->count(),
            'cancelled' => Transaction::where('status', 'cancelled')->count(),
            'totalAmount' => Transaction::where('status', 'completed')->sum('total_amount'),
            'qrisCount' => Transaction::where('payment_method', 'qris')->count(),
            'saldoCount' => Transaction::where('payment_method', 'saldo')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
