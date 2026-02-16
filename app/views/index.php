<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Système de Gestion des Dons</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                <li><a href="/" class="active">Tableau de bord</a></li>
                <li><a href="/besoins">Besoins</a></li>
                <li><a href="/dons">Dons</a></li>
                <li><a href="/villes">Villes</a></li>
                <li><a href="/simulation">Simulation</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">Système de suivi en temps réel</div>
            <h1 class="hero-title">Tableau de bord des distributions</h1>
            <p class="hero-subtitle">Visualisez et gérez les besoins des sinistrés et l'attribution des dons par ville</p>
        </div>
        <div class="hero-stats">
            <div class="stat-card">
                <div class="stat-icon">📍</div>
                <div class="stat-value" id="totalVilles">12</div>
                <div class="stat-label">Villes concernées</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value" id="totalDons">847</div>
                <div class="stat-label">Dons reçus</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-value" id="tauxAttribution">73%</div>
                <div class="stat-label">Taux d'attribution</div>
            </div>
        </div>
    </section>

    <!-- Main Dashboard -->
    <main class="dashboard-main">
        <div class="dashboard-container">
            <!-- Filters & Actions -->
            <div class="dashboard-header">
                <div class="filter-group">
                    <select class="filter-select" id="filterType">
                        <option value="all">Tous les types</option>
                        <option value="nature">En nature</option>
                        <option value="materiel">Matériaux</option>
                        <option value="argent">Argent</option>
                    </select>
                    <select class="filter-select" id="filterStatut">
                        <option value="all">Tous les statuts</option>
                        <option value="urgent">Urgent</option>
                        <option value="partiel">Partiellement couvert</option>
                        <option value="complet">Complètement couvert</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button class="btn-secondary" onclick="exportData()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Exporter
                    </button>
                    <button class="btn-primary" onclick="window.location.href='simulation.html'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Lancer simulation
                    </button>
                </div>
            </div>

            <!-- Cities Grid -->
            <div class="cities-grid" id="citiesGrid">
                <!-- City cards will be dynamically inserted here -->
            </div>

            <!-- Detailed View Section -->
            <div class="detail-section">
                <h2 class="section-title">Analyse détaillée par catégorie</h2>
                <div class="category-analysis">
                    <div class="category-card">
                        <div class="category-header">
                            <h3>🌾 En nature</h3>
                            <span class="category-badge urgent">Prioritaire</span>
                        </div>
                        <div class="category-stats">
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Riz</span>
                                    <span>6,800 / 10,000 kg</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 68%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Huile</span>
                                    <span>2,340 / 5,000 L</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 47%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Eau potable</span>
                                    <span>8,900 / 8,000 L</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill complete" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-header">
                            <h3>🔨 Matériaux</h3>
                            <span class="category-badge warning">Modéré</span>
                        </div>
                        <div class="category-stats">
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Tôles</span>
                                    <span>1,200 / 2,000 unités</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 60%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Clous</span>
                                    <span>45 / 50 kg</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 90%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Bâches</span>
                                    <span>350 / 500 m²</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 70%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="category-card">
                        <div class="category-header">
                            <h3>💰 Argent</h3>
                            <span class="category-badge success">Bon</span>
                        </div>
                        <div class="category-stats">
                            <div class="progress-group">
                                <div class="progress-label">
                                    <span>Total collecté</span>
                                    <span>24,500,000 / 30,000,000 Ar</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 82%"></div>
                                </div>
                            </div>
                            <div class="amount-details">
                                <div class="amount-item">
                                    <span>Distribué</span>
                                    <strong>18,200,000 Ar</strong>
                                </div>
                                <div class="amount-item">
                                    <span>En attente</span>
                                    <strong>6,300,000 Ar</strong>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <script src="script.js"></script>
</body>
</html>
