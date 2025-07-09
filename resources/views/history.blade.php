@extends('layouts.user-dashboard')

@section('title', 'Riwayat - Dashboard Customer')

@section('page-title', 'Riwayat')

@section('content')
<div class="section-card">
    <h2 class="section-title">Riwayat Order</h2>
    <table class="order-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Status</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->created_at->format('d-m-Y') }}</td>
                    <td>{{ $order->product->name }}</td>
                    <td>
                        @if($order->status == 'completed')
                            <span class="status-badge status-success">Berhasil</span>
                        @elseif($order->status == 'pending')
                            <span class="status-badge status-pending">Diproses</span>
                        @elseif($order->status == 'failed')
                            <span class="status-badge status-failed">Gagal</span>
                        @elseif($order->status == 'cancelled')
                            <span class="status-badge status-failed">Dibatalkan</span>
                        @endif
                    </td>
                    <td>{{ $order->formatted_total }}</td>
                    <td>
                        @if($order->status == 'completed')
                            <a href="{{ route('user.order.success', $order->transaction_id) }}" class="btn-link">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                        @elseif($order->status == 'pending')
                            <a href="{{ route('user.order.pending', $order->transaction_id) }}" class="btn-link">
                                <i class="fas fa-spinner"></i> Proses
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada riwayat order</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($orders->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $orders->links() }}
        </div>
    @endif
</div>



<style>
.btn-link {
    color: #007bff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-link:hover {
    text-decoration: underline;
}

.text-muted {
    color: #6c757d;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.order-table th,
.order-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.order-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.order-table tr:last-child td {
    border-bottom: none;
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.status-success {
    background: #d4edda;
    color: #155724;
}

.status-badge.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.status-failed {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .order-table {
        display: block;
        overflow-x: auto;
    }
}
</style>
@endsection 