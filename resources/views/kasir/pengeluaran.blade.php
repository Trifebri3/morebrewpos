@extends('kasir.layouts.app')

@section('content')
<style>
    .container { max-width: 800px; margin: 0 auto; padding: 32px 20px; }
    .header-box { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .budget-info { display: flex; flex-direction: column; }
    .budget-label { font-size: 14px; color: var(--text-muted); font-weight: 500; }
    .budget-amount { font-size: 24px; font-weight: 700; color: #16a34a; }
    .budget-amount.warning { color: #dc2626; }
    
    .card { background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 16px; background: #fafafa; }
    .card-body { padding: 24px; }
    
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: var(--text-main); }
    .form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; outline: none; }
    
    .btn-submit { background: var(--text-main); color: white; padding: 12px 24px; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; width: 100%; margin-top: 8px; }
    
    .history-item { padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .history-item:last-child { border-bottom: none; }
    .history-title { font-weight: 600; font-size: 14px; color: var(--text-main); }
    .history-desc { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
    .history-amount { font-weight: 600; font-size: 15px; text-align: right; }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-top: 4px; display: inline-block; }
    .badge-pending { background: #fef08a; color: #854d0e; }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
</style>

<div class="container">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('kasir.dashboard') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500;">&larr; Kembali ke POS</a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="header-box">
        <div>
            <h1 style="font-size: 24px; margin-bottom: 4px;">Catat Belanja Harian</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Laporkan pengeluaran mendadak/kas kecil.</p>
        </div>
        <div class="budget-info">
            <span class="budget-label">Sisa Anggaran Hari Ini</span>
            <span class="budget-amount {{ $sisaBudget < 100000 ? 'warning' : '' }}">Rp {{ number_format($sisaBudget, 0, ',', '.') }}</span>
        </div>
    </div>

    <div style="display: flex; gap: 24px; align-items: flex-start;">
        <div class="card" style="flex: 1;">
            <div class="card-header">Form Laporan Belanja</div>
            <div class="card-body">
                <form action="{{ route('kasir.pengeluaran.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama Barang / Keperluan</label>
                        <input type="text" name="nama_item" class="form-control" required placeholder="Cth: Beli Es Batu">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" required min="1" max="{{ $sisaBudget }}" placeholder="Cth: 50000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Cth: Di warung Pak Budi">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bukti Foto / Nota (Opsional, max 2MB otomatis dikompress)</label>
                        <input type="file" id="bukti_file" class="form-control" accept="image/*">
                        <input type="hidden" name="bukti_base64" id="bukti_base64">
                        <small style="color: var(--text-muted); font-size: 12px; margin-top: 4px; display: block;">File akan otomatis diperkecil sebelum diupload.</small>
                    </div>
                    <button type="submit" id="btnSubmit" class="btn-submit" {{ $sisaBudget <= 0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' }}>Kirim Laporan</button>
                </form>

                <script>
                    document.getElementById('bukti_file').addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function(e) {
                            const img = new Image();
                            img.src = e.target.result;
                            img.onload = function() {
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');

                                // Set maximum dimensions
                                const MAX_WIDTH = 1200;
                                const MAX_HEIGHT = 1200;
                                let width = img.width;
                                let height = img.height;

                                if (width > height) {
                                    if (width > MAX_WIDTH) {
                                        height *= MAX_WIDTH / width;
                                        width = MAX_WIDTH;
                                    }
                                } else {
                                    if (height > MAX_HEIGHT) {
                                        width *= MAX_HEIGHT / height;
                                        height = MAX_HEIGHT;
                                    }
                                }

                                canvas.width = width;
                                canvas.height = height;
                                ctx.drawImage(img, 0, 0, width, height);

                                // Compress and set to hidden input (0.7 quality)
                                const dataurl = canvas.toDataURL('image/jpeg', 0.7);
                                document.getElementById('bukti_base64').value = dataurl;
                            };
                        };
                    });
                </script>
            </div>
        </div>

        <div class="card" style="flex: 1;">
            <div class="card-header">Riwayat Laporan Anda</div>
            <div style="max-height: 400px; overflow-y: auto;">
                @forelse($riwayat as $item)
                    <div class="history-item">
                        <div>
                            <div class="history-title">{{ $item->nama_item }}</div>
                            <div class="history-desc">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }} - {{ $item->keterangan ?: 'Tanpa keterangan' }}</div>
                            <span class="badge badge-{{ $item->status }}">{{ strtoupper($item->status) }}</span>
                        </div>
                        <div class="history-amount">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div style="padding: 40px 20px; text-align: center; color: var(--text-muted); font-size: 14px;">
                        Belum ada riwayat pengeluaran.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
