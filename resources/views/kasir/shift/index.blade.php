@extends('kasir.layouts.app', $data ?? [])

@section('content')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0; }
    
    .day-container { padding: 24px; border-bottom: 1px solid var(--border-color); }
    .day-container:last-child { border-bottom: none; }
    .day-title { font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    
    .shifts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
    .shift-box { border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #f8fafc; position: relative; }
    .shift-box.libur { background: #fee2e2; border-color: #fca5a5; }
    .shift-name { font-weight: 700; font-size: 15px; color: var(--text-main); margin-bottom: 4px; }
    .shift-time { font-size: 13px; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 4px; }
    
    .employee-list { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
    .employee-item { display: flex; align-items: center; background: white; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; gap: 8px; }
    
    .empty-state { text-align: center; padding: 20px; font-size: 13px; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 8px; }
</style>

<div style="padding: 24px; max-width: 1000px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Jadwal Shift Karyawan</h2>
            <span style="font-size: 14px; color: var(--text-muted);">Hanya Admin yang dapat merubah jadwal</span>
        </div>
        
        <div>
            @php
                $haris = array_keys($hari_urutan);
            @endphp
            @foreach($haris as $hari)
                @php
                    $shiftsHariIni = isset($groupedShifts[$hari]) ? $groupedShifts[$hari] : collect();
                @endphp
                <div class="day-container">
                    <div class="day-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Hari {{ $hari }}
                    </div>
                    
                    @if($shiftsHariIni->isEmpty())
                        <div class="empty-state">Belum ada sesi shift di hari {{ $hari }}.</div>
                    @else
                        <div class="shifts-grid">
                            @foreach($shiftsHariIni as $shift)
                                <div class="shift-box {{ $shift->is_libur ? 'libur' : '' }}">
                                    <div class="shift-name">{{ $shift->nama }} {!! $shift->is_libur ? '<span style="color:#ef4444; font-size:12px;">(Libur)</span>' : '' !!}</div>
                                    @if(!$shift->is_libur)
                                        <div class="shift-time">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}
                                        </div>
                                    @endif

                                    <div style="margin-top: 16px;">
                                        <div style="font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px;">KARYAWAN BERTUGAS:</div>
                                        <div class="employee-list">
                                            @foreach($shift->users as $u)
                                                <div class="employee-item {{ $u->id == auth()->id() ? 'my-shift' : '' }}" style="{{ $u->id == auth()->id() ? 'border-color: #0ea5e9; background: #f0f9ff;' : '' }}">
                                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: {{ $u->id == auth()->id() ? '#0ea5e9' : '#e2e8f0' }}; color: {{ $u->id == auth()->id() ? 'white' : 'black' }}; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    </div>
                                                    <span style="{{ $u->id == auth()->id() ? 'font-weight: bold; color: #0369a1;' : '' }}">
                                                        {{ $u->name }} {{ $u->id == auth()->id() ? '(Anda)' : '' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if($shift->users->isEmpty())
                                                <div style="font-size: 12px; color: #94a3b8; font-style: italic;">Belum ada karyawan.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
