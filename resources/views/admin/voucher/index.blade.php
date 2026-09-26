@extends('admin.layouts.app', $data ?? ['title' => 'Voucher Promo'])

@section('content')
<style>
    .btn-primary { background: var(--text-main); color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); }
    .data-table th, .data-table td { padding: 16px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    .data-table th { background: var(--bg-color); font-weight: 600; color: var(--text-main); }
    .btn-action { padding: 6px 12px; font-size: 13px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-main); display: inline-block; }
    .btn-action:hover { background: var(--bg-color); }
    .badge-status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Daftar Voucher Promo</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Kelola voucher diskon untuk pelanggan.</p>
    </div>
    <a href="{{ route('admin.penjualan.voucher.create') }}" class="btn-primary">Tambah Voucher</a>
</div>

<div style="padding: 24px 40px 40px;">
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Promo</th>
                <th>Diskon</th>
                <th>Masa Berlaku</th>
                <th>Statistik</th>
                <th>Status</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers as $item)
                <tr>
                    <td style="font-weight: 600; font-family: monospace;">{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>
                        {{ $item->tipe_diskon === 'persen' ? round($item->nilai_diskon) . '%' : 'Rp ' . number_format($item->nilai_diskon, 0, ',', '.') }}
                        @if($item->minimal_belanja)
                            <br><small style="color: var(--text-muted);">Min. Rp {{ number_format($item->minimal_belanja, 0, ',', '.') }}</small>
                        @endif
                    </td>
                    <td style="font-size: 13px;">
                        @if($item->tanggal_mulai && $item->tanggal_selesai)
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                        @elseif($item->tanggal_mulai)
                            Mulai: {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                        @elseif($item->tanggal_selesai)
                            Sampai: {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                        @else
                            Selamanya
                        @endif

                        @if($item->hari_berlaku && count($item->hari_berlaku) > 0)
                            <br><small style="color: var(--text-muted);">Hari: {{ implode(', ', $item->hari_berlaku) }}</small>
                        @endif

                        @if($item->jam_mulai || $item->jam_selesai)
                            <br><small style="color: var(--text-muted);">Jam: {{ $item->jam_mulai ? \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') : '00:00' }} - {{ $item->jam_selesai ? \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '23:59' }}</small>
                        @endif
                    </td>
                    <td style="font-size: 13px;">
                        <div>Terpakai: <strong>{{ $item->terpakai }} kali</strong></div>
                        @if($item->kuota !== null)
                            <div style="color: var(--text-muted); margin-top: 4px;">Sisa Kuota: {{ $item->kuota }}</div>
                        @else
                            <div style="color: var(--text-muted); margin-top: 4px;">Tanpa batas kuota</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status {{ $item->status ? 'status-active' : 'status-inactive' }}">
                            {{ $item->status ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a href="{{ route('admin.penjualan.voucher.show', $item->id) }}" class="btn-action" style="border-color: #3b82f6; color: #2563eb;">Detail</a>
                            <a href="{{ route('admin.penjualan.voucher.edit', $item->id) }}" class="btn-action">Edit</a>
                            <form action="{{ route('admin.penjualan.voucher.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus voucher ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action" style="color: #ef4444; border-color: #fca5a5; cursor: pointer; background: transparent;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        Belum ada data voucher promo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
