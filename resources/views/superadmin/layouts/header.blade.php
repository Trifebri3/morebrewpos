<header class="top-bar" style="position: relative; z-index: 50;">
    <style>
        .profile-dropdown-wrapper { position: relative; }
        .profile-dropdown-btn:hover { background: rgba(0,0,0,0.04); border-radius: 8px; }
        .notification-btn:hover { color: var(--text-main) !important; }
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
        <svg style="position:absolute; margin:12px 16px; width:16px; height:16px; color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" placeholder="Search..." style="padding-left: 40px;">
    </div>
    
    <div class="header-actions" style="display: flex; align-items: center; gap: 14px;">
        <button class="notification-btn" style="background:none; border:none; color:var(--text-muted); cursor:pointer; position:relative; transition: color 0.2s;" title="Notifikasi">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span style="position:absolute; top:-2px; right:-2px; width:8px; height:8px; background:red; border-radius:50%; border:2px solid white;"></span>
        </button>

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
            <div class="profile-dropdown profile-dropdown-btn" onclick="toggleHeaderDropdown('superadmin-user-menu')" style="display: flex; align-items: center; gap: 10px; cursor:pointer; padding: 6px 10px; transition: background 0.2s; user-select: none;">
                <div style="width: 36px; height: 36px; background: #0f172a; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                    {{ strtoupper(substr(auth()->user()->name ?? ($dummyUser ?? 'S'), 0, 1)) }}
                </div>
                <div style="display: flex; flex-direction: column; text-align: left;">
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main); line-height: 1.2;">{{ auth()->user()->name ?? ($dummyUser ?? 'Superadmin') }}</span>
                    <span style="font-size: 11px; color: var(--text-muted); text-transform: capitalize;">{{ auth()->user()->role ?? 'Superadmin' }}</span>
                </div>
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted); margin-left: 2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>

            {{-- Dropdown Menu Popover --}}
            <div id="superadmin-user-menu" class="profile-dropdown-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 240px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06); padding: 8px; z-index: 9999;">
                <div style="padding: 10px 12px 8px; border-bottom: 1px solid #f1f5f9; margin-bottom: 6px;">
                    <p style="font-size: 13.5px; font-weight: 600; color: #0f172a; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name ?? ($dummyUser ?? 'Superadmin') }}</p>
                    <p style="font-size: 11.5px; color: #64748b; margin: 2px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email ?? 'superadmin@morebrew.com' }}</p>
                    <div style="margin-top: 6px;">
                        <span style="display: inline-block; padding: 2px 8px; background: #f1f5f9; color: #334155; border-radius: 12px; font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">{{ auth()->user()->role ?? 'Superadmin' }}</span>
                    </div>
                </div>

                <a href="{{ route('superadmin.kedai.index') }}" class="dropdown-item-link">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>Kelola Kedai</span>
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