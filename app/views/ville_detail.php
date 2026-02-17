<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de <?= htmlspecialchars($ville['name'] ?? 'la ville') ?> - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/styles3.css">
    
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
                <li><a href="/achats">Achats</a></li>
                <li><a href="/simulation">Simulation</a></li>
                <li><a href="/recapitulatif">Récapitulatif</a></li>
            </ul>
            <button class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="content-main">
        <div class="content-container">
            <!-- Back button -->
            <a href="/villes" class="back-button">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour à la liste des villes
            </a>

            <!-- Detail Header -->
            <!-- Detail Header -->
            <div class="detail-header">
                <?php 
                // Normaliser les données de la ville
                if(isset($ville[0]) && is_array($ville[0])) {
                    $ville_data = $ville[0];
                } else {
                    $ville_data = $ville;
                }
                
                $nom_ville = $ville_data['name'] ?? $ville_data['ville_nom'] ?? 'Ville inconnue';
                $id_ville = $ville_data['id'] ?? $ville_data['ville_id'] ?? 'N/A';
                $region = $ville_data['region_name'] ?? $ville_data['region'] ?? 'Non spécifiée';
                ?>
                <h1><?= htmlspecialchars($nom_ville) ?></h1>
                <div class="info-badge">
                    <strong>ID:</strong> #<?= htmlspecialchars($id_ville) ?> | 
                    <strong>Région:</strong> <?= htmlspecialchars($region) ?>
                </div>
            </div>

            <!-- Statistiques calculées -->
            <?php 
            $total_besoins = count($besoins ?? []);
            $total_dons = count($dons ?? []);
            
            $montant_total = 0;
            foreach($besoins as $b) {
                $montant_total += ($b['quantite'] * ($b['prix_unitaire'] ?? 1));
            }
            
            $montant_recu = 0;
            foreach($dons as $d) {
                $montant_recu += ($d['quantite'] * ($d['prix_unitaire'] ?? 1));
            }
            
            $taux = ($montant_total > 0) ? round(($montant_recu / $montant_total) * 100) : 0;
            
            $priorite_class = 'normal';
            $priorite_text = 'Normal';
            if($taux < 30) {
                $priorite_class = 'urgent';
                $priorite_text = 'URGENT';
            } elseif($taux < 70) {
                $priorite_class = 'important';
                $priorite_text = 'Important';
            }
            ?>
            
            <div class="stats-grid-detail">
                <div class="stat-box-detail">
                    <div class="stat-icon-large">📦</div>
                    <div class="stat-content-detail">
                        <h3>Total besoins</h3>
                        <div class="value"><?= $total_besoins ?></div>
                    </div>
                </div>
                <div class="stat-box-detail">
                    <div class="stat-icon-large">💰</div>
                    <div class="stat-content-detail">
                        <h3>Montant total</h3>
                        <div class="value"><?= number_format($montant_total, 0, ',', ' ') ?> Ar</div>
                    </div>
                </div>
                <div class="stat-box-detail">
                    <div class="stat-icon-large">📊</div>
                    <div class="stat-content-detail">
                        <h3>Taux couverture</h3>
                        <div class="value"><?= $taux ?>%</div>
                    </div>
                </div>
                <div class="stat-box-detail">
                    <div class="stat-icon-large">⚡</div>
                    <div class="stat-content-detail">
                        <h3>Priorité</h3>
                        <div class="value"><span class="priority-badge <?= $priorite_class ?>"><?= $priorite_text ?></span></div>
                    </div>
                </div>
            </div>

            <!-- Liste des besoins -->
            <div class="form-card">
                <div class="section-title">
                    <h2>Besoins enregistrés</h2>
                    <span class="badge-count"><?= $total_besoins ?></span>
                </div>
                
                <?php if(empty($besoins)): ?>
                    <div class="empty-message">
                        Aucun besoin enregistré pour cette ville
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($besoins as $besoin): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($besoin['article_nom'] ?? 'Article') ?></strong></td>
                                    <td><?= number_format($besoin['quantite']) ?></td>
                                    <td><?= number_format($besoin['prix_unitaire'] ?? 0, 0, ',', ' ') ?> Ar</td>
                                    <td><strong><?= number_format($besoin['quantite'] * ($besoin['prix_unitaire'] ?? 1), 0, ',', ' ') ?> Ar</strong></td>
                                    <td><?= date('d/m/Y', strtotime($besoin['date_de_saisie'] ?? 'now')) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dons reçus -->
            <div class="form-card" style="margin-top: 2rem;">
                <div class="section-title">
                    <h2>Dons reçus</h2>
                    <span class="badge-count"><?= $total_dons ?></span>
                </div>
                
                <?php if(empty($dons)): ?>
                    <div class="empty-message">
                        Aucun don reçu pour cette ville
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th>Date d'attribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($dons as $don): ?>
                                <tr>
                                    <td><?= htmlspecialchars($don['article_nom'] ?? 'Article') ?></td>
                                    <td><strong><?= number_format($don['quantite']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($don['date_dispatch'] ?? $don['date_de_saisie'] ?? 'now')) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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
                        <a href="/besoins">Besoins</a>
                        <a href="/dons">Dons</a>
                        <a href="/villes">Villes</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 BNGRC - Projet S3 - Système de gestion des dons</p>
            </div>
        </div>
    </footer>

    <script>
        // Navigation toggle
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function() {
                navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
            });
        }

        // Responsive table
        window.addEventListener('resize', function() {
            const tables = document.querySelectorAll('.table-wrapper');
            tables.forEach(table => {
                if (window.innerWidth < 768) {
                    table.style.overflowX = 'auto';
                }
            });
        });
    </script>
</body>
</html>