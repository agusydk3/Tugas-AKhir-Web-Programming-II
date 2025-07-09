@extends('layouts.user-dashboard')

@section('title', 'Kontak - Dashboard Customer')

@section('page-title', 'Kontak')

@section('content')
<div class="section-card">
    <h2 class="section-title">Kontak & Bantuan</h2>
    <p>Hubungi kami melalui:</p>
    <ul style="margin: 15px 0 20px 20px;">
        <li style="margin-bottom: 10px;">Email: <a href="mailto:support@digital.com" style="color: #667eea; text-decoration: none;">support@digital.com</a></li>
        <li style="margin-bottom: 10px;">WhatsApp: <a href="https://wa.me/6281234567890" target="_blank" style="color: #667eea; text-decoration: none;">0812-3456-7890</a></li>
        <li style="margin-bottom: 10px;">Telegram: <a href="https://t.me/digitalsupport" target="_blank" style="color: #667eea; text-decoration: none;">@digitalsupport</a></li>
    </ul>
    
    <p>Jam operasional layanan pelanggan:</p>
    <ul style="margin: 15px 0 20px 20px;">
        <li style="margin-bottom: 5px;">Senin - Jumat: 08.00 - 21.00 WIB</li>
        <li style="margin-bottom: 5px;">Sabtu: 09.00 - 18.00 WIB</li>
        <li style="margin-bottom: 5px;">Minggu & Hari Libur: 10.00 - 17.00 WIB</li>
    </ul>
</div>

<div class="section-card">
    <h2 class="section-title">Kirim Pesan</h2>
    <form action="{{ route('user.contact.send') }}" method="POST">
        @csrf
        <div class="deposit-form-group">
            <label class="deposit-label" for="subject">Subjek</label>
            <input type="text" id="subject" class="deposit-input" name="subject" placeholder="Masukkan subjek pesan" required>
        </div>
        
        <div class="deposit-form-group">
            <label class="deposit-label" for="message">Pesan</label>
            <textarea id="message" class="deposit-input" name="message" rows="5" placeholder="Tuliskan pesan Anda di sini..." required style="resize: vertical;"></textarea>
        </div>
        
        @if(session('success'))
            <div style="margin-bottom: 15px; color:#00b894; font-weight:600;">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div style="margin-bottom: 15px; color:#e17055; font-weight:600;">
                {{ session('error') }}
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
        
        <button type="submit" class="deposit-btn">Kirim Pesan</button>
    </form>
</div>

<div class="section-card">
    <h2 class="section-title">FAQ</h2>
    
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 600; color: #2d3436; margin-bottom: 8px;">Bagaimana cara melakukan deposit?</h3>
        <p style="color: #636e72; font-size: 14px;">Anda dapat melakukan deposit melalui menu Deposit dengan memilih metode pembayaran yang tersedia seperti QRIS, transfer bank, atau e-wallet.</p>
    </div>
    
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 600; color: #2d3436; margin-bottom: 8px;">Berapa lama proses pembelian produk?</h3>
        <p style="color: #636e72; font-size: 14px;">Proses pembelian produk digital biasanya selesai dalam waktu 5-10 menit setelah pembayaran berhasil diverifikasi.</p>
    </div>
    
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 600; color: #2d3436; margin-bottom: 8px;">Bagaimana jika produk yang saya beli bermasalah?</h3>
        <p style="color: #636e72; font-size: 14px;">Kami memberikan garansi untuk semua produk. Jika mengalami masalah, segera hubungi tim support kami melalui kontak yang tersedia.</p>
    </div>
    
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 600; color: #2d3436; margin-bottom: 8px;">Apakah ada biaya tambahan untuk deposit?</h3>
        <p style="color: #636e72; font-size: 14px;">Tidak ada biaya tambahan untuk deposit. Jumlah yang Anda transfer akan masuk 100% ke saldo akun Anda.</p>
    </div>
</div>
@endsection 