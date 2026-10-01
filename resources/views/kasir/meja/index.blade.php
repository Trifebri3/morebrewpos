@extends('kasir.layouts.app', $data ?? [])

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary:hover { opacity: 0.9; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .btn-action { padding: 6px 12px; font-size: 13px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-main); display: inline-block; cursor: pointer; background: transparent; }
    .btn-action:hover { background: var(--bg-color); }
    .btn-action-primary { border-color: #3b82f6; color: #2563eb; }
    .btn-action-primary:hover { background: #eff6ff; }

    /* Modal Styles */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-container { background: white; border-radius: 8px; width: 400px; max-width: 90%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    .modal-title { margin-top: 0; font-size: 18px; margin-bottom: 16px; }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Daftar Status Meja</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Daftar meja yang tersedia untuk pelanggan dan QR Codenya.</p>
    </div>
</div>

<div style="padding: 24px 40px 40px;">
    <div style="background: white; border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="80">No</th>
                    <th>Nama / Nomor Meja</th>
                    <th>Status</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mejas as $i => $meja)
                <tr>
                    <td style="color: var(--text-muted);">{{ $i + 1 }}</td>
                    <td style="font-weight: 600; color: var(--text-main);">{{ $meja->name }}</td>
                    <td>
                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Tersedia</span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-action btn-action-primary" onclick="showQr('{{ $meja->name }}', '{{ route('kasir.meja.qr', $meja->id) }}')">Lihat QR</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        Belum ada data meja. Menunggu admin untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tampil QR -->
<div id="modal-qr" class="modal-overlay" onclick="if(event.target === this) this.style.display='none'">
    <div class="modal-container" style="text-align: center;">
        <h3 class="modal-title" id="qr-title">QR Code</h3>
        <div style="display: flex; justify-content: center; margin-bottom: 16px;">
            <img id="qr-image" src="" alt="QR Code" style="width: 250px; height: 250px; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; background: #f8fafc;">
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">Pelanggan dapat melakukan scan QR code ini untuk memesan.</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button type="button" onclick="document.getElementById('modal-qr').style.display='none'" class="btn-action">Tutup</button>
            <a id="qr-download" href="#" target="_blank" download="qrcode.svg" class="btn-primary" style="margin: 0; border-color: #3b82f6;">Unduh QR (SVG)</a>
        </div>
    </div>
</div>

<script>
    function showQr(name, url) {
        document.getElementById('qr-title').innerText = 'QR Code ' + name;
        document.getElementById('qr-image').src = url;
        document.getElementById('qr-download').href = url;
        document.getElementById('modal-qr').style.display = 'flex';
    }
</script>
@endsection
