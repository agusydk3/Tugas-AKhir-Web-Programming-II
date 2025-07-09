@extends('layouts.user-dashboard')

@section('title', 'Dashboard Customer')

@section('page-title', 'Dasbor')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Total Saldo</div>
            <div class="stat-icon" style="background: #00b894;">💰</div>
        </div>
        <div class="stat-value">{{ Auth::user()->getFormattedBalance() }}</div>
        <a href="{{ route('user.deposit') }}" class="stat-action">Top Up Saldo</a>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Total Order</div>
            <div class="stat-icon" style="background: #0984e3;">📦</div>
        </div>
        <div class="stat-value">{{ $totalOrders }}</div>
        <a href="{{ route('user.history') }}" class="stat-action">Lihat Riwayat</a>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Order Berhasil</div>
            <div class="stat-icon" style="background: #00b894;">✅</div>
        </div>
        <div class="stat-value">{{ $successRate }}%</div>
        <span class="stat-action">Tingkat Keberhasilan</span>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-title">Produk Tersedia</div>
            <div class="stat-icon" style="background: #fdcb6e;">🛒</div>
        </div>
        <div class="stat-value">{{ \App\Models\Product::where('is_active', true)->count() }}</div>
        <a href="{{ route('user.product') }}" class="stat-action">Lihat Produk</a>
    </div>
</div>

<div class="activity-card full-width">
        <h3 class="card-title">Aktivitas Terbaru</h3>
        <div class="activity-list">
        @forelse($recentActivities as $activity)
                <div class="activity-item">
                <div class="activity-icon" style="background: {{ $activity['icon_color'] }};">{{ $activity['icon'] }}</div>
                    <div class="activity-content">
                    <h5>{{ $activity['title'] }}</h5>
                    <p>{{ $activity['description'] }} - {{ $activity['time_ago'] }}</p>
                    </div>
                </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h4>Belum Ada Aktivitas</h4>
                <p>Aktivitas Anda akan muncul di sini setelah Anda melakukan transaksi.</p>
                <a href="{{ route('user.product') }}" class="btn btn-primary">Mulai Belanja</a>
                </div>
            @endforelse
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.stat-title {
    font-size: 14px;
    color: #6c757d;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 15px;
}

.stat-action {
    margin-top: auto;
    color: #007bff;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}

.stat-action:hover {
    text-decoration: underline;
}

.activity-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.full-width {
    grid-column: 1 / -1;
}

.card-title {
    font-size: 18px;
    color: #343a40;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f1f1f1;
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.activity-content h5 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #343a40;
}

.activity-content p {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
}

.empty-state {
    text-align: center;
    padding: 30px;
    color: #6c757d;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.empty-state h4 {
    margin: 0 0 10px 0;
    color: #343a40;
}

.empty-state p {
    margin: 0 0 20px 0;
}

.btn-primary {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #0069d9;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
