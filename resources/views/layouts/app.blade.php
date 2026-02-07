<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NJIEZM Admin | Panel de Génération')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Space+Grotesk:wght@300;500;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <!-- Bibliothèques PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
    <style>
        :root {
            --nj-blue: #003366;
            --nj-yellow: #FFD700;
            --nj-white: #F8F9FA;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #f0f2f5;
            color: var(--nj-blue);
            min-height: 100vh;
            display: flex;
        }

        /* --- SIDEBAR STYLE --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--nj-blue);
            color: white;
            height: 100vh;
            position: fixed;
            border-right: 5px solid var(--nj-yellow);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            font-family: 'Special Elite', cursive;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-menu {
            padding: 1rem 0;
            flex-grow: 1;
        }

        .nav-item-admin {
            padding: 12px 25px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .nav-item-admin:hover, .nav-item-admin.active {
            background: var(--nj-yellow);
            color: var(--nj-blue);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 40px;
        }

        .admin-card {
            background: white;
            border: 2px solid var(--nj-blue);
            box-shadow: 8px 8px 0px rgba(0, 51, 102, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-family: 'Special Elite', cursive;
            margin-bottom: 25px;
            border-bottom: 3px solid var(--nj-yellow);
            display: inline-block;
            padding-bottom: 5px;
        }

        .btn-njie {
            background: var(--nj-yellow);
            color: var(--nj-blue);
            font-weight: 800;
            border: 2px solid var(--nj-blue);
            border-radius: 0;
            padding: 10px 20px;
            text-transform: uppercase;
            box-shadow: 4px 4px 0px var(--nj-blue);
            transition: 0.2s;
        }

        .btn-njie:hover {
            background: var(--nj-blue);
            color: white;
            transform: translate(-2px, -2px);
        }

        /* Forms */
        .form-label { font-weight: 700; font-size: 0.85rem; color: #555; }
        .form-control, .form-select {
            border-radius: 0;
            border: 1px solid #ccc;
            font-family: 'JetBrains Mono', monospace;
        }

        .table-admin input {
            border: none;
            width: 100%;
            background: transparent;
        }
        
        .total-box {
            background: var(--nj-blue);
            color: var(--nj-yellow);
            padding: 15px;
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* --- LOGO GENERATOR SPECIFIC --- */
        .logo-preview-box {
            background: var(--nj-blue);
            padding: 60px;
            border-radius: 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            border: 2px solid var(--nj-blue);
        }

        .logo-preview-box.transparent {
            background-image: 
                linear-gradient(45deg, #eee 25%, transparent 25%), 
                linear-gradient(-45deg, #eee 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #eee 75%), 
                linear-gradient(-45deg, transparent 75%, #eee 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            background-color: white;
        }

        #logo-canvas-text {
            font-family: 'Special Elite', cursive;
            font-size: 4rem;
            color: var(--nj-yellow);
            user-select: none;
            line-height: 1;
        }

        #logo-canvas-text span {
            color: white;
        }

        #export-canvas { display: none; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3 class="m-0">NJIEZM<span style="color:var(--nj-yellow)">.ADMIN</span></h3>
        </div>
        <div class="nav-menu">
<!-- ... dans la div .nav-menu ... -->
<a href="{{ route('dashboard') }}" class="nav-item-admin {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>
<a href="{{ route('simulator') }}" class="nav-item-admin {{ request()->routeIs('simulator') ? 'active' : '' }}">🧮 Simulateur Rapide</a>

<!-- Section Devis -->
<div class="nav-item-admin">📄 Gestion Devis</div>
<div class="ps-4">
    <a href="{{ route('devis') }}" class="nav-item-admin {{ request()->routeIs('devis') ? 'active' : '' }}" style="font-size: 0.8rem;">+ Créer</a>
    <!-- C'est ce lien qui pose problème, il doit correspondre à la route nommée 'devis.list' -->
    <a href="{{ route('devis.list') }}" class="nav-item-admin {{ request()->routeIs('devis.list') ? 'active' : '' }}" style="font-size: 0.8rem;">+ Voir tous</a>
</div>

<!-- Section Factures -->
<div class="nav-item-admin">💰 Gestion Facture</div>
<div class="ps-4">
    <a href="{{ route('facture') }}" class="nav-item-admin {{ request()->routeIs('facture') ? 'active' : '' }}" style="font-size: 0.8rem;">+ Créer</a>
    <!-- Et celui-ci pour 'factures.list' -->
    <a href="{{ route('factures.list') }}" class="nav-item-admin {{ request()->routeIs('factures.list') ? 'active' : '' }}" style="font-size: 0.8rem;">+ Voir toutes</a>
</div>

<a href="{{ route('logo') }}" class="nav-item-admin {{ request()->routeIs('logo') ? 'active' : '' }}">🎨 Exportateur Logo</a>
<!-- ... -->
        </div>
        <div class="p-4 mt-auto">
            <a href="#" class="btn btn-outline-light w-100 btn-sm rounded-0">Déconnexion</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>