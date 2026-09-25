    <aside class="sidebar-left">
        <div class="brand">
            <h2>POS System</h2>
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
            <span>Role admin</span>
        </div>
    </aside>