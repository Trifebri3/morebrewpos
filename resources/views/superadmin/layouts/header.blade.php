<header class="top-bar">
    <style>
        .profile-dropdown:hover { background: rgba(0,0,0,0.03); border-radius: 8px; }
        .notification-btn:hover { color: var(--text-main) !important; }
    </style>
    <div class="search-bar">
        <svg style="position:absolute; margin:12px 16px; width:16px; height:16px; color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" placeholder="Search..." style="padding-left: 40px;">
    </div>
    
    <div class="header-actions" style="display: flex; align-items: center; gap: 24px;">
        <button class="notification-btn" style="background:none; border:none; color:var(--text-muted); cursor:pointer; position:relative; transition: color 0.2s;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span style="position:absolute; top:-2px; right:-2px; width:8px; height:8px; background:red; border-radius:50%; border:2px solid white;"></span>
        </button>
        
        <div class="profile-dropdown" style="display: flex; align-items: center; gap: 12px; cursor:pointer; padding: 6px 12px; transition: background 0.2s;">
            <div style="width: 36px; height: 36px; background: var(--text-main); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                {{ substr($dummyUser ?? 'S', 0, 1) }}
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 14px; font-weight: 500; color: var(--text-main);">{{ $dummyUser ?? 'Superadmin' }}</span>
                <span style="font-size: 11px; color: var(--text-muted);">Administrator</span>
            </div>
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
    </div>
</header>