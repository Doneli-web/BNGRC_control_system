<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achats - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .config-card {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .config-card .form-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .config-card input {
            width: 100px;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
        }
        .calcul-result {
            background: var(--bg-tertiary);
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 8px;
        }
        .calcul-result p {
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }
        .calcul-result .total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        .alert-success {
            background: rgba(74, 139, 111, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }
        .alert-error {
            background: rgba(196, 69, 54, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        .filter-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .btn-calculer {
            background: var(--info);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-calculer:hover {
            background: #3a6a8c;
        }
        .btn-acheter {
            background: var(--success);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-acheter:hover {
            background: #3b7a5c;
        }
        .last-update {
            text-align: right;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
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

    <!-- Messages de notification -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb">
                <a href="/">Accueil</a>
                <span>/</span>
                <span>Achats via dons en argent</span>
            </div>
            <h1 class="page-title">Achats de besoins</h1>
            <p class="page-description">Utilisez les dons en argent pour acheter des besoins en nature et matériaux</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="content-main">
        <div class="content-container">
            <!-- Configuration des frais -->
            <div class="config-card">
                <h2 style="margin-bottom: 1rem;">Configuration des frais</h2>
                <div class="form-group">
                    <label for="fraisInput">Frais d'achat (%):</label>
                    <input type="number" id="fraisInput" min="0" max="100" step="0.1" value="<?= $frais ?? 10 ?>">
                    <button class="btn-primary" onclick="updateFrais()">Mettre à jour</button>
                    <span id="fraisMessage" style="margin-left: 1rem;"></span>
                </div>
            </div>

            <!-- Formulaire d'achat -->
            <div class="form-card">
                <h2>Nouvel achat</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="donSelect">Don en argent disponible</label>
                        <select id="donSelect" class="form-control" onchange="loadDonDetails()">
                            <option value="">Sélectionner un don</option>
                            <?php if(!empty($dons_argent)): ?>
                                <?php foreach($dons_argent as $don): ?>
                                <option value="<?= $don['id'] ?>" 
                                        data-montant="<?= $don['quantite'] ?>"
                                        data-donateur="<?= htmlspecialchars($don['donateur'] ?? 'Anonyme') ?>">
                                    <?= htmlspecialchars($don['donateur'] ?? 'Anonyme') ?> - <?= number_format($don['quantite']) ?> Ar (<?= $don['date_de_saisie'] ?>)
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="besoinSelect">Besoin à acheter</label>
                        <select id="besoinSelect" class="form-control" onchange="loadBesoinDetails()">
                            <option value="">Sélectionner un besoin</option>
                            <?php if(!empty($besoins)): ?>
                                <?php foreach($besoins as $besoin): ?>
                                <option value="<?= $besoin['id'] ?>"
                                        data-article="<?= htmlspecialchars($besoin['article_nom']) ?>"
                                        data-prix="<?= $besoin['prix_unitaire'] ?>"
                                        data-quantite="<?= $besoin['quantite'] ?>"
                                        data-ville="<?= htmlspecialchars($besoin['ville_nom'] ?? 'Inconnue') ?>">
                                    <?= htmlspecialchars($besoin['ville_nom'] ?? 'Inconnue') ?> - 
                                    <?= htmlspecialchars($besoin['article_nom']) ?> - 
                                    <?= number_format($besoin['quantite']) ?> unités
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="montantInput">Montant à utiliser (Ar)</label>
                        <input type="number" id="montantInput" class="form-control" min="1000" step="1000" placeholder="Ex: 500000">
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="btn-calculer" onclick="calculerAchat()">Calculer</button>
                    </div>
                </div>

                <!-- Résultat du calcul -->
                <div id="calculResultat" class="calcul-result" style="display: none;">
                    <h3>Résultat du calcul</h3>
                    <p>💰 Montant à utiliser: <strong><span id="montantAffiche">0</span> Ar</strong></p>
                    <p>📦 Quantité achetable: <strong><span id="quantiteAffiche">0</span> unités</strong></p>
                    <p>📊 Frais (<span id="fraisPourcentageAffiche"><?= $frais ?? 10 ?></span>%): <span id="fraisMontantAffiche">0</span> Ar</p>
                    <p class="total">💵 TOTAL À PAYER: <span id="totalAffiche">0</span> Ar</p>
                    <div class="form-actions">
                        <button class="btn-acheter" onclick="effectuerAchat()" id="btnAcheter">Confirmer l'achat</button>
                        <button class="btn-secondary" onclick="resetFormulaire()">Annuler</button>
                    </div>
                </div>

                <!-- Messages -->
                <div id="messageContainer" style="margin-top: 1rem;"></div>
            </div>

            <!-- Filtre par ville -->
            <div class="filter-section">
                <label for="villeFilter">Filtrer par ville:</label>
                <select id="villeFilter" class="filter-select" onchange="filterByVille(this.value)">
                    <option value="">Toutes les villes</option>
                    <?php if(!empty($villes)): ?>
                        <?php foreach($villes as $ville): ?>
                        <option value="<?= $ville['id'] ?>"><?= htmlspecialchars($ville['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="last-update" id="lastUpdate"></span>
            </div>

            <!-- Historique des achats -->
            <div class="list-section">
                <h2>Historique des achats (<?= $total_achats ?? 0 ?>)</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Ville</th>
                                <th>Montant utilisé</th>
                                <th>Frais</th>
                                <th>Total payé</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="historiqueTableBody">
                            <?php if(empty($achats)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Aucun achat effectué</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($achats as $achat): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($achat['date_achat'] ?? 'now')) ?></td>
                                    <td><?= htmlspecialchars($achat['ville_nom'] ?? 'Inconnue') ?></td>
                                    <td><?= number_format($achat['montant_utilise'] ?? 0) ?> Ar</td>
                                    <td><?= number_format($achat['frais_montant'] ?? 0) ?> Ar (<?= $achat['frais_pourcentage'] ?? 0 ?>%)</td>
                                    <td><strong><?= number_format($achat['montant_total'] ?? 0) ?> Ar</strong></td>
                                    <td>
                                        <a href="/achats/delete/<?= $achat['id'] ?>" class="btn-icon-small delete" 
                                           onclick="return confirm('Supprimer cet achat ?')">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </a>
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
        // Variables globales depuis PHP
        const fraisInitial = <?= $frais ?? 10 ?>;
        
        // Fonctions JavaScript
        function updateFrais() {
            const frais = document.getElementById('fraisInput').value;
            
            fetch('/achats/frais/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'frais=' + frais
            }).then(() => {
                window.location.reload();
            });
        }

        function loadDonDetails() {
            // Récupérer les infos du don sélectionné
            const select = document.getElementById('donSelect');
            const option = select.options[select.selectedIndex];
            const montant = option.dataset.montant;
            
            document.getElementById('montantInput').max = montant;
        }

        function loadBesoinDetails() {
            // Récupérer les infos du besoin sélectionné
            const select = document.getElementById('besoinSelect');
            const option = select.options[select.selectedIndex];
        }

        function calculerAchat() {
            const idDon = document.getElementById('donSelect').value;
            const idBesoin = document.getElementById('besoinSelect').value;
            const montant = document.getElementById('montantInput').value;
            
            if(!idDon || !idBesoin || !montant) {
                alert('Veuillez remplir tous les champs');
                return;
            }
            
            const formData = new FormData();
            formData.append('idDon', idDon);
            formData.append('idBesoin', idBesoin);
            formData.append('montant', montant);
            
            fetch('/achats/calculer', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('montantAffiche').textContent = data.data.montant_utilise;
                    document.getElementById('quantiteAffiche').textContent = data.data.quantite_achetee;
                    document.getElementById('fraisPourcentageAffiche').textContent = data.data.frais_pourcentage;
                    document.getElementById('fraisMontantAffiche').textContent = data.data.frais_montant;
                    document.getElementById('totalAffiche').textContent = data.data.montant_total;
                    document.getElementById('calculResultat').style.display = 'block';
                } else {
                    alert(data.message);
                }
            });
        }

        function effectuerAchat() {
            // Récupérer les valeurs du calcul
            const idDon = document.getElementById('donSelect').value;
            const idBesoin = document.getElementById('besoinSelect').value;
            const montant_utilise = document.getElementById('montantAffiche').textContent;
            const frais_pourcentage = document.getElementById('fraisPourcentageAffiche').textContent;
            const frais_montant = document.getElementById('fraisMontantAffiche').textContent;
            const montant_total = document.getElementById('totalAffiche').textContent;
            
            const formData = new FormData();
            formData.append('idDon', idDon);
            formData.append('idBesoin', idBesoin);
            formData.append('montant_utilise', montant_utilise);
            formData.append('frais_pourcentage', frais_pourcentage);
            formData.append('frais_montant', frais_montant);
            formData.append('montant_total', montant_total);
            
            fetch('/achats/effectuer', {
                method: 'POST',
                body: formData
            }).then(() => {
                window.location.reload();
            });
        }

        function resetFormulaire() {
            document.getElementById('donSelect').value = '';
            document.getElementById('besoinSelect').value = '';
            document.getElementById('montantInput').value = '';
            document.getElementById('calculResultat').style.display = 'none';
        }

        function filterByVille(villeId) {
            if(villeId) {
                window.location.href = '/achats/ville/' + villeId;
            } else {
                window.location.href = '/achats';
            }
        }

        // Navigation toggle
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.querySelector('.nav-menu');
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function() {
                navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
            });
        }
    </script>
</body>
</html>