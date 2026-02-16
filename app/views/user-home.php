<div class="container">

    <div class="page-header">
        <div class="page-title">
            <h2>Mes objets</h2>
            <p>Liste des objets que tu possèdes actuellement</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un objet
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="fas fa-box"></i></div>
            </div>
            <div class="stat-value">12</div>
            <div class="stat-label">Objets possédés</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green"><i class="fas fa-right-left"></i></div>
            </div>
            <div class="stat-value">3</div>
            <div class="stat-label">Échanges réalisés</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
            </div>
            <div class="stat-value">2</div>
            <div class="stat-label">Échanges en attente</div>
        </div>
    </div>

    <!-- Section table -->
    <div class="section">
        <div class="section-header">
            <h3 class="section-title">Derniers objets ajoutés</h3>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Objet</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="item-name">
                                <div class="item-icon"><i class="fas fa-mobile-alt"></i></div>
                                Téléphone
                            </div>
                        </td>
                        <td>Électronique</td>
                        <td><span class="status active">Disponible</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="item-name">
                                <div class="item-icon"><i class="fas fa-book"></i></div>
                                Livre
                            </div>
                        </td>
                        <td>Culture</td>
                        <td><span class="status pending">En échange</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>