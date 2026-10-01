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
            <div class="header-actions" style="display: flex; align-items: center; gap: 24px; margin-left: auto;">
                <button onclick="thermalPrinter.connectBluetooth().then(res => { if(res) { document.getElementById('bt-status').innerHTML = '✅ BT Terhubung'; document.getElementById('bt-status').style.color = 'green'; } })" style="padding: 8px 16px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span id="bt-status">Bluetooth (Mobile)</span>
                </button>
                <button onclick="thermalPrinter.connectSerial().then(res => { if(res) { document.getElementById('serial-status').innerHTML = '✅ Serial Terhubung'; document.getElementById('serial-status').style.color = 'green'; } })" style="padding: 8px 16px; background: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span id="serial-status">Serial COM (PC)</span>
                </button>
                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; padding: 8px 16px; background: #fef3c7; color: #d97706; border-radius: 8px; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Mode Admin
                </a>
            </div>
        </header>