<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StockController extends Controller
{
    /**
     * Register custom routes for this controller.
     * 
     * This is called by the RouteServiceProvider via routes/web.php
     */
    public static function routes()
    {
        Route::get('stocks/product/{product}', [self::class, 'showByProduct'])->name('stocks.product');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['stocks' => function($query) {
            $query->latest();
        }])->get();
        
        return view('admin.stock', compact('products'));
    }
    
    /**
     * Display stocks for a specific product.
     */
    public function showByProduct(Product $product)
    {
        $stocks = $product->stocks()->latest()->get();
        return response()->json([
            'success' => true,
            'product' => $product,
            'stocks' => $stocks,
            'stockCount' => $product->stockCount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'entries' => 'required|array|min:1',
            'entries.*.serial_number' => 'nullable|string|max:255',
        ]);
        
        $stocks = [];
        
        foreach ($validated['entries'] as $entry) {
            $stock = new Stock([
                'product_id' => $validated['product_id'],
                'serial_number' => $entry['serial_number'] ?? null,
                'is_sold' => false,
            ]);
            
            $stock->save();
            $stocks[] = $stock;
        }
        
        $product = Product::find($validated['product_id']);
        
        return response()->json([
            'success' => true,
            'message' => count($stocks) . ' stock entries added successfully',
            'stocks' => $stocks,
            'product' => $product,
            'stockCount' => $product->stockCount
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        return response()->json([
            'success' => true,
            'stock' => $stock,
            'product' => $stock->product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'serial_number' => 'nullable|string|max:255',
            'is_sold' => 'sometimes|boolean',
        ]);
        
        $stock->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Stock entry updated successfully',
            'stock' => $stock,
            'product' => $stock->product,
            'stockCount' => $stock->product->stockCount
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $product = $stock->product;
        $stock->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Stock entry deleted successfully',
            'product' => $product,
            'stockCount' => $product->stockCount
        ]);
    }
}
