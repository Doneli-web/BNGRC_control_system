<?php 
ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Besoins - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="/assets/js/besoins.js" defer></script>
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
                <li><a href="/besoins" class="active">Besoins</a></li>
                <li><a href="/dons">Dons</a></li>
                <li><a href="/villes">Villes</a></li>
                <li><a href="/achats" class="active">Achats</a></li>
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb">
                <a href="index.html">Accueil</a>
                <span>/</span>
                <span>Gestion des besoins</span>
            </div>
            <h1 class="page-title">Gestion des besoins</h1>
            <p class="page-description">Enregistrez et suivez les besoins des sinistrés par ville et par catégorie</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="content-container">
            <!-- Add Need Form -->
            <div class="form-card">
                <div class="form-header">
                    <h2>Ajouter un besoin</h2>
                    <button class="btn-icon" onclick="resetForm()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                    </button>
                </div>
                <form action="/besoins/add" method="post" id="besoinForm" class="styled-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ville">Ville *</label>
                            <select id="ville" required name="ville">
                                <option value="">Sélectionner une ville</option>
                                <?php foreach($villes as $v) { ?>
                                    <option value="<?= $v["id"] ?>"><?= $v["name"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="categorie">Catégorie *</label>
                            <select id="categorie" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach($typeDon as $td) { ?>
                                    <option value="<?= $td["id"] ?>"><?= $td["name"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="article">Article *</label>
                            <select id="article" name="article" required disabled>
                                <option value="">Sélectionner une catégorie d'abord</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantite">Quantité *</label>
                            <input type="number" id="quantite" min="1" step="1" value="1" required 
                                   placeholder="Ex: 100" name="quantite">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prixUnitaire">Prix unitaire (Ar) *</label>
                            <input type="number" id="prixUnitaire" min="0" step="0.01" required 
                                   placeholder="Ex: 5000" readonly>
                        </div>
                        <div class="form-group">
                            <label for="montantTotal">Montant total (Ar)</label>
                            <input type="text" id="montantTotal" readonly class="total-display">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="resetForm()">Annuler</button>
                        <button type="submit" class="btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Enregistrer le besoin
                        </button>
                    </div>
                </form>
            </div>

            <!-- Needs List -->
            <div class="list-section">
                <div class="list-header">
                    <h2>Liste des besoins enregistrés</h2>
                    <div class="list-actions">
                        <input type="search" class="search-input" placeholder="Rechercher..." 
                               onkeyup="filterBesoins(this.value)">
                        <select class="filter-select-small" onchange="filterByVille(this.value)">
                            <option value="">Toutes les villes</option>
                            <?php foreach($villes as $v) { ?>
                                <option value="<?= $v["name"] ?>"><?= $v["name"] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Ville</th>
                                <th>Catégorie</th>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Montant total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="besoinsTableBody">
                            <?php if(empty($besoins)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    Aucun besoin enregistré pour le moment
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($besoins as $besoin): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($besoin['date_de_saisie'])) ?></td>
                                    <td><span class="city-tag"><?= htmlspecialchars($besoin['ville_name']) ?></span></td>
                                    <td><span class="cat-badge"><?= htmlspecialchars($besoin['type_name']) ?></span></td>
                                    <td><?= htmlspecialchars($besoin['article_name']) ?></td>
                                    <td><?= number_format($besoin['quantite']) ?></td>
                                    <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                    <td><strong><?= number_format($besoin['prix_unitaire'] * $besoin['quantite'], 0, ',', ' ') ?> Ar</strong></td>
                                    <td>
                                        <div class="action-buttons-small">
                                            <button class="btn-icon-small" title="Modifier">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <button class="btn-icon-small delete" title="Supprimer">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
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
<?php 
    session_start();
    if(isset($_SESSION["message"])){ ?>
        <script>alert('<?php echo $_SESSION["message"]; ?>');</script>
        <?php 
            unset($_SESSION["message"]);
        ?>
<?php } ?>
</body>
</html>
