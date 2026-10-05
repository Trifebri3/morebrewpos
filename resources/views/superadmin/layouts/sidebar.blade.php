    <aside class="sidebar-left">
        <div class="brand" style="display: flex; align-items: center; gap: 12px; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #212121; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.15); flex-shrink: 0;">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="max-width: 32px; max-height: 32px; object-fit: contain;" onerror="this.src='https://placehold.co/40x40/212121/ffffff?text=MB'">
            </div>
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.2; letter-spacing: -0.3px;">MoreBrew POS</h2>
                <span style="font-size: 11px; color: #64748b; font-weight: 500;">Superadmin Panel</span>
            </div>
        </div>

        @if(isset($navGroups))
            @foreach($navGroups as $groupName => $items)
                <div class="nav-group-title" style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin: 24px 0 8px 8px; letter-spacing: 0.5px;">{{ $groupName }}</div>
                <ul class="nav-list">
                    @foreach($items as $item)
                    <li>
                        <a href="{{ $item['url'] ?? '#' }}" class="nav-link {{ ($item['active'] ?? false) ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            @endforeach
        @endif
        
        <div class="user-info">
            <p>{{ $dummyUser ?? 'User' }}</p>
            <span>Role superadmin</span>
        </div>
    </aside>