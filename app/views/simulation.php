<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation de Distribution - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .simulation-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .sim-panel {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
        }
        
        .sim-controls {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: var(--spacing-xl);
        }
        
        .sim-status {
            text-align: center;
            padding: var(--spacing-lg);
            background: var(--bg-tertiary);
            border-radius: 8px;
            margin-bottom: var(--spacing-lg);
        }
        
        .mode-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: var(--spacing-md);
        }
        
        .mode-badge.preview {
            background: rgba(74, 125, 156, 0.1);
            color: var(--info);
        }
        
        .mode-badge.final {
            background: rgba(74, 139, 111, 0.1);
            color: var(--success);
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
        }
        
        .result-card {
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: var(--spacing-md);
            animation: fadeInUp 0.5s ease-out;
        }
        
        .result-card h4 {
            margin-bottom: var(--spacing-sm);
            color: var(--primary);
        }
        
        .log-container {
            background: var(--text-primary);
            color: #00ff00;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            padding: var(--spacing-md);
            border-radius: 8px;
            height: 300px;
            overflow-y: auto;
            margin-top: var(--spacing-lg);
        }
        
        .log-entry {
            margin-bottom: 0.5rem;
            opacity: 0;
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        .log-success { color: #00ff00; }
        .log-warning { color: #ffaa00; }
        .log-error { color: #ff4444; }
        .log-info { color: #00aaff; }
        
        .btn-validate {
            background: var(--success);
            animation: pulse 2s infinite;
        }
        
        .btn-validate:hover {
            background: #3a6d57;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(74, 139, 111, 0.7); }
            50% { box-shadow: 0 0 0 10px rgba(74, 139, 111, 0); }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="logo-icon">
                    <svg viewBox="0 0 40 40" fill="none">
                        <path d="M20 5L35 15V25L20 35L5 25V15L20 5Z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="20" cy="20" r="4" fill="currentColor"/>
                    </svg>
                </div>
                <span class="brand-text">BNGRC<span class="brand-sub">Gestion des Dons</span></span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">Tableau de bord</a></li>
                <li><a href="besoins.html">Besoins</a></li>
                <li><a href="dons.html">Dons</a></li>
                <li><a href="achats.html">Achats</a></li>
                <li><a href="simulation.html" class="active">Simulation</a></li>
                <li><a href="recapitulatif.html">Récapitulatif</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb">
                <a href="index.html">Accueil</a>
                <span>/</span>
                <span>Simulation de distribution</span>
            </div>
            <h1 class="page-title">Simulation de distribution</h1>
            <p class="page-description">Prévisualisez ou validez la répartition automatique des dons aux besoins</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="simulation-container">
            <!-- Control Panel -->
            <div class="sim-panel">
                <h2 style="text-align: center; margin-bottom: 2rem;">🎮 Centre de contrôle</h2>
                
                <div class="sim-status" id="simStatus">
                    <h3>Prêt à simuler</h3>
                    <p>Choisissez une action ci-dessous</p>
                </div>
                
                <div class="sim-controls">
                    <button class="btn-secondary" onclick="previewSimulation()" id="btnPreview">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        👁️ Prévisualiser la simulation
                    </button>
                    
                    <button class="btn-primary btn-validate" onclick="validateSimulation()" id="btnValidate" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        ✅ Valider et dispatcher
                    </button>
                    
                    <button class="btn-secondary" onclick="resetSimulation()" id="btnReset">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                        </svg>
                        Réinitialiser
                    </button>
                </div>
                
                <div class="log-container" id="logContainer">
                    <div class="log-entry log-info">[INFO] Système initialisé et prêt...</div>
                    <div class="log-entry log-info">[INFO] Mode prévisualisation disponible</div>
                </div>
            </div>
            
            <!-- Results Panel -->
            <div class="sim-panel" id="resultsPanel" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>📊 Résultats de la simulation</h2>
                    <span class="mode-badge" id="modeBadge"></span>
                </div>
                <div class="results-grid" id="resultsGrid"></div>
            </div>
            
            <!-- Statistics Summary -->
            <div class="sim-panel" id="statsPanel" style="display: none;">
                <h2 style="margin-bottom: 1.5rem;">📈 Résumé statistique</h2>
                <div class="stats-grid" id="statsGrid"></div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>BNGRC</h3>
                    <p>Bureau National de Gestion des Risques et des Catastrophes</p>
                </div>
                <div class="footer-links">
                    <div class="link-group">
                        <h4>Navigation</h4>
                        <a href="index.html">Tableau de bord</a>
                        <a href="besoins.html">Gestion des besoins</a>
                        <a href="dons.html">Gestion des dons</a>
                    </div>
                    <div class="link-group">
                        <h4>Ressources</h4>
                        <a href="#">Documentation</a>
                        <a href="#">Support</a>
                        <a href="#">Contact</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 BNGRC. Projet S3 - Système de gestion des dons.</p>
            </div>
        </div>
    </footer>

    <script src="/assets/js/simulation-v2.js"></script>
</body>
</html>
