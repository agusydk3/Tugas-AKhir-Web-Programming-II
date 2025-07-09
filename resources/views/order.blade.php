@extends('layouts.user-dashboard')
@section('title', 'Pembayaran QRIS - Dashboard Customer')
@section('page-title', 'Pembayaran QRIS')
@section('content')
<div class="section-card">
    <h2 class="section-title">Pembayaran QRIS</h2>
    
    <div class="payment-container">
        <!-- Status Header -->
        <div class="payment-status-container">
            <div class="status-badge pending">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Menunggu Pembayaran</span>
            </div>
        </div>
        
        <!-- Main Payment Content - Two Column Layout -->
        <div class="payment-content">
            <!-- Left Column: QR Code (Primary) -->
            <div class="qr-column">
                <div class="qr-section">
                    <div class="qr-header">
                        <h3><i class="fas fa-qrcode"></i> Scan QR Code</h3>
                        <p>Gunakan aplikasi e-wallet untuk scan</p>
                    </div>
                    
                    <div class="qr-code-container">
                        @if(isset($qrisImageUrl))
                            <div class="qr-wrapper">
                                <img src="{{ route('user.order.qris-image', $transaction->transaction_id) }}" alt="QRIS Code" class="qr-image">
                            </div>
                        @else
                            <div class="qr-frame">
                                <i class="fas fa-qrcode"></i>
                                <div>QR Code Sedang Dimuat...</div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Countdown Timer -->
                    <div class="countdown-container">
                        <div class="countdown-label">Bayar dalam:</div>
                        <div class="countdown" id="countdown">
                            <span id="minutes">15</span>:<span id="seconds">00</span>
                        </div>
                        <div class="countdown-info">Transaksi akan dibatalkan jika tidak dibayar</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Transaction Details -->
            <div class="details-column">
                <!-- Product Info Section -->
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
                        <div class="info-item total-item">
                            <span class="info-label">Total Pembayaran:</span>
                            <span class="info-value total">Rp {{ number_format(session('qris_payment_' . $transaction->transaction_id)['total_amount'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Instructions Toggle -->
                <div class="payment-instructions-toggle">
                    <div class="instruction-header" onclick="toggleInstructions()">
                        <h4>
                            <i class="fas fa-info-circle"></i>
                            Cara Pembayaran
                        </h4>
                        <i class="fas fa-chevron-down toggle-icon" id="toggleIcon"></i>
                    </div>
                    <div class="instruction-content" id="instructionContent">
                        <div class="quick-steps">
                            <div class="quick-step">
                                <span class="step-num">1</span>
                                <span>Buka aplikasi e-wallet atau mobile banking</span>
                            </div>
                            <div class="quick-step">
                                <span class="step-num">2</span>
                                <span>Pilih menu "Scan QR" atau "QRIS"</span>
                            </div>
                            <div class="quick-step">
                                <span class="step-num">3</span>
                                <span>Arahkan kamera ke QR Code di samping</span>
                            </div>
                            <div class="quick-step">
                                <span class="step-num">4</span>
                                <span>Periksa detail transaksi dan nominal</span>
                            </div>
                            <div class="quick-step">
                                <span class="step-num">5</span>
                                <span>Konfirmasi dan selesaikan pembayaran</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="action-buttons">
                    <form action="{{ route('user.order.cancel', $transaction->transaction_id) }}" method="POST" style="width: 100%;">
                        @csrf
                        <button type="submit" class="btn btn-outline cancel-btn">
                            <i class="fas fa-times"></i> Batalkan Pesanan
                        </button>
                    </form>
                </div>
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
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.status-badge.pending {
    background-color: #fff3cd;
    color: #856404;
    border: 2px solid #ffeaa7;
}

.status-badge.success {
    background-color: #d4edda;
    color: #155724;
    border: 2px solid #00b894;
}

.status-badge.failed {
    background-color: #f8d7da;
    color: #721c24;
    border: 2px solid #e17055;
}

/* Main Content Layout - Two Columns */
.payment-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    padding: 30px;
    min-height: 600px;
}

/* QR Column (Left) - Primary Focus */
.qr-column {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.qr-section {
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.qr-header {
    margin-bottom: 25px;
}

.qr-header h3 {
    color: #2d3436;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.qr-header p {
    color: #636e72;
    font-size: 14px;
    margin: 0;
}

.qr-code-container {
    width: 320px;
    height: 320px;
    background: white;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    margin: 0 auto 25px;
    border: 3px solid #74b9ff;
    position: relative;
}

.qr-wrapper {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.qr-image {
    max-width: 100%;
    max-height: 100%;
    display: block;
    border-radius: 8px;
}

.qr-frame {
    text-align: center;
    color: #74b9ff;
}

.qr-frame i {
    font-size: 80px;
    margin-bottom: 15px;
}

.qr-frame div {
    font-size: 14px;
    font-weight: 600;
}

.countdown-container {
    background: linear-gradient(135deg, #fd79a8, #e84393);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(232, 67, 147, 0.3);
}

.countdown-label {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
    opacity: 0.9;
}

.countdown {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    font-family: 'Courier New', monospace;
}

.countdown-info {
    font-size: 12px;
    opacity: 0.8;
}

/* Details Column (Right) */
.details-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.product-info h3 {
    margin-bottom: 20px;
    color: #2d3436;
    font-size: 20px;
    font-weight: 700;
}

.transaction-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #0984e3;
}

.info-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding: 6px 0;
}

.info-item:last-child {
    margin-bottom: 0;
}

.total-item {
    border-top: 2px solid #ddd;
    padding-top: 15px;
    margin-top: 10px;
}

.info-label {
    font-weight: 600;
    color: #636e72;
    font-size: 14px;
}

.info-value {
    color: #2d3436;
    font-weight: 500;
    font-size: 14px;
}

.info-value.total {
    font-weight: 700;
    color: #00b894;
    font-size: 18px;
}

/* Payment Instructions Toggle */
.payment-instructions-toggle {
    border: 2px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
}

.instruction-header {
    background: #74b9ff;
    color: white;
    padding: 15px 20px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.3s ease;
    user-select: none;
}

.instruction-header:hover {
    background: #0984e3;
}

.instruction-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.toggle-icon {
    transition: transform 0.3s ease;
    font-size: 14px;
}

.toggle-icon.rotated {
    transform: rotate(180deg);
}

.instruction-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: white;
}

.instruction-content.show {
    max-height: 400px;
}

.quick-steps {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.quick-step {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    border-left: 4px solid #74b9ff;
}

.step-num {
    background: #74b9ff;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin-right: 12px;
    font-size: 14px;
}

.quick-step span:last-child {
    font-size: 14px;
    color: #2d3436;
    line-height: 1.4;
}

.action-buttons {
    margin-top: auto;
}

.cancel-btn {
    width: 100%;
    padding: 15px 20px;
    font-size: 16px;
    font-weight: 600;
    color: #e17055;
    background: transparent;
    border: 2px solid #e17055;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 50px;
}

.cancel-btn:hover {
    background: #e17055;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(225, 112, 85, 0.3);
}

@media (max-width: 768px) {
    .payment-content {
        grid-template-columns: 1fr;
        padding: 20px;
        gap: 25px;
    }
    
    .qr-code-container {
        width: 280px;
        height: 280px;
    }
    
    .countdown {
        font-size: 28px;
    }
}

@media (max-width: 480px) {
    .qr-code-container {
        width: 250px;
        height: 250px;
    }
    
    .status-badge {
        font-size: 14px;
        padding: 10px 15px;
    }
    
    .payment-content {
        padding: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    updateCountdown();
    
    const countdownInterval = setInterval(updateCountdown, 1000);
    
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
                
                statusBadge.classList.remove('pending');
                statusBadge.classList.add('success');
                statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Pembayaran Berhasil';
                
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            } else if (data.status === 'expired' || data.status === 'cancelled') {
                clearInterval(statusInterval);
                clearInterval(countdownInterval);
                
                statusBadge.classList.remove('pending');
                statusBadge.classList.add('failed');
                statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Pembayaran Gagal';
                
                setTimeout(() => {
                    window.location.href = data.redirect || "{{ route('user.product') }}";
                }, 2000);
            } else if (data.timeLeft) {
                timeLeft = data.timeLeft;
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
    }
    
    checkPaymentStatus();
    const statusInterval = setInterval(checkPaymentStatus, 10000);
});

function toggleInstructions() {
    const content = document.getElementById('instructionContent');
    const icon = document.getElementById('toggleIcon');
    
    if (content.classList.contains('show')) {
        content.classList.remove('show');
        icon.classList.remove('rotated');
    } else {
        content.classList.add('show');
        icon.classList.add('rotated');
    }
}
</script>
@endsection
