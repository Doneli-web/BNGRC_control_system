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
        
        .sim-status h3 {
            font-size: 1.5rem;
            margin-bottom: var(--spacing-sm);
            color: var(--text-primary);
        }
        
        .progress-tracker {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: var(--spacing-lg) 0;
            position: relative;
        }
        
        .progress-tracker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border);
            z-index: 0;
        }
        
        .progress-step {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            position: relative;
            z-index: 1;
            transition: all var(--transition-base);
        }
        
        .progress-step.active {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            box-shadow: 0 0 0 4px rgba(196, 69, 54, 0.2);
        }
        
        .progress-step.complete {
            border-color: var(--success);
            background: var(--success);
            color: white;
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
        
        .result-item {
            display: flex;
            justify-content: space-between;
            padding: var(--spacing-xs) 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .result-item:last-child {
            border-bottom: none;
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
                <li><a href="/dons">Dons</a></li>
                <li><a href="/villes">Villes</a></li>
                <li><a href="/simulation" class="active">Simulation</a></li>
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
            <p class="page-description">Simulez la répartition automatique des dons aux villes en fonction des besoins et de la date de réception</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="simulation-container">
            <!-- Control Panel -->
            <div class="sim-panel">
                <h2 style="text-align: center; margin-bottom: 2rem;">Centre de contrôle</h2>
                
                <div class="sim-status" id="simStatus">
                    <h3>Prêt à simuler</h3>
                    <p>Cliquez sur "Lancer la simulation" pour démarrer la répartition automatique</p>
                </div>
                
                <div class="progress-tracker" id="progressTracker">
                    <div class="progress-step" data-step="1">1</div>
                    <div class="progress-step" data-step="2">2</div>
                    <div class="progress-step" data-step="3">3</div>
                    <div class="progress-step" data-step="4">4</div>
                    <div class="progress-step" data-step="5">5</div>
                </div>
                
                <div class="sim-controls">
                    <button class="btn-primary" onclick="startSimulation()" id="btnStart">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Lancer la simulation
                    </button>
                    <button class="btn-secondary" onclick="resetSimulation()" id="btnReset" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                        </svg>
                        Réinitialiser
                    </button>
                    <button class="btn-secondary" onclick="exportResults()" id="btnExport" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Exporter résultats
                    </button>
                </div>
                
                <div class="log-container" id="logContainer">
                    <div class="log-entry log-info">Système initialisé et prêt...</div>
                </div>
            </div>
            
            <!-- Results Panel -->
            <div class="sim-panel" id="resultsPanel" style="display: none;">
                <h2 style="margin-bottom: 1.5rem;">Résultats de la simulation</h2>
                <div class="results-grid" id="resultsGrid"></div>
            </div>
            
            <!-- Statistics Summary -->
            <div class="sim-panel" id="statsPanel" style="display: none;">
                <h2 style="margin-bottom: 1.5rem;">Résumé statistique</h2>
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

    <script src="/assets/js/simulation.js"></script>
</body>
</html>
