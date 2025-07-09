@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-description">Selamat datang di panel admin</p>
</div>

<div class="cards-grid">
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Total Pengguna</h3>
                <div class="card-value">{{ number_format($totalUsers) }}</div>
                <p class="card-description">Customer dan Reseller</p>
            </div>
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Transaksi Berhasil</h3>
                <div class="card-value">{{ number_format($successfulTransactions) }}</div>
                <p class="card-description">Total transaksi sukses</p>
            </div>
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Produk</h3>
                <div class="card-value">{{ number_format($totalProducts) }}</div>
                <p class="card-description">Total produk di toko</p>
            </div>
            <div class="card-icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Pendapatan</h3>
                <div class="card-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <p class="card-description">Total pendapatan dari semua penjualan</p>
            </div>
            <div class="card-icon" style="font-weight: bold; font-size: 1.2rem;">
                Rp
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">Transaksi Terbaru</h3>
        <div class="search-box">
            <a href="{{ route('admin.transactions') }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Lihat Semua
            </a>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th>Total</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $transaction)
            <tr>
                <td>#{{ $transaction->id }}</td>
                <td>{{ $transaction->user->name }}</td>
                <td>{{ $transaction->product->name }}</td>
                <td>{{ $transaction->getFormattedTotal() }}</td>
                <td>
                    <span class="status-badge" style="background: {{ $transaction->payment_method === 'qris' ? '#e0f2fe' : '#f3e5f5' }}; color: {{ $transaction->payment_method === 'qris' ? '#0277bd' : '#7b1fa2' }};">
                        {{ strtoupper($transaction->payment_method) }}
                    </span>
                </td>
                <td>
                    <span class="status-badge status-{{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn-icon">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 