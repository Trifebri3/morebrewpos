        @php $kedaiData = \App\Models\Kedai::first(); @endphp
        <script>
            window.kedaiInfo = {
                wifiSsid: "{{ $kedaiData ? $kedaiData->wifi_ssid : '' }}",
                wifiPassword: "{{ $kedaiData ? $kedaiData->wifi_password : '' }}",
                instagram: "{{ $kedaiData ? $kedaiData->instagram : '' }}"
            };
        </script>
        <header class="top-bar" style="position: relative; z-index: 50;">
            <style>
                .profile-dropdown-wrapper { position: relative; }
                .profile-dropdown-btn:hover { background: rgba(0,0,0,0.04); border-radius: 8px; }
                .dropdown-item-link {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 9px 12px;
                    border-radius: 6px;
                    color: #334155;
                    text-decoration: none;
                    font-size: 13px;
                    font-weight: 500;
                    transition: all 0.15s ease;
                }
                .dropdown-item-link:hover {
                    background-color: #f1f5f9;
                    color: #0f172a;
                }
            </style>
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                <a href="{{ route('kasir.pengaturan') }}" class="{{ request()->routeIs('kasir.pengaturan*') ? 'active-header-link' : '' }}" style="text-decoration: none; padding: 8px 16px; background: {{ request()->routeIs('kasir.pengaturan*') ? '#0f172a' : '#ffffff' }}; color: {{ request()->routeIs('kasir.pengaturan*') ? '#ffffff' : '#0f172a' }}; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ request()->routeIs('kasir.pengaturan*') ? '#ffffff' : '#000000' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    <span>Pengaturan & Pajak</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; padding: 8px 16px; background: #ffffff; color: #0f172a; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <svg width="16" height="16" fill="none" stroke="#000000" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    <span>Mode Admin</span>
                </a>

                {{-- Direct Keluar / Logout Button --}}
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Keluar dari sistem (Log Out)" style="padding: 8px 14px; background: #ffffff; color: #dc2626; border: 1px solid #fecdd3; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.03);" onmouseover="this.style.background='#fff1f2'; this.style.borderColor='#fda4af';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#fecdd3';">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar</span>
                    </button>
                </form>

                {{-- Profile Dropdown Wrapper --}}
                <div class="profile-dropdown-wrapper">
                    <div class="profile-dropdown profile-dropdown-btn" onclick="toggleHeaderDropdown('kasir-user-menu')" style="display: flex; align-items: center; gap: 10px; cursor:pointer; padding: 6px 10px; transition: background 0.2s; user-select: none;">
                        <div style="width: 36px; height: 36px; background: #0f172a; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                            {{ strtoupper(substr(auth()->user()->name ?? ($dummyUser ?? 'K'), 0, 1)) }}
                        </div>
                        <div style="display: flex; flex-direction: column; text-align: left;">
                            <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main); line-height: 1.2;">{{ auth()->user()->name ?? ($dummyUser ?? 'Kasir') }}</span>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: capitalize;">{{ auth()->user()->role ?? 'Kasir Terminal' }}</span>
                        </div>
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted); margin-left: 2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    {{-- Dropdown Menu Popover --}}
                    <div id="kasir-user-menu" class="profile-dropdown-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 240px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06); padding: 8px; z-index: 9999;">
                        <div style="padding: 10px 12px 8px; border-bottom: 1px solid #f1f5f9; margin-bottom: 6px;">
                            <p style="font-size: 13.5px; font-weight: 600; color: #0f172a; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name ?? ($dummyUser ?? 'Kasir') }}</p>
                            <p style="font-size: 11.5px; color: #64748b; margin: 2px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email ?? 'kasir@morebrew.com' }}</p>
                            <div style="margin-top: 6px;">
                                <span style="display: inline-block; padding: 2px 8px; background: #f1f5f9; color: #334155; border-radius: 12px; font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">{{ auth()->user()->role ?? 'Kasir' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('kasir.pengaturan') }}" class="dropdown-item-link">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            <span>Pengaturan Printer & Pajak</span>
                        </a>

                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item-link">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            <span>Buka Mode Admin</span>
                        </a>

                        <div style="border-top: 1px solid #f1f5f9; margin: 6px 0;"></div>

                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 9px 12px; border: none; background: none; color: #dc2626; font-size: 13px; font-weight: 600; border-radius: 6px; cursor: pointer; text-align: left; transition: all 0.15s ease;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span>Keluar (Log Out)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
        <script>
            function toggleHeaderDropdown(id) {
                const el = document.getElementById(id);
                if (!el) return;
                const isShown = el.style.display === 'block';
                document.querySelectorAll('.profile-dropdown-menu').forEach(m => m.style.display = 'none');
                if (!isShown) {
                    el.style.display = 'block';
                }
            }
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.profile-dropdown-wrapper')) {
                    document.querySelectorAll('.profile-dropdown-menu').forEach(m => m.style.display = 'none');
                }
            });
        </script>