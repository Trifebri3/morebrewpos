@extends('kasir.layouts.app', $data ?? [])

@section('content')
<div class="page-header" style="padding: 32px 40px 0;">
    <h1>{{ $title ?? 'Modul' }}</h1>
</div>

<div style="padding: 40px; text-align: center; color: var(--text-muted); margin-top: 40px;">
    <h3 style="font-size: 24px; color: var(--text-main); margin-bottom: 8px;">Upss!</h3>
    <p style="font-size: 16px;">Masih on progress ya...</p>
    <a href="{{ route('kasir.dashboard') }}" style="display: inline-block; margin-top: 24px; color: white; background: var(--text-main); padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500;">Kembali ke Dashboard</a>
</div>
@endsection
