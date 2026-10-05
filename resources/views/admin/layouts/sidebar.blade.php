    <aside class="sidebar-left">
        <div class="brand" style="display: flex; align-items: center; gap: 12px; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
            <img src="{{ asset('logo.png') }}?v={{ file_exists(public_path('logo.png')) ? filemtime(public_path('logo.png')) : 1 }}" alt="Logo MoreBrew" style="width: 38px; height: 38px; object-fit: contain; flex-shrink: 0; background: transparent; border: none; box-shadow: none;" onerror="this.src='https://placehold.co/38x38/transparent/000000?text=MB'">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.2; letter-spacing: -0.3px;">MoreBrew POS</h2>
                <span style="font-size: 11px; color: #64748b; font-weight: 500;">Specialty Coffee & Lab</span>
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
        
        <div class="user-info">
            <p>{{ $dummyUser ?? 'User' }}</p>
            <span>Role admin</span>
        </div>
    </aside>