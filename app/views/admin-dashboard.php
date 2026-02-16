<div class="page-header">
    <div class="page-title">
        <h2>Tableau de bord</h2>
        <p>Vue d'ensemble de votre plateforme d'échange et vente</p>
    </div>
    
    <div class="page-actions">
        <button class="btn btn-secondary">
            <i class="fas fa-download"></i>
            Exporter
        </button>
        <button class="btn btn-primary" id="addObjectBtn">
            <i class="fas fa-plus"></i>
            Ajouter un objet
        </button>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon blue">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-trend positive">
                <i class="fas fa-arrow-up"></i> 12%
            </div>
        </div>
        <div class="stat-value">1,248</div>
        <div class="stat-label">Objets actifs</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon green">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-trend positive">
                <i class="fas fa-arrow-up"></i> 8%
            </div>
        </div>
        <div class="stat-value">342</div>
        <div class="stat-label">Échanges ce mois</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-trend positive">
                <i class="fas fa-arrow-up"></i> 5%
            </div>
        </div>
        <div class="stat-value">2,158</div>
        <div class="stat-label">Utilisateurs actifs</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon red">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-trend negative">
                <i class="fas fa-arrow-down"></i> 3%
            </div>
        </div>
        <div class="stat-value">47</div>
        <div class="stat-label">En attente de validation</div>
    </div>
</div>

<!-- Contenu principal -->
<div class="content-grid">
    <!-- Section Objets récents -->
    <div class="section">
        <div class="section-header">
            <h3 class="section-title">Objets récents</h3>
            <a href="#" class="section-link">Voir tout</a>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Objet</th>
                        <th>Catégorie</th>
                        <th>Propriétaire</th>
                        <th>Date d'ajout</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="item-name">
                                <div class="item-icon">
                                    <i class="fas fa-laptop"></i>
                                </div>
                                <div>
                                    <div>Laptop Dell XPS 15</div>
                                    <div class="category-count">Électronique</div>
                                </div>
                            </div>
                        </td>
                        <td>Électronique</td>
                        <td>Jean Dupont</td>
                        <td>12/03/2023</td>
                        <td>
                            <span class="status active">Actif</span>
                        </td>
                        <td>
                            <div class="category-actions">
                                <button class="icon-btn">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="item-name">
                                <div class="item-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <div>Collection Harry Potter</div>
                                    <div class="category-count">Livres</div>
                                </div>
                            </div>
                        </td>
                        <td>Livres</td>
                        <td>Marie Curie</td>
                        <td>10/03/2023</td>
                        <td>
                            <span class="status pending">En attente</span>
                        </td>
                        <td>
                            <div class="category-actions">
                                <button class="icon-btn">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="item-name">
                                <div class="item-icon">
                                    <i class="fas fa-couch"></i>
                                </div>
                                <div>
                                    <div>Canapé en cuir</div>
                                    <div class="category-count">Meubles</div>
                                </div>
                            </div>
                        </td>
                        <td>Meubles</td>
                        <td>Paul Martin</td>
                        <td>08/03/2023</td>
                        <td>
                            <span class="status completed">Échangé</span>
                        </td>
                        <td>
                            <div class="category-actions">
                                <button class="icon-btn">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <div class="item-name">
                                <div class="item-icon">
                                    <i class="fas fa-bicycle"></i>
                                </div>
                                <div>
                                    <div>Vélo de route</div>
                                    <div class="category-count">Sports</div>
                                </div>
                            </div>
                        </td>
                        <td>Sports</td>
                        <td>Lucie Bernard</td>
                        <td>05/03/2023</td>
                        <td>
                            <span class="status active">Actif</span>
                        </td>
                        <td>
                            <div class="category-actions">
                                <button class="icon-btn">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="icon-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Section Catégories -->
    <div class="section">
        <div class="section-header">
            <h3 class="section-title">Catégories principales</h3>
            <a href="#" class="section-link">Gérer</a>
        </div>
        
        <ul class="category-list">
            <li class="category-item">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div>
                        <div class="category-name">Électronique</div>
                        <div class="category-count">312 objets</div>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="icon-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </li>
            
            <li class="category-item">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <div class="category-name">Livres</div>
                        <div class="category-count">198 objets</div>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="icon-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </li>
            
            <li class="category-item">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div>
                        <div class="category-name">Vêtements</div>
                        <div class="category-count">156 objets</div>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="icon-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </li>
            
            <li class="category-item">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-couch"></i>
                    </div>
                    <div>
                        <div class="category-name">Meubles</div>
                        <div class="category-count">87 objets</div>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="icon-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </li>
            
            <li class="category-item">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div>
                        <div class="category-name">Sports</div>
                        <div class="category-count">124 objets</div>
                    </div>
                </div>
                <div class="category-actions">
                    <button class="icon-btn">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </li>
        </ul>
    </div>
</div>