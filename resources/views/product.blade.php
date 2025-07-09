@extends('layouts.user-dashboard')
@section('title', 'Produk - Dashboard Customer')
@section('page-title', 'Produk')
@section('content')
<div class="section-card">
    <h2 class="section-title">Katalog Produk Digital</h2>
    
    <!-- Search and Filter Section -->
    <div class="product-filters" style="margin-bottom: 20px;">
        <div class="search-box" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" 
                   id="productSearch" 
                   class="search-input" 
                   placeholder="Cari produk..." 
                   style="flex: 1; min-width: 200px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px;">
            <select id="stockFilter" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="all">Semua Stok</option>
                <option value="available">Tersedia</option>
                <option value="out">Stok Habis</option>
            </select>
        </div>
    </div>
    
    <div class="product-grid" id="productGrid">
        @forelse($products as $product)
        <div class="product-card" data-product-name="{{ strtolower($product->name) }}" data-product-description="{{ strtolower($product->description ?? '') }}" data-stock="{{ $product->stockCount > 0 ? 'available' : 'out' }}">
            @if($product->stockCount == 0)
                <div class="out-of-stock-overlay">
                    <div class="out-of-stock-text">Stok Habis</div>
                </div>
            @else
                <div class="in-stock-badge">
                    <i class="fas fa-check-circle"></i> Tersedia
                </div>
            @endif
            
            <div class="product-header {{ $product->category_class ?? 'default-header' }}">
                <div style="display:flex;align-items:center;gap:12px;flex:1;">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    @else
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 18px; font-weight: bold; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                            {{ strtoupper(substr($product->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight: bold; font-size: 16px; opacity: 0.9;">
                            {{ Str::limit($product->name, 20) }}
                        </div>
                        <div style="font-size: 12px; opacity: 0.7;">
                            Digital Product
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="product-info">
                <h3 class="product-title">{{ $product->name }}</h3>
                <p class="product-description">
                    {{ $product->description ?? 'Produk berkualitas dengan harga terjangkau dan garansi penuh.' }}
                </p>
                <div class="product-price">{{ $product->formatted_price }}</div>
                <div class="product-stock">
                    <span class="stock-indicator {{ $product->stockCount > 0 ? 'in-stock' : 'out-of-stock' }}">
                        Stok: {{ $product->stockCount }}
                    </span>
                </div>
                
                @if($product->stockCount > 0)
                    <a href="{{ route('user.order', $product->id) }}" class="btn btn-success">
                        <i class="fas fa-shopping-cart"></i> Beli Sekarang
                    </a>
                @else
                    <button class="btn out" disabled>
                        <i class="fas fa-times"></i> Stok Habis
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
            <div style="color: #666; font-size: 18px;">
                <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                <h3>Belum Ada Produk</h3>
                <p>Produk sedang dalam proses penambahan. Silakan cek kembali nanti.</p>
            </div>
        </div>
        @endforelse
    </div>
    
    @if($products->count() > 0)
    <div class="product-summary" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <strong>Total Produk: {{ $products->count() }}</strong>
            </div>
            <div>
                <span class="text-success">
                    <i class="fas fa-check-circle"></i> 
                    {{ $products->where('stockCount', '>', 0)->count() }} Tersedia
                </span>
                <span style="margin: 0 10px;">|</span>
                <span class="text-danger">
                    <i class="fas fa-times-circle"></i> 
                    {{ $products->where('stockCount', 0)->count() }} Habis
                </span>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Product Grid Layout */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    height: fit-content;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Product Header Styles */
.product-header {
    padding: 20px;
    position: relative;
    min-height: 80px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.default-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.netflix-header {
    background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
}



/* Product Info Styles */
.product-info {
    padding: 20px;
}

.product-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
    line-height: 1.4;
}

.product-description {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    font-size: 20px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 10px;
}

.product-stock {
    margin-bottom: 15px;
}

.stock-indicator {
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.stock-indicator.in-stock {
    background: #d4edda;
    color: #155724;
}

.stock-indicator.out-of-stock {
    background: #f8d7da;
    color: #721c24;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
}

.btn.out {
    background: #6c757d;
    color: white;
    cursor: not-allowed;
    opacity: 0.7;
}

/* Out of Stock Overlay */
.out-of-stock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    z-index: 2;
    pointer-events: none;
}

.out-of-stock-text {
    color: white;
    font-weight: bold;
    font-size: 18px;
    text-transform: uppercase;
    text-align: center;
    padding: 10px;
    background: rgba(220, 53, 69, 0.8);
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* In Stock Badge */
.in-stock-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Search and Filter Styles */
.product-filters {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.search-box {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    min-width: 250px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-box select {
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.search-box select:focus {
    outline: none;
    border-color: #667eea;
}

/* Product Summary */
.product-summary {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 30px;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

/* No Products State */
.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-products i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
    opacity: 0.5;
}

.no-products h3 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #495057;
}

.no-products p {
    font-size: 16px;
    color: #6c757d;
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .product-filters {
        margin-bottom: 20px;
        padding: 15px;
    }
    
    .search-box {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input {
        min-width: auto;
    }
    
    .product-header {
        padding: 15px;
        min-height: 60px;
    }
    
    .product-info {
        padding: 15px;
    }
    
    .product-title {
        font-size: 16px;
    }
    
    .product-price {
        font-size: 18px;
    }
    
    .product-summary > div {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .product-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .product-card {
        border-radius: 10px;
    }
    
    .product-header {
        padding: 12px;
        min-height: 50px;
    }
    
    .product-info {
        padding: 12px;
    }
    
    .btn {
        padding: 10px 16px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    const stockFilter = document.getElementById('stockFilter');
    const productCards = document.querySelectorAll('.product-card');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const stockValue = stockFilter.value;
        
        productCards.forEach(card => {
            if (card.classList.contains('no-products')) return;
            
            const productName = card.getAttribute('data-product-name');
            const productDescription = card.getAttribute('data-product-description');
            const stockStatus = card.getAttribute('data-stock');
            
            // Check search term
            const matchesSearch = searchTerm === '' || 
                                productName.includes(searchTerm) || 
                                productDescription.includes(searchTerm);
            
            // Check stock filter
            const matchesStock = stockValue === 'all' || stockStatus === stockValue;
            
            // Show/hide card
            if (matchesSearch && matchesStock) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Check if any products are visible
        const visibleCards = Array.from(productCards).filter(card => 
            !card.classList.contains('no-products') && card.style.display !== 'none'
        );
        
        // Show/hide no results message
        let noResultsMsg = document.getElementById('noResultsMessage');
        if (visibleCards.length === 0 && productCards.length > 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.id = 'noResultsMessage';
                noResultsMsg.className = 'no-products';
                noResultsMsg.style.gridColumn = '1 / -1';
                noResultsMsg.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                        <h3>Tidak Ada Hasil</h3>
                        <p>Produk yang Anda cari tidak ditemukan. Coba kata kunci lain.</p>
                    </div>
                `;
                document.getElementById('productGrid').appendChild(noResultsMsg);
            }
            noResultsMsg.style.display = 'block';
        } else if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }
    
    // Event listeners
    searchInput.addEventListener('keyup', filterProducts);
    stockFilter.addEventListener('change', filterProducts);
    
    // Add fade in animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection