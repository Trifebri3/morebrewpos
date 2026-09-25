<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard - {{ $role ?? 'superadmin' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #212121;
            --primary-hover: #000000;
            --bg-color: #f9f9f9;
            --sidebar-bg: #ffffff;
            --text-main: #1a1a1a;
            --text-muted: #8e8e93;
            --border-color: #f0f0f0;
            --card-bg: #ffffff;
            --radius-lg: 12px;
            --radius-md: 8px;
            --radius-sm: 4px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Left */
        .sidebar-left {
            width: 250px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 32px 24px;
        }

        .brand {
            margin-bottom: 48px;
        }

        .brand h2 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text-main);
            background-color: var(--bg-color);
        }

        .user-info {
            margin-top: auto;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }

        .user-info p {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .user-info span {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            background-color: var(--bg-color);
        }

        .search-bar {
            width: 320px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background-color: var(--sidebar-bg);
            outline: none;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .search-bar input:focus {
            border-color: var(--text-muted);
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        .role-badge {
            background-color: var(--text-main);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 24px;
            padding: 0 40px 40px;
        }

        .product-card {
            background-color: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .product-card:hover {
            border-color: var(--text-muted);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .product-img {
            height: 120px;
            background-color: var(--bg-color);
            border-radius: var(--radius-md);
            margin-bottom: 16px;
        }

        .product-title {
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .product-price {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-main);
        }

        /* Sidebar Right (Kasir only mainly) */
        .sidebar-right {
            width: 320px;
            background-color: var(--sidebar-bg);
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>

    @include('superadmin.layouts.sidebar')

    <main class="main-content">
        @include('superadmin.layouts.header')

        <section class="content-area">
            @yield('content')
        </section>

        @include('superadmin.layouts.footer')
    </main>

</body>
</html>