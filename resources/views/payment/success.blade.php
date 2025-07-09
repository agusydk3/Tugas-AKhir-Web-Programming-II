@extends('layouts.user-dashboard')
@section('title', 'Pembayaran Berhasil - Dashboard Customer')
@section('page-title', 'Pembayaran Berhasil')
@section('content')
<div class="section-card">
    <h2 class="section-title">Pembayaran Berhasil</h2>
    
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-message">
                <h3>Transaksi Berhasil</h3>
                <p>Produk digital Anda telah berhasil dibeli dan siap digunakan</p>
            </div>
        </div>
        
        <div class="transaction-details">
            <h4>Detail Transaksi</h4>
            
            <div class="detail-group">
                <div class="detail-item">
                    <span class="detail-label">ID Transaksi:</span>
                    <span class="detail-value">{{ $transaction->transaction_id }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nomor Kuitansi:</span>
                    <span class="detail-value">{{ $transaction->receipt_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal:</span>
                    <span class="detail-value">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value status-completed">Completed</span>
                </div>
            </div>
            
            <div class="product-details">
                <div class="product-image">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div class="product-placeholder">
                            {{ strtoupper(substr($product->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="product-info">
                    <h5>{{ $product->name }}</h5>
                    <div class="product-meta">
                        <div class="meta-item">
                            <span class="meta-label">Harga:</span>
                            <span class="meta-value">{{ $product->formatted_price }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Jumlah:</span>
                            <span class="meta-value">{{ $transaction->quantity }} unit</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Subtotal:</span>
                            <span class="meta-value">{{ $transaction->formatted_total }}</span>
                        </div>
                        @if(session('qris_payment_' . $transaction->transaction_id) && isset(session('qris_payment_' . $transaction->transaction_id)['service_fee']))
                        <div class="meta-item">
                            <span class="meta-label">Biaya Layanan:</span>
                            <span class="meta-value">Rp {{ number_format(session('qris_payment_' . $transaction->transaction_id)['service_fee'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="meta-item total">
                            <span class="meta-label">Total:</span>
                            @if(session('qris_payment_' . $transaction->transaction_id) && isset(session('qris_payment_' . $transaction->transaction_id)['total_amount']))
                                <span class="meta-value">Rp {{ number_format(session('qris_payment_' . $transaction->transaction_id)['total_amount'], 0, ',', '.') }}</span>
                            @else
                                <span class="meta-value">{{ $transaction->formatted_total }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-data">
                <div class="order-data-header">
                    <h4>Data Pesanan :</h4>
                    @if(count($serialNumbers) > 1)
                        <button class="copy-all-btn" id="copyAllData">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            <span>Salin Semua</span>
                        </button>
                    @endif
                </div>
                <div class="data-list">
                    @forelse($serialNumbers as $index => $serial)
                        <div class="data-item">
                            <div class="data-content">
                                <span class="data-label">Data {{ $index + 1 }}:</span>
                                <span class="data-value">{{ $serial }}</span>
                            </div>
                            <button class="copy-btn" data-serial="{{ $serial }}" title="Salin data">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="no-data">Tidak ada data pesanan</div>
                    @endforelse
                </div>
            </div>
            
            <div class="product-notes">
                <h4>Catatan Produk</h4>
                <div class="notes-content">
                    @if($product->notes)
                        {!! nl2br(e($product->notes)) !!}
                    @else
                        <p>Terima kasih telah melakukan pembelian. Produk digital ini dapat langsung digunakan dengan data yang telah diberikan.</p>
                        <p>Jika Anda mengalami kendala, silakan hubungi customer service kami.</p>
                    @endif
                </div>
            </div>
            
            <div class="receipt-actions">
                <button class="btn btn-primary" id="downloadReceipt">
                    <i class="fas fa-download"></i> Unduh Kuitansi
                </button>
                <a href="{{ route('user.history') }}" class="btn btn-outline">
                    <i class="fas fa-history"></i> Lihat Riwayat
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.success-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 20px;
}

.success-header {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 30px;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-bottom: 1px solid #c3e6cb;
}

.success-icon {
    font-size: 48px;
    color: #28a745;
    animation: successPulse 2s ease-in-out infinite;
}

@keyframes successPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.success-message h3 {
    margin: 0 0 5px 0;
    color: #155724;
    font-size: 24px;
    font-weight: 700;
}

.success-message p {
    margin: 0;
    color: #155724;
    opacity: 0.8;
    font-size: 16px;
}

.transaction-details {
    padding: 30px;
}

.transaction-details h4 {
    margin: 0 0 20px 0;
    color: #343a40;
    font-size: 18px;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.detail-group {
    margin-bottom: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #dee2e6;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding: 8px 0;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.detail-value {
    color: #495057;
    font-weight: 500;
}

.status-completed {
    color: #28a745;
    font-weight: 700;
    background: rgba(40, 167, 69, 0.1);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
}

.product-details {
    display: flex;
    gap: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    border: 1px solid #dee2e6;
}

.product-image {
    flex: 0 0 80px;
}

.product-image img {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.product-placeholder {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 30px;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.product-info {
    flex: 1;
}

.product-info h5 {
    margin: 0 0 15px 0;
    color: #343a40;
    font-size: 18px;
    font-weight: 600;
}

.product-meta {
    font-size: 14px;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 4px 0;
}

.meta-item.total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px dashed #ced4da;
}

.meta-label {
    color: #6c757d;
    font-weight: 500;
}

.meta-value {
    font-weight: 600;
    color: #495057;
}

.meta-item.total .meta-value {
    color: #28a745;
    font-size: 16px;
    font-weight: 700;
}

.order-data {
    margin-bottom: 30px;
}

.order-data-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.order-data-header h4 {
    margin: 0;
    color: #343a40;
    font-size: 18px;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
    flex: 1;
}

.copy-all-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
}

.copy-all-btn:hover {
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.data-list {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #dee2e6;
}

.data-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 16px 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.data-item:last-child {
    margin-bottom: 0;
}

.data-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.data-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.data-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.data-value {
    font-family: 'Courier New', monospace;
    font-size: 16px;
    color: #212529;
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    word-break: break-all;
}

.copy-btn {
    background: linear-gradient(135deg, #6c757d 0%, #8d939a 100%);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.copy-btn:hover {
    background: linear-gradient(135deg, #5a6268 0%, #7a8186 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.copy-btn.copied {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.no-data {
    text-align: center;
    color: #6c757d;
    padding: 20px;
}

.product-notes {
    margin-bottom: 30px;
}

.notes-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    color: #495057;
    line-height: 1.6;
}

.receipt-actions {
    display: flex;
    gap: 10px;
}

.btn {
    flex: 1;
    padding: 12px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: #28a745;
    color: white;
    border: none;
}

.btn-primary:hover {
    background: #218838;
}

.btn-outline {
    color: #6c757d;
    background: transparent;
    border: 1px solid #6c757d;
}

.btn-outline:hover {
    background: #f8f9fa;
    color: #495057;
}

@media (max-width: 768px) {
    .success-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .product-details {
        flex-direction: column;
    }
    
    .product-image {
        margin: 0 auto;
    }
    
    .receipt-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy serial number functionality
    const copyButtons = document.querySelectorAll('.copy-btn');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const serial = this.getAttribute('data-serial');
            navigator.clipboard.writeText(serial).then(() => {
                // Change button icon temporarily to show success
                const icon = this.querySelector('svg');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                
                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 1500);
            });
        });
    });

    // Copy all data functionality
    document.getElementById('copyAllData').addEventListener('click', function() {
        const allData = Array.from(document.querySelectorAll('.data-value')).map(data => data.textContent).join(', ');
        navigator.clipboard.writeText(allData).then(() => {
            alert('Semua data pesanan telah disalin ke clipboard.');
        });
    });

    // Download receipt functionality (mock)
    document.getElementById('downloadReceipt').addEventListener('click', function() {
        alert('Fitur unduh kuitansi akan segera tersedia.');
    });
});
</script>
@endsection