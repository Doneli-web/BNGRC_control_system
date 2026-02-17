<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .recap-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .recap-header {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
            text-align: center;
        }
        
        .refresh-button {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: var(--spacing-md) var(--spacing-lg);
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-base);
            margin-top: var(--spacing-md);
        }
        
        .refresh-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .refresh-button.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .refresh-icon {
            animation: none;
        }
        
        .refresh-button.loading .refresh-icon {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .summary-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: var(--spacing-xl);
            transition: all var(--transition-base);
        }
        
        .summary-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }
        
        .summary-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: var(--spacing-md);
        }
        
        .summary-amount {
            font-family: var(--font-display);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
        }
        
        .summary-amount.total {
            color: var(--text-primary);
        }
        
        .summary-amount.satisfied {
            color: var(--success);
        }
        
        .summary-amount.remaining {
            color: var(--danger);
        }
        
        .summary-detail {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .progress-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xl);
        }
        
        .detail-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: var(--spacing-xl);
        }
        
        .last-update {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: var(--spacing-md);
        }
        
        .auto-refresh-toggle {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            justify-content: center;
            margin-top: var(--spacing-md);
        }
        
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 24px;
            background: var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: background var(--transition-base);
        }
        
        .toggle-switch.active {
            background: var(--success);
        }
        
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: transform var(--transition-base);
        }
        
        .toggle-switch.active::after {
            transform: translateX(26px);
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
                <li><a href="/">Tableau de bord</a></li>
                <li><a href="/besoins">Besoins</a></li>
                <li><a href="/dons">Dons</a></li>
                <li><a href="/achats">Achats</a></li>
                <li><a href="/simulation">Simulation</a></li>
                <li><a href="/recapitulatif" class="active">Récapitulatif</a></li>
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
                <span>Récapitulatif</span>
            </div>
            <h1 class="page-title">Récapitulatif global</h1>
            <p class="page-description">Vue d'ensemble des besoins totaux, satisfaits et restants</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="recap-container">
            
            <!-- Header avec bouton actualiser -->
            <div class="recap-header">
                <h2 style="margin-bottom: var(--spacing-sm);">📊 État des besoins</h2>
                <p style="color: var(--text-secondary); margin-bottom: var(--spacing-md);">
                    Données actualisées en temps réel
                </p>
                
                <button class="refresh-button" id="btnRefresh" onclick="refreshData()">
                    <svg class="refresh-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="1 4 1 10 7 10"/>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                    </svg>
                    Actualiser
                </button>
                
                <div class="auto-refresh-toggle">
                    <span style="font-size: 0.85rem;">Actualisation automatique (30s)</span>
                    <div class="toggle-switch" id="autoRefreshToggle" onclick="toggleAutoRefresh()"></div>
                </div>
                
                <div class="last-update" id="lastUpdate">
                    Dernière mise à jour: Jamais
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="summary-grid">
                <!-- Besoins totaux -->
                <div class="summary-card">
                    <div class="summary-title">💰 Besoins Totaux</div>
                    <div class="summary-amount total" id="montantTotal"><?= number_format($totalBesoin, 2, '.', ' ') ?> Ar</div>
                    <div class="summary-detail" id="detailTotal">
                        Montant total de tous les besoins enregistrés
                    </div>
                </div>

                <!-- Besoins satisfaits -->
                <div class="summary-card">
                    <div class="summary-title">✅ Besoins Satisfaits</div>
                    <div class="summary-amount satisfied" id="montantSatisfait"><?= number_format($totalMontantSatisfait, 2, '.', ' ') ?> Ar</div>
                    <div class="summary-detail" id="detailSatisfait">
                        Couvert par les dons et achats
                    </div>
                </div>

                <!-- Besoins restants -->
                <div class="summary-card">
                    <div class="summary-title">⚠️ Besoins Restants</div>
                    <div class="summary-amount remaining" id="montantRestant"><?= number_format($totalBesoin - $totalMontantSatisfait, 2, '.', ' ') ?> Ar</div>
                    <div class="summary-detail" id="detailRestant">
                        Encore à couvrir
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-section">
                <h3 style="margin-bottom: var(--spacing-md);">Progression globale</h3>
                <div class="progress-group">
                    <div class="progress-label">
                        <span>Taux de satisfaction</span>
                        <span id="tauxSatisfaction">0%</span>
                    </div>
                    <div class="progress-bar" style="height: 40px;">
                        <div class="progress-fill" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Details by Category -->
            <div class="detail-section">
                <h3 style="margin-bottom: var(--spacing-lg);">Détails par catégorie</h3>
                <div class="category-analysis" id="categoryDetails">
                    <!-- Dynamically filled -->
                </div>
            </div>

            <!-- Details by City -->
            <div class="detail-section" style="margin-top: var(--spacing-xl);">
                <h3 style="margin-bottom: var(--spacing-lg);">Détails par ville</h3>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ville</th>
                                <th>Besoins totaux</th>
                                <th>Satisfaits</th>
                                <th>Restants</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        <tbody id="cityDetailsTable">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">
                                    Chargement...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                        <a href="/">Tableau de bord</a>
                        <a href="/besoins">Gestion des besoins</a>
                        <a href="/dons">Gestion des dons</a>
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

    <script src="/assets/js/recapitulatif.js"></script>
</body>
</html>
