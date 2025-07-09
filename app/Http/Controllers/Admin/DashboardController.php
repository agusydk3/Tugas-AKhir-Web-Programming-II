<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get counts for dashboard stats
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalProducts = Product::count();
        $successfulTransactions = Transaction::where('status', 'completed')->count();
        $totalRevenue = Transaction::where('status', 'completed')->sum('total_amount');
        
        // Get recent transactions for the dashboard table
        $recentTransactions = Transaction::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalProducts', 
            'successfulTransactions', 
            'totalRevenue', 
            'recentTransactions'
        ));
    }
} 