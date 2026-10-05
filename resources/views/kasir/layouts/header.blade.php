        @php $kedaiData = \App\Models\Kedai::first(); @endphp
        <script>
            window.kedaiInfo = {
                wifiSsid: "{{ $kedaiData ? $kedaiData->wifi_ssid : '' }}",
                wifiPassword: "{{ $kedaiData ? $kedaiData->wifi_password : '' }}",
                instagram: "{{ $kedaiData ? $kedaiData->instagram : '' }}"
            };
        </script>
        <header class="top-bar">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                <a href="{{ route('kasir.pengaturan') }}" style="text-decoration: none; padding: 8px 14px; background: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    ⚙️ Pengaturan & Pajak
                </a>
                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; padding: 8px 16px; background: #fef3c7; color: #d97706; border-radius: 8px; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Mode Admin
                </a>
            </div>
        </header>