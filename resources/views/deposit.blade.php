@extends('layouts.user-dashboard')

@section('title', 'Deposit - Dashboard Customer')

@section('page-title', 'Deposit')

@section('content')
<div class="section-card">
    <h2 class="section-title">Deposit Saldo</h2>
    <form id="depositForm" action="{{ route('user.deposit.process') }}" method="POST" autocomplete="off">
        @csrf
        <div class="deposit-form-group">
            <label class="deposit-label" for="deposit-amount">Nominal Deposit</label>
            <input type="number" min="1000" step="1000" id="deposit-amount" class="deposit-input" name="amount" placeholder="Masukkan nominal (minimal 1.000)" required>
        </div>
        
        @if(session('success'))
            <div id="deposit-success" style="margin-bottom: 15px; color:#00b894; font-weight:600;">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div id="deposit-error" style="margin-bottom: 15px; color:#e17055; font-weight:600;">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('info'))
            <div id="deposit-info" style="margin-bottom: 15px; color:#0984e3; font-weight:600;">
                {{ session('info') }}
            </div>
        @endif
        
        @if($errors->any())
            <div style="margin-bottom: 15px; color:#e17055; font-weight:600;">
                <ul style="list-style-type: none; padding: 0; margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <button type="submit" class="deposit-btn">Bayar dengan QRIS</button>
    </form>
</div>
<div class="section-card" style="margin-top:12px;">
    <p>Saldo akan otomatis masuk setelah pembayaran berhasil diverifikasi.</p>
    <p>Pembayaran akan kedaluwarsa dalam 15 menit jika tidak diselesaikan.</p>
</div>

@if(isset($qrCode))
<div class="section-card" id="payment-section">
    <h2 class="section-title">Pembayaran QRIS</h2>
    <div style="text-align: center; margin: 20px 0;">
        <img src="{{ route('user.deposit.qris-image') }}" alt="QRIS Code" style="max-width: 300px;">
        <p style="margin-top: 15px; font-weight: 600;">Scan QR Code di atas menggunakan aplikasi e-wallet Anda</p>
        
        <div class="payment-details" style="margin: 20px auto; max-width: 300px; text-align: left; border: 1px solid #ddd; border-radius: 8px; padding: 15px; background-color: #f8f9fa;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Jumlah Deposit:</span>
                <span>Rp {{ number_format(session('deposit_payment')['amount'], 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Biaya Layanan:</span>
                <span>Rp {{ number_format(session('deposit_payment')['service_fee'], 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px dashed #ddd; font-weight: bold;">
                <span>Total Pembayaran:</span>
                <span>Rp {{ number_format(session('deposit_payment')['total_amount'], 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="payment-instructions" style="margin: 20px auto; max-width: 300px; text-align: left;">
            <div class="instruction-header" id="toggleInstructions" style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background-color: #f1f2f6; border-radius: 8px; cursor: pointer;">
                <span style="font-weight: 600;">Cara Pembayaran</span>
                <span id="arrow-icon">▼</span>
            </div>
            <div id="instructionsContent" style="display: none; padding: 15px; border: 1px solid #ddd; border-radius: 0 0 8px 8px; margin-top: -5px;">
                <ol style="padding-left: 20px; margin: 0;">
                    <li style="margin-bottom: 10px;">Buka aplikasi e-wallet atau mobile banking Anda</li>
                    <li style="margin-bottom: 10px;">Pilih menu scan QRIS atau Scan QR</li>
                    <li style="margin-bottom: 10px;">Arahkan kamera ke kode QR di atas</li>
                    <li style="margin-bottom: 10px;">Periksa detail pembayaran di aplikasi Anda</li>
                    <li style="margin-bottom: 10px;">Konfirmasi dan selesaikan pembayaran</li>
                    <li>Saldo akan otomatis ditambahkan setelah pembayaran berhasil</li>
                </ol>
            </div>
        </div>
        
        <div id="timer" style="margin-top: 10px; font-size: 18px; font-weight: bold; color: #e17055;"></div>
        <div id="payment-status" style="margin-top: 15px;"></div>
        
        <div style="margin-top: 20px;">
            <form id="cancelForm" action="{{ route('user.deposit.cancel') }}" method="POST">
                @csrf
                <button type="submit" class="deposit-btn" style="background-color: #e17055;">Batalkan Pembayaran</button>
            </form>
        </div>
    </div>
</div>

<!-- Modern Success Animation Overlay -->
<div id="success-animation-overlay" class="success-overlay">
    <div class="success-container">
        <!-- Floating particles background -->
        <div class="particles"></div>
        
        <!-- Main success content -->
        <div class="success-content">
            <!-- Modern checkmark with ripple effect -->
            <div class="checkmark-wrapper">
                <div class="checkmark-ripple"></div>
                <div class="checkmark-circle">
                    <svg class="checkmark-svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle-bg" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </div>
            
            <!-- Success text with modern typography -->
            <h2 class="success-title">Pembayaran Berhasil!</h2>
            <p class="success-subtitle">Saldo telah ditambahkan ke akun Anda</p>
            
            <!-- Loading indicator -->
            <div class="loading-container">
                <div class="loading-bar">
                    <div class="loading-progress"></div>
                </div>
                <p class="loading-text">Mengalihkan ke dashboard...</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<style>
/* Modern Success Animation Styles */
.success-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.95), rgba(139, 195, 74, 0.95));
    backdrop-filter: blur(10px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.5s ease-out;
}

.success-container {
    position: relative;
    text-align: center;
    max-width: 400px;
    padding: 40px;
}

/* Floating particles */
.particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.particles::before,
.particles::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    animation: float 3s infinite ease-in-out;
}

.particles::before {
    top: 20%;
    left: 20%;
    animation-delay: 0s;
}

.particles::after {
    top: 80%;
    right: 20%;
    animation-delay: 1.5s;
}

/* Checkmark animation */
.checkmark-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 30px;
}

.checkmark-ripple {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: ripple 2s infinite;
}

.checkmark-circle {
    position: relative;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    animation: scaleIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.checkmark-svg {
    width: 80px;
    height: 80px;
}

.checkmark-circle-bg {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4CAF50;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    animation-delay: 0.3s;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke-width: 3;
    stroke: #4CAF50;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

/* Typography */
.success-title {
    color: white;
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 15px 0;
    animation: slideUp 0.6s ease-out 0.4s both;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.success-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 18px;
    font-weight: 400;
    margin: 0 0 40px 0;
    animation: slideUp 0.6s ease-out 0.6s both;
}

/* Loading indicator */
.loading-container {
    animation: slideUp 0.6s ease-out 0.8s both;
}

.loading-bar {
    width: 200px;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    margin: 0 auto 15px;
    overflow: hidden;
}

.loading-progress {
    height: 100%;
    background: linear-gradient(90deg, white, rgba(255, 255, 255, 0.8));
    border-radius: 2px;
    animation: loadingProgress 3s ease-in-out;
}

.loading-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
    margin: 0;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes ripple {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(2);
        opacity: 0;
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0;
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
        opacity: 1;
    }
}

@keyframes loadingProgress {
    0% {
        width: 0%;
    }
    70% {
        width: 80%;
    }
    100% {
        width: 100%;
    }
}

/* Responsive design */
@media (max-width: 480px) {
    .success-container {
        padding: 20px;
    }
    
    .success-title {
        font-size: 24px;
    }
    
    .success-subtitle {
        font-size: 16px;
    }
    
    .checkmark-circle {
        width: 60px;
        height: 60px;
    }
    
    .checkmark-svg {
        width: 60px;
        height: 60px;
    }
    
    .checkmark-ripple {
        width: 100px;
        height: 100px;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('depositForm');
        const successMessage = document.getElementById('deposit-success');
        const errorMessage = document.getElementById('deposit-error');
        const infoMessage = document.getElementById('deposit-info');
        const paymentSection = document.getElementById('payment-section');
        const timerElement = document.getElementById('timer');
        const paymentStatusElement = document.getElementById('payment-status');
        const successAnimationOverlay = document.getElementById('success-animation-overlay');
        
        // Toggle instructions
        const toggleInstructions = document.getElementById('toggleInstructions');
        const instructionsContent = document.getElementById('instructionsContent');
        const arrowIcon = document.getElementById('arrow-icon');
        
        if (toggleInstructions) {
            toggleInstructions.addEventListener('click', function() {
                if (instructionsContent.style.display === 'none') {
                    instructionsContent.style.display = 'block';
                    arrowIcon.textContent = '▲';
                } else {
                    instructionsContent.style.display = 'none';
                    arrowIcon.textContent = '▼';
                }
            });
        }
        
        // Hide messages after 5 seconds
        [successMessage, errorMessage, infoMessage].forEach(element => {
            if (element) {
                setTimeout(() => {
                    element.style.display = 'none';
                }, 5000);
            }
        });
        
        // If payment section exists, start checking payment status
        if (paymentSection) {
            // Hide the deposit form when showing payment
            form.style.display = 'none';
            
            let checkInterval;
            
            // Function to format time
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
            }
            
            // Function to show modern success animation
            function showSuccessAnimation() {
                // Show the overlay
                successAnimationOverlay.style.display = 'flex';
                
                // Add floating particles dynamically
                createFloatingParticles();
                
                // Play success sound if available
                playSuccessSound();
                
                // Redirect to dashboard after 4 seconds (longer to enjoy the animation)
                setTimeout(() => {
                    // Add fade out animation before redirect
                    successAnimationOverlay.style.animation = 'fadeOut 0.5s ease-in';
                    setTimeout(() => {
                        window.location.href = '{{ route("user.dashboard") }}';
                    }, 500);
                }, 4000);
            }
            
            // Function to create additional floating particles
            function createFloatingParticles() {
                const particlesContainer = document.querySelector('.particles');
                
                // Create multiple particles
                for (let i = 0; i < 15; i++) {
                    const particle = document.createElement('div');
                    particle.style.position = 'absolute';
                    particle.style.width = Math.random() * 8 + 4 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.background = `rgba(255, 255, 255, ${Math.random() * 0.8 + 0.2})`;
                    particle.style.borderRadius = '50%';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animation = `float ${Math.random() * 3 + 2}s infinite ease-in-out`;
                    particle.style.animationDelay = Math.random() * 2 + 's';
                    
                    particlesContainer.appendChild(particle);
                }
            }
            
            // Function to play success sound (optional)
            function playSuccessSound() {
                try {
                    // Create audio context for success sound
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Create a simple success chime
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.setValueAtTime(523.25, audioContext.currentTime); // C5
                    oscillator.frequency.setValueAtTime(659.25, audioContext.currentTime + 0.1); // E5
                    oscillator.frequency.setValueAtTime(783.99, audioContext.currentTime + 0.2); // G5
                    
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.5);
                } catch (error) {
                    // Silently fail if audio context is not supported
                    console.log('Audio not supported');
                }
            }
            
            // Function to check payment status
            function checkPaymentStatus() {
                fetch('{{ route("user.deposit.check-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        clearInterval(checkInterval);
                        showSuccessAnimation();
                    } else if (data.status === 'expired') {
                        clearInterval(checkInterval);
                        paymentStatusElement.innerHTML = '<div style="color: #e17055; font-weight: bold;">Pembayaran Kedaluwarsa</div>';
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else if (data.error) {
                        paymentStatusElement.innerHTML = `<div style="color: #e17055; font-weight: bold;">${data.error}</div>`;
                    } else {
                        // Hanya set timer awal jika belum diinisialisasi
                        if (!window.countdownInitialized && data.timeLeft) {
                            initializeCountdown(data.timeLeft);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                });
            }
            
            // Initialize countdown timer
            function initializeCountdown(seconds) {
                window.countdownInitialized = true;
                let timeLeft = seconds;
                
                // Update timer immediately
                timerElement.textContent = `Waktu tersisa: ${formatTime(timeLeft)}`;
                
                // Set interval for countdown
                const timerInterval = setInterval(() => {
                    timeLeft--;
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        timerElement.textContent = 'Waktu tersisa: 0:00';
                        timerElement.style.color = '#e17055';
                        paymentStatusElement.innerHTML = '<div style="color: #e17055; font-weight: bold;">Pembayaran Kedaluwarsa</div>';
                        
                        // Reload page after 3 seconds
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                        return;
                    }
                    
                    timerElement.textContent = `Waktu tersisa: ${formatTime(timeLeft)}`;
                    
                    // Change color to red when less than 1 minute remaining
                    if (timeLeft < 60) {
                        timerElement.style.color = '#e17055';
                    }
                }, 1000);
            }
            
            // Check payment status every 10 seconds
            checkInterval = setInterval(checkPaymentStatus, 10000);
            
            // Check immediately on page load
            checkPaymentStatus();
        }
    });
</script>
@endsection