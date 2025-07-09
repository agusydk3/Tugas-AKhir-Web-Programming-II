<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's transactions
        $totalOrders = Transaction::where('user_id', $user->id)->count();
        $successfulOrders = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Calculate success rate
        $successRate = $totalOrders > 0 ? round(($successfulOrders / $totalOrders) * 100) : 0;
        
        // Get recent activities (transactions)
        $recentActivities = Transaction::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'title' => $transaction->status == 'completed' ? 'Order Berhasil' : 
                              ($transaction->status == 'pending' ? 'Order Diproses' : 
                              ($transaction->status == 'failed' ? 'Order Gagal' : 'Order Dibatalkan')),
                    'description' => 'Pembelian ' . $transaction->product->name,
                    'time_ago' => $transaction->created_at->diffForHumans(),
                    'icon' => $transaction->status == 'completed' ? '✅' : 
                             ($transaction->status == 'pending' ? '⏳' : 
                             ($transaction->status == 'failed' ? '❌' : '🚫')),
                    'icon_color' => $transaction->status == 'completed' ? '#00b894' : 
                                   ($transaction->status == 'pending' ? '#fdcb6e' : 
                                   ($transaction->status == 'failed' ? '#d63031' : '#636e72'))
                ];
            });
        
        return view('dashboard', compact('totalOrders', 'successRate', 'recentActivities'));
    }

    /**
     * Menampilkan halaman produk
     *
     * @return \Illuminate\View\View
     */
    public function product()
    {
        // Get all products and sort them - available products first, then out-of-stock products
        $availableProducts = Product::all()->filter(function ($product) {
            return $product->stockCount > 0;
        })->sortByDesc('created_at');
        
        $outOfStockProducts = Product::all()->filter(function ($product) {
            return $product->stockCount == 0;
        })->sortByDesc('created_at');

        // Merge the collections
        $products = $availableProducts->merge($outOfStockProducts)->values();
        
        return view('product', compact('products'));
    }


    /**
     * Menampilkan halaman riwayat transaksi
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $orders = Auth::user()->transactions()
            ->with('product')
            ->latest()
            ->paginate(10);
            
        return view('history', compact('orders'));
    }

    /**
     * Menampilkan halaman kontak
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Memproses pengiriman pesan kontak
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendContact(Request $request)
    {
        // Validasi input
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Logika mengirim pesan kontak
        // TODO: Implementasikan logika untuk menyimpan pesan ke database

        return redirect()->route('user.contact')->with('success', 'Pesan berhasil dikirim.');
    }

    /**
     * Menampilkan halaman order produk (pre-checkout)
     *
     * @param  int  $product
     * @return \Illuminate\View\View
     */
    public function order($product)
    {
        // Get product details
        $product = Product::findOrFail($product);
        
        // Check if product has stock
        if ($product->stockCount <= 0) {
            return redirect()->route('user.product')->with('error', 'Produk tidak tersedia.');
        }
        
        return view('order', [
            'product' => $product,
            'availableStock' => $product->stockCount
        ]);
    }
    
    /**
     * Process checkout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCheckout(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:qris,saldo',
        ]);
        
        // Get product
        $product = Product::findOrFail($validated['product_id']);
        
        // Check stock availability
        if ($product->stockCount < $validated['quantity']) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }
        
        // Calculate total amount
        $totalAmount = $product->price * $validated['quantity'];
        
        // Check user balance if payment method is saldo
        if ($validated['payment_method'] === 'saldo') {
            $user = Auth::user();
            
            if ($user->balance < $totalAmount) {
                return back()->with('error', 'Saldo tidak mencukupi. Silakan isi saldo terlebih dahulu.');
            }
        }
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'expired_at' => $validated['payment_method'] === 'qris' ? now()->addMinutes(15) : null,
            ]);
            
            // Process payment based on method
            if ($validated['payment_method'] === 'saldo') {
                // Process saldo payment immediately
                
                // Deduct user balance
                $user = Auth::user();
                $user->balance -= $totalAmount;
                $user->save();
                
                // Mark stocks as sold
                $availableStocks = $product->stocks()
                    ->where('is_sold', false)
                    ->limit($validated['quantity'])
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
                
                // Redirect to success page
                DB::commit();
                return redirect()->route('user.order.success', $transaction->transaction_id);
            } else {
                // For QRIS, redirect to payment page
                DB::commit();
                return redirect()->route('user.order.pending', $transaction->transaction_id);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Display pending payment page (QRIS)
     *
     * @param  string  $transaction
     * @return \Illuminate\View\View
     */
    public function pendingPayment($transaction)
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
        
        return view('payment.qris', [
            'transaction' => $transaction,
            'product' => $transaction->product,
            'timeLeft' => now()->diffInSeconds($transaction->expired_at)
        ]);
    }

    /**
     * Display success payment page
     *
     * @param  string  $transaction
     * @return \Illuminate\View\View
     */
    public function successPayment($transaction)
    {
        // Find transaction by transaction_id
        $transaction = Transaction::where('transaction_id', $transaction)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['product', 'stocks'])
            ->firstOrFail();
        
        return view('payment.success', [
            'transaction' => $transaction,
            'product' => $transaction->product,
            'serialNumbers' => $transaction->stocks->pluck('serial_number')
        ]);
    }
    
    /**
     * Cancel order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelOrder(Request $request, $transaction)
    {
        // Find transaction by transaction_id
        $transaction = Transaction::where('transaction_id', $transaction)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();
        
        $transaction->status = 'cancelled';
        $transaction->save();
        
        return redirect()->route('user.product')->with('info', 'Pesanan telah dibatalkan.');
    }


} 