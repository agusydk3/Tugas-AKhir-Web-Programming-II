@extends('admin.layouts.app')

@section('title', 'Detail Transaksi')

@section('css')
<link rel="stylesheet" href="{{ asset('css/transaction-detail.css') }}">
@endsection

@section('content')
<div class="container">
    <a href="{{ route('admin.transactions') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    
    <div class="transaction-header">
        <div class="transaction-id">{{ $transaction->transaction_id }}</div>
        <div class="transaction-date">{{ $transaction->created_at->format('d M Y, H:i') }}</div>
        <div class="transaction-status status-{{ $transaction->status }}">
            @if($transaction->status == 'completed')
                Completed
            @elseif($transaction->status == 'pending')
                Pending
            @elseif($transaction->status == 'failed')
                Failed
            @elseif($transaction->status == 'cancelled')
                Cancelled
            @endif
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Informasi Pelanggan</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nama</div>
                <div class="info-value">{{ $transaction->user->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $transaction->user->email }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">User ID</div>
                <div class="info-value">{{ $transaction->user->id }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Role</div>
                <div class="info-value">{{ $transaction->user->getRoleName() }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Informasi Produk</h3>
        <div class="product-card">
            <div class="product-image">
                @if($transaction->product->image)
                    <img src="{{ Storage::url($transaction->product->image) }}" alt="{{ $transaction->product->name }}">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 24px; font-weight: bold;">
                        {{ strtoupper(substr($transaction->product->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="product-details">
                <div class="product-name">{{ $transaction->product->name }}</div>
                <div class="product-price">{{ $transaction->product->formatted_price }} / unit</div>
                <div class="product-description">
                    {{ $transaction->product->description ?? 'Tidak ada deskripsi' }}
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Detail Pesanan</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Jumlah</div>
                <div class="info-value">{{ $transaction->quantity }} unit</div>
            </div>
            <div class="info-item">
                <div class="info-label">Harga Satuan</div>
                <div class="info-value">{{ $transaction->product->formatted_price }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Pembayaran</div>
                <div class="info-value total-amount">{{ $transaction->formatted_total }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value">
                    <span class="payment-badge payment-{{ $transaction->payment_method }}">
                        {{ strtoupper($transaction->payment_method) }}
                    </span>
                </div>
            </div>
            @if($transaction->receipt_number)
            <div class="info-item">
                <div class="info-label">Nomor Receipt</div>
                <div class="info-value">{{ $transaction->receipt_number }}</div>
            </div>
            @endif
            @if($transaction->expired_at)
            <div class="info-item">
                <div class="info-label">Kedaluwarsa Pada</div>
                <div class="info-value">{{ $transaction->expired_at->format('d M Y, H:i') }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Nomor Serial</h3>
        @if($transaction->stocks->count() > 0)
        <div class="serial-list">
            @foreach($transaction->stocks as $stock)
            <div class="serial-item">
                <div class="serial-number">{{ $stock->serial_number }}</div>
                <div>
                    @if($stock->is_sold)
                    <span class="payment-badge payment-saldo">Terjual</span>
                    @else
                    <span class="payment-badge">Tersedia</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="no-serials">
            @if($transaction->status == 'completed')
            <p>Tidak ada nomor serial yang ditetapkan untuk transaksi ini.</p>
            @else
            <p>Nomor serial akan ditetapkan ketika transaksi selesai.</p>
            @endif
        </div>
        @endif
    </div>

    <div class="section">
        <h3 class="section-title">Aksi</h3>
        <div class="action-buttons">
            <button class="btn btn-primary" id="updateStatusBtn">
                <i class="fas fa-edit"></i> Ubah Status
            </button>
            
            @if($transaction->status == 'completed')
            <button class="btn btn-success" id="printReceiptBtn">
                <i class="fas fa-print"></i> Cetak Receipt
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal untuk update status -->
<div class="modal-backdrop" id="statusModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Ubah Status Transaksi</div>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="updateStatusForm">
                @csrf
                <input type="hidden" id="transactionId" value="{{ $transaction->id }}">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $transaction->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $transaction->status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ $transaction->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="saveStatus">Simpan</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal elements
        const modal = document.getElementById('statusModal');
        const updateStatusBtn = document.getElementById('updateStatusBtn');
        const closeModalBtn = document.getElementById('closeModal');
        const saveStatusBtn = document.getElementById('saveStatus');
        
        // Show modal
        updateStatusBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });
        
        // Close modal
        closeModalBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Save status changes
        saveStatusBtn.addEventListener('click', function() {
            const transactionId = document.getElementById('transactionId').value;
            const newStatus = document.getElementById('status').value;
            
            fetch(`/admin/transactions/${transactionId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and reload page
                    modal.style.display = 'none';
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui status');
            });
        });
        
        // Print receipt
        const printReceiptBtn = document.getElementById('printReceiptBtn');
        if (printReceiptBtn) {
            printReceiptBtn.addEventListener('click', function() {
                window.print();
            });
        }
    });
</script>
@endsection 