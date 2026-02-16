<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Villes - BNGRC</title>
    <link rel="stylesheet" href="assets/css/styles.css">
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
            <!-- Cities Overview -->
            <div class="list-section">
                <div class="list-header">
                    <h2>Villes concernées</h2>
                    <div class="list-actions">
                        <input type="search" class="search-input" placeholder="Rechercher une ville..." 
                               onkeyup="filterVilles(this.value)">
                        <select class="filter-select-small" onchange="sortVilles(this.value)">
                            <option value="nom">Trier par nom</option>
                            <option value="besoins">Trier par besoins</option>
                            <option value="priorite">Trier par priorité</option>
                        </select>
                    </div>
                </div>

                <div class="cities-grid" id="villesGrid" style="margin-top: 2rem;">
                    <!-- City cards will be dynamically inserted -->
                </div>
            </div>

            <!-- Detailed Information Panel -->
            <div class="form-card" style="margin-top: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Informations sur les villes de Madagascar</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Antananarivo</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Capitale et plus grande ville de Madagascar. Centre administratif et économique du pays.
                            Population: ~3 millions d'habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Toamasina</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Principal port de Madagascar sur la côte est. Ville économiquement importante pour le commerce.
                            Population: ~300,000 habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Antsirabe</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Ville thermale située dans les hautes terres centrales. Connue pour ses sources chaudes.
                            Population: ~250,000 habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Mahajanga</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Grande ville portuaire sur la côte nord-ouest. Important centre commercial.
                            Population: ~220,000 habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Fianarantsoa</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Capitale culturelle des hautes terres. Centre universitaire et religieux important.
                            Population: ~190,000 habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Toliara</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Ville du sud-ouest, porte d'entrée vers les régions arides du sud.
                            Population: ~160,000 habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Antsiranana</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Port important dans le nord. Base navale et centre touristique.
                            Population: ~115,000 habitants.
                        </p>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 8px;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary);">📍 Morondava</h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">
                            Ville côtière de l'ouest, célèbre pour l'allée des baobabs.
                            Population: ~55,000 habitants.
                        </p>
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
