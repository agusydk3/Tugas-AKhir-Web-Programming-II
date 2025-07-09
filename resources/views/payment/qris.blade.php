@extends('layouts.user-dashboard')
@section('title', 'Pembayaran QRIS - Dashboard Customer')
@section('page-title', 'Pembayaran QRIS')
@section('content')
<div class="section-card">
    <h2 class="section-title">Pembayaran QRIS</h2>
    
    <div class="payment-container">
        <div class="payment-status-container">
            <div class="status-badge pending">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Menunggu Pembayaran</span>
            </div>
        </div>
        
        <div class="payment-details">
            <div class="product-info">
                <h3>{{ $product->name }}</h3>
                <div class="transaction-info">
                    <div class="info-item">
                        <span class="info-label">ID Transaksi:</span>
                        <span class="info-value">{{ $transaction->transaction_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jumlah:</span>
                        <span class="info-value">{{ $transaction->quantity }} unit</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Harga Produk:</span>
                        <span class="info-value">{{ $transaction->formatted_total }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Biaya Layanan:</span>
                        <span class="info-value">Rp {{ number_format(session('qris_payment_' . $transaction->transaction_id)['service_fee'], 0, ',', '.') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Pembayaran:</span>
                        <span class="info-value total">Rp {{ number_format(session('qris_payment_' . $transaction->transaction_id)['total_amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="qr-container">
                <div class="qr-code-container">
                    @if(isset($qrisImageUrl))
                        <img src="{{ route('user.order.qris-image', $transaction->transaction_id) }}" alt="QRIS Code" class="qr-image">
                    @else
                        <div class="qr-frame">
                            <i class="fas fa-qrcode"></i>
                            <div>QR Code</div>
                        </div>
                    @endif
                </div>
                <div class="payment-instruction">
                    <h4>Cara Pembayaran:</h4>
                    <ol>
                        <li>Buka aplikasi e-wallet atau mobile banking Anda</li>
                        <li>Pilih menu Scan QR atau QRIS</li>
                        <li>Scan QR Code di samping</li>
                        <li>Periksa detail transaksi dan nominal pembayaran</li>
                        <li>Konfirmasi dan selesaikan pembayaran</li>
                    </ol>
                </div>
            </div>
            
            <div class="countdown-container">
                <div class="countdown-label">Bayar dalam:</div>
                <div class="countdown" id="countdown">
                    <span id="minutes">15</span>:<span id="seconds">00</span>
                </div>
                <div class="countdown-info">Transaksi akan dibatalkan jika tidak dibayar dalam waktu tersebut</div>
            </div>
            
            <div class="action-buttons">
                <form action="{{ route('user.order.cancel', $transaction->transaction_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline cancel-btn">
                        <i class="fas fa-times"></i> Batalkan Pesanan
                    </button>
                </form>
                <a href="{{ route('user.product') }}" class="btn btn-outline back-btn">
                    <i class="fas fa-arrow-left"></i> Kembali ke Produk
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.payment-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 20px;
}

.payment-status-container {
    background: #f8f9fa;
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 16px;
}

.status-badge.pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-badge.success {
    background-color: #d4edda;
    color: #155724;
}

.status-badge.failed {
    background-color: #f8d7da;
    color: #721c24;
}

.payment-details {
    padding: 20px;
}

.product-info {
    margin-bottom: 20px;
}

.product-info h3 {
    margin-bottom: 15px;
    color: #343a40;
    font-size: 20px;
}

.transaction-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #495057;
}

.info-value {
    color: #495057;
}

.info-value.total {
    font-weight: 700;
    color: #28a745;
    font-size: 16px;
}

.qr-container {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.qr-code-container {
    flex: 0 0 200px;
    height: 200px;
    background: #f8f9fa;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ced4da;
    overflow: hidden;
}

.qr-image {
    max-width: 100%;
    max-height: 100%;
    display: block;
}

.qr-frame {
    text-align: center;
    color: #6c757d;
}

.qr-frame i {
    font-size: 80px;
    margin-bottom: 10px;
}

.payment-instruction {
    flex: 1;
    min-width: 250px;
}

.payment-instruction h4 {
    margin-bottom: 15px;
    color: #343a40;
}

.payment-instruction ol {
    padding-left: 20px;
    color: #495057;
}

.payment-instruction li {
    margin-bottom: 8px;
}

.countdown-container {
    background: #e9ecef;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

.countdown-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}

.countdown {
    font-size: 28px;
    font-weight: 700;
    color: #dc3545;
    margin-bottom: 5px;
}

.countdown-info {
    font-size: 12px;
    color: #6c757d;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.cancel-btn, .back-btn {
    flex: 1;
    padding: 12px;
    font-size: 16px;
    font-weight: 600;
    color: #6c757d;
    background: transparent;
    border: 1px solid #6c757d;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.cancel-btn:hover, .back-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

@media (max-width: 768px) {
    .qr-container {
        flex-direction: column;
    }
    
    .qr-code-container {
        margin: 0 auto;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set the countdown timer
    let timeLeft = {{ $timeLeft }};
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    const statusBadge = document.querySelector('.status-badge');
    const apiTransactionId = "{{ $apiTransactionId }}";
    const transactionId = "{{ $transaction->transaction_id }}";
    
    function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        minutesEl.textContent = minutes.toString().padStart(2, '0');
        secondsEl.textContent = seconds.toString().padStart(2, '0');
        
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            statusBadge.classList.remove('pending');
            statusBadge.classList.add('failed');
            statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Pembayaran Kedaluwarsa';
            
            setTimeout(() => {
                window.location.href = "{{ route('user.product') }}";
            }, 3000);
        }
        
        timeLeft -= 1;
    }
    
    // Initial call to set the correct time
    updateCountdown();
    
    // Update countdown every second
    const countdownInterval = setInterval(updateCountdown, 1000);
    
    // Check payment status every 10 seconds
    function checkPaymentStatus() {
        fetch("{{ route('user.order.check-status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                transaction_id: transactionId,
                api_transaction_id: apiTransactionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'completed') {
                clearInterval(statusInterval);
                clearInterval(countdownInterval);
                
                // Update UI to show success
                statusBadge.classList.remove('pending');
                statusBadge.classList.add('success');
                statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Pembayaran Berhasil';
                
                // Redirect to success page
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            } else if (data.status === 'expired' || data.status === 'cancelled') {
                clearInterval(statusInterval);
                clearInterval(countdownInterval);
                
                // Update UI to show failure
                statusBadge.classList.remove('pending');
                statusBadge.classList.add('failed');
                statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Pembayaran Gagal';
                
                // Redirect to product page
                setTimeout(() => {
                    window.location.href = data.redirect || "{{ route('user.product') }}";
                }, 2000);
            } else if (data.timeLeft) {
                // Update the timer if returned from server
                timeLeft = data.timeLeft;
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
    }
    
    // Check status immediately and then every 10 seconds
    checkPaymentStatus();
    const statusInterval = setInterval(checkPaymentStatus, 10000);
});
</script>
@endsection 