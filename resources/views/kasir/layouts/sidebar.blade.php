    <aside class="sidebar-left">
        <div class="brand" style="display: flex; align-items: center; gap: 12px; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
            <img src="{{ asset('logo.png') }}?v={{ file_exists(public_path('logo.png')) ? filemtime(public_path('logo.png')) : 1 }}" alt="Logo MoreBrew" style="width: 38px; height: 38px; object-fit: contain; flex-shrink: 0; background: transparent; border: none; box-shadow: none;" onerror="this.src='https://placehold.co/38x38/transparent/000000?text=MB'">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.2; letter-spacing: -0.3px;">MoreBrew POS</h2>
                <span style="font-size: 11px; color: #64748b; font-weight: 500;">Kasir Terminal</span>
            </div>
        </div>

        @if(isset($navGroups))
            @foreach($navGroups as $groupName => $items)
                <div class="nav-group-title" style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin: 24px 0 8px 8px; letter-spacing: 0.5px;">{{ $groupName }}</div>
                <ul class="nav-list">
                    @foreach($items as $item)
                    <li>
                        <a href="{{ $item['url'] ?? '#' }}" class="nav-link {{ ($item['active'] ?? false) ? 'active' : '' }}" style="display: flex; align-items: center; gap: 10px;">
                            <x-nav-icon :name="$item['label']" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            @endforeach
        @endif
        
        <div class="user-info" style="margin-top: auto; padding-top: 18px; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; gap: 10px;">
            <div style="min-width: 0; flex: 1;">
                <p style="font-size: 13.5px; font-weight: 600; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name ?? ($dummyUser ?? 'Kasir') }}</p>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; text-transform: capitalize;">Role {{ auth()->user()->role ?? 'kasir' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; flex-shrink: 0;">
                @csrf
                <button type="submit" title="Keluar / Log Out" style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 6px; padding: 6px 10px; color: #e11d48; cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 600; transition: all 0.15s ease;" onmouseover="this.style.background='#ffe4e6'; this.style.borderColor='#fda4af';" onmouseout="this.style.background='#fff1f2'; this.style.borderColor='#fecdd3';">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>