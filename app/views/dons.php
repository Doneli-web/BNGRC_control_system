<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Dons - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .alert {
            padding: 1rem;
            margin: 1rem auto;
            max-width: 1400px;
            border-radius: 8px;
            text-align: center;
        }
        .alert.success {
            background: rgba(74, 139, 111, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        .alert.error {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
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
                <li><a href="/dons" class="active">Dons</a></li>
                <li><a href="/villes">Villes</a></li>
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

    <!-- Messages de notification -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert success">
            <?= $_SESSION['success']; 
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert error">
            <?= $_SESSION['error']; 
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb">
                <a href="/">Accueil</a>
                <span>/</span>
                <span>Gestion des dons</span>
            </div>
            <h1 class="page-title">Gestion des dons</h1>
            <p class="page-description">Enregistrez les dons reçus et suivez leur répartition</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="content-container">
            <!-- Add Donation Form -->
            <div class="form-card">
                <div class="form-header">
                    <h2>Enregistrer un don</h2>
                    <button class="btn-icon" onclick="resetForm()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                    </button>
                </div>
                <form action="/dons/add" method="POST" class="styled-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="idArticle">Article *</label>
                            <select id="idArticle" name="idArticle" required>
                                <option value="">Sélectionner un article</option>
                                <?php foreach($articles as $article): ?>
                                <option value="<?= $article['id'] ?>" 
                                        data-prix="<?= $article['prix_unitaire'] ?>">
                                    <?= htmlspecialchars($article['name']) ?> - <?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> Ar
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantite">Quantité *</label>
                            <input type="number" id="quantite" name="quantite" min="1" step="1" required 
                                   placeholder="Ex: 100">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prixUnitaire">Prix unitaire (Ar)</label>
                            <input type="text" id="prixUnitaire" class="total-display" readonly>
                        </div>
                        <div class="form-group">
                            <label for="valeurTotale">Valeur totale (Ar)</label>
                            <input type="text" id="valeurTotale" class="total-display" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dateReception">Date de réception</label>
                        <input type="date" id="dateReception" name="dateReception" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="resetForm()">Annuler</button>
                        <button type="submit" class="btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Enregistrer le don
                        </button>
                    </div>
                </form>
            </div>

            <!-- Donations Statistics -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-icon-large">📦</div>
                    <div class="stat-content">
                        <h3>Total des dons</h3>
                        <p class="stat-number"><?= $total_dons ?></p>
                        <span class="stat-change">enregistrements</span>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon-large">💰</div>
                    <div class="stat-content">
                        <h3>Valeur totale</h3>
                        <?php 
                        $valeur_totale = 0;
                        foreach($dons as $don) {
                            $valeur_totale += $don['quantite'] * ($don['prix_unitaire'] ?? 1);
                        }
                        ?>
                        <p class="stat-number"><?= number_format($valeur_totale, 0, ',', ' ') ?> Ar</p>
                        <span class="stat-change">tous dons confondus</span>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon-large">📊</div>
                    <div class="stat-content">
                        <h3>Articles différents</h3>
                        <p class="stat-number"><?= count($articles) ?></p>
                        <span class="stat-change">dans le catalogue</span>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon-large">📅</div>
                    <div class="stat-content">
                        <h3>Année</h3>
                        <p class="stat-number">2026</p>
                        <span class="stat-change">opérations en cours</span>
                    </div>
                </div>
            </div>

            <!-- Donations List -->
            <div class="list-section">
                <div class="list-header">
                    <h2>Historique des dons</h2>
                    <div class="list-actions">
                        <input type="search" class="search-input" placeholder="Rechercher..." id="searchInput">
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Valeur totale</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donsTableBody">
                            <?php if(empty($dons)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    Aucun don enregistré pour le moment
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($dons as $don): ?>
                                <tr>
                                    <td>#<?= $don['id'] ?></td>
                                    <td>
                                        <span class="city-tag">
                                            <?= htmlspecialchars($don['article_nom'] ?? 'Article ' . $don['idArticle']) ?>
                                        </span>
                                    </td>
                                    <td><strong><?= number_format($don['quantite']) ?></strong></td>
                                    <td><?= number_format($don['prix_unitaire'] ?? 0, 0, ',', ' ') ?> Ar</td>
                                    <td><strong><?= number_format($don['quantite'] * ($don['prix_unitaire'] ?? 1), 0, ',', ' ') ?> Ar</strong></td>
                                    <td><?= date('d/m/Y', strtotime($don['date_de_saisie'] ?? 'now')) ?></td>
                                    <td>
                                        <div class="action-buttons-small">
                                            <a href="/dons/delete/<?= $don['id'] ?>" class="btn-icon-small delete" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?')"
                                               title="Supprimer">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                        <a href="/dons">Gestion des dons</a>
                        <a href="/villes">Villes</a>
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

    
    <script src="/assets/js/dons.js"></script>
</body>
</html>