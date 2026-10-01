@extends('admin.layouts.app', $data ?? [])

@section('content')
<style>
    .timeline { position: relative; padding-left: 32px; margin-top: 24px; }
    .timeline::before {
        content: ''; position: absolute; left: 11px; top: 0; bottom: 0;
        width: 2px; background: #e2e8f0;
    }
    .timeline-item { position: relative; margin-bottom: 24px; }
    .timeline-icon {
        position: absolute; left: -32px; top: 0; width: 24px; height: 24px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        color: white; border: 4px solid white; background: var(--icon-color, #94a3b8);
    }
    .timeline-content {
        background: white; border: 1px solid var(--border-color);
        border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .timeline-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 8px;
    }
    .timeline-title { font-weight: 700; color: var(--text-main); font-size: 15px; }
    .timeline-time { font-size: 12px; color: var(--text-muted); font-weight: 500; }
    .timeline-desc { font-size: 14px; color: var(--text-muted); line-height: 1.5; }
    .timeline-user {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 12px; font-size: 12px; font-weight: 600; color: #475569;
        background: #f1f5f9; padding: 4px 10px; border-radius: 20px;
    }
</style>

<div class="page-header" style="padding: 32px 40px 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Log Aktivitas Sistem</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 8px;">Pantau semua aktivitas transaksi, absensi, dan pengeluaran secara terpusat.</p>
    </div>
</div>

<div style="padding: 24px 40px 40px; max-width: 800px;">
    @if(count($activities) > 0)
        <div class="timeline">
            @foreach($activities as $act)
            <div class="timeline-item">
                <div class="timeline-icon" style="--icon-color: {{ $act['color'] }};">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $act['icon'] !!}</svg>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <div class="timeline-title">{{ $act['title'] }}</div>
                        <div class="timeline-time">{{ \Carbon\Carbon::parse($act['date'])->diffForHumans() }}</div>
                    </div>
                    <div class="timeline-desc">{{ $act['description'] }}</div>
                    <div class="timeline-user">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ $act['user'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; border: 1px dashed var(--border-color); color: var(--text-muted);">
            Belum ada aktivitas terekam.
        </div>
    @endif
</div>
@endsection
