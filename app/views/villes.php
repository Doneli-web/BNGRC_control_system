<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Villes - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .stats-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-mini-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
        .stat-mini-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            font-family: var(--font-display);
        }
        .stat-mini-card .label {
            font-size: 0.9rem;
            color: var(--text-muted);
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
                <li><a href="/villes" class="active">Villes</a></li>
                <li><a href="/simulation">Simulation</a></li>
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
                <a href="/">Accueil</a>
                <span>/</span>
                <span>Gestion des villes</span>
            </div>
            <h1 class="page-title">Gestion des villes</h1>
            <p class="page-description">Consultez les informations détaillées sur chaque ville et leurs besoins</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="content-container">
            
            <!-- Statistiques rapides -->
            <div class="stats-mini-grid">
                <div class="stat-mini-card">
                    <div class="number"><?= htmlspecialchars($total_villes ?? count($villes)) ?></div>
                    <div class="label">Villes totales</div>
                </div>
                <div class="stat-mini-card">
                    <div class="number"><?= htmlspecialchars($total_regions ?? $regions_count ?? '0') ?></div>
                    <div class="label">Régions</div>
                </div>
                <div class="stat-mini-card">
                    <div class="number"><?= htmlspecialchars($total_besoins ?? '0') ?></div>
                    <div class="label">Besoins enregistrés</div>
                </div>
                <div class="stat-mini-card">
                    <div class="number"><?= htmlspecialchars($villes_urgentes ?? '0') ?></div>
                    <div class="label">Villes urgentes</div>
                </div>
            </div>

            <!-- Cities Overview -->
            <div class="list-section">
                <div class="list-header">
                    <h2>Villes concernées</h2>
                    <div class="list-actions">
                        <input type="search" class="search-input" id="searchInput" placeholder="Rechercher une ville...">
                        <select class="filter-select-small" id="sortSelect">
                            <option value="nom">Trier par nom</option>
                            <option value="besoins">Trier par besoins</option>
                            <option value="priorite">Trier par priorité</option>
                        </select>
                    </div>
                </div>

                <div class="cities-grid" id="villesGrid">
                    <?php if(empty($villes)): ?>
                        <p style="text-align: center; color: var(--text-muted); padding: 2rem; grid-column: 1/-1;">
                            Aucune ville disponible
                        </p>
                    <?php else: ?>
                        <?php foreach($villes as $index => $ville): ?>
                        <div class="city-card" data-nom="<?= htmlspecialchars($ville['name']) ?>" 
                             data-besoins="<?= $ville_stats[$ville['id']]['total_besoins'] ?? 0 ?>"
                             data-priorite="<?= $ville_stats[$ville['id']]['priorite'] ?? 'normal' ?>">
                            <div class="city-header">
                                <h3 class="city-name"><?= htmlspecialchars($ville['name']) ?></h3>
                                <?php 
                                $priorite = $ville_stats[$ville['id']]['priorite'] ?? 'normal';
                                $badge_class = 'normal';
                                $badge_text = 'Normal';
                                if($priorite == 'urgent') {
                                    $badge_class = 'urgent';
                                    $badge_text = 'URGENT';
                                } elseif($priorite == 'important') {
                                    $badge_class = 'important';
                                    $badge_text = 'Important';
                                }
                                ?>
                                <span class="priority-badge <?= $badge_class ?>"><?= $badge_text ?></span>
                            </div>
                            <div class="needs-summary">
                                
                                <div class="need-item">
                                    <span class="need-label">Nombre de besoins</span>
                                    <span class="need-value"><?= $ville_stats[$ville['id']]['total_besoins'] ?? 0 ?></span>
                                </div>
                                <div class="need-item">
                                    <span class="need-label">Montant total</span>
                                    <span class="need-value"><?= number_format($ville_stats[$ville['id']]['montant_total'] ?? 0, 0, ',', ' ') ?> Ar</span>
                                </div>
                                <div class="need-item">
                                    <span class="need-label">Région</span>
                                    <span class="need-value"><?= htmlspecialchars($ville['region_name'] ?? 'N/A') ?></span>
                                </div>
                                <?php if(($ville_stats[$ville['id']]['taux_couverture'] ?? 0) > 0): ?>
                                <div class="progress-group" style="margin-top: 0.5rem;">
                                    <div class="progress-label">
                                        <span>Couverture</span>
                                        <span><?= $ville_stats[$ville['id']]['taux_couverture'] ?? 0 ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $ville_stats[$ville['id']]['taux_couverture'] ?? 0 ?>%"></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <a href="/villes/detail/<?= $ville['id'] ?>" class="btn-icon-small" title="Voir détails">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detailed Information Panel -->
            <div class="form-card" style="margin-top: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Informations sur les villes de Madagascar</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <?php foreach($villes as $ville): ?>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">
                            📍 <?= htmlspecialchars($ville['name']) ?>
                        </h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            <?= htmlspecialchars($ville['description'] ?? 'Population: à renseigner') ?>
                        </p>
                        <small style="color: var(--text-muted);">Région: <?= htmlspecialchars($ville['region_name'] ?? 'N/A') ?></small>
                    </div>
                    <?php endforeach; ?>
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

    <script>
        // Navigation
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function() {
                navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
            });
        }

        // Load and display villes with their data
        function loadVilles() {
            const besoins = JSON.parse(localStorage.getItem('besoins')) || [];
            const villesData = {};
            
            besoins.forEach(besoin => {
                if (!villesData[besoin.ville]) {
                    villesData[besoin.ville] = {
                        nom: besoin.ville,
                        besoins: [],
                        totalMontant: 0,
                        prioriteMax: 'normal'
                    };
                }
                
                villesData[besoin.ville].besoins.push(besoin);
                villesData[besoin.ville].totalMontant += besoin.montantTotal;
                
                if (besoin.priorite === 'urgent' || 
                    (besoin.priorite === 'important' && villesData[besoin.ville].prioriteMax !== 'urgent')) {
                    villesData[besoin.ville].prioriteMax = besoin.priorite;
                }
            });
            
            displayVilles(Object.values(villesData));
        }

        function displayVilles(villes) {
            const grid = document.getElementById('villesGrid');
            grid.innerHTML = '';
            
            if (villes.length === 0) {
                grid.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Aucune ville avec des besoins enregistrés</p>';
                return;
            }
            
            villes.forEach((ville, index) => {
                const card = document.createElement('div');
                card.className = 'city-card';
                card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
                
                card.innerHTML = `
                    <div class="city-header">
                        <h3 class="city-name">${ville.nom}</h3>
                        <span class="priority-badge ${ville.prioriteMax}">${ville.prioriteMax.toUpperCase()}</span>
                    </div>
                    <div class="needs-summary">
                        <div class="need-item">
                            <span class="need-label">Nombre de besoins</span>
                            <span class="need-value">${ville.besoins.length}</span>
                        </div>
                        <div class="need-item">
                            <span class="need-label">Montant total</span>
                            <span class="need-value">${formatMoney(ville.totalMontant)} Ar</span>
                        </div>
                        <div class="need-item">
                            <span class="need-label">Catégories</span>
                            <span class="need-value">${getUniqueCategories(ville.besoins).join(', ')}</span>
                        </div>
                    </div>
                `;
                
                grid.appendChild(card);
            });
        }

        function getUniqueCategories(besoins) {
            const categories = [...new Set(besoins.map(b => b.categorie))];
            const labels = {
                'nature': 'Nature',
                'materiel': 'Matériel',
                'argent': 'Argent'
            };
            return categories.map(c => labels[c] || c);
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-MG').format(amount);
        }

        function filterVilles(searchTerm) {
            const cards = document.querySelectorAll('.city-card');
            const term = searchTerm.toLowerCase();
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(term) ? '' : 'none';
            });
        }

        function sortVilles(criteria) {
            // Implementation would depend on stored data
            console.log('Sorting by:', criteria);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', loadVilles);
    </script>
</body>
</html>
    <script src="/assets/js/villes.js"></script>
</body>
</html>
