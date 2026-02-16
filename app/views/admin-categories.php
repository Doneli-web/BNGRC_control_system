<div class="page-header">
    <div class="page-title">
        <h2>Catégories</h2>
        <p>Gérer les catégories d’objets de la plateforme</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" id="addCategoryBtn">
            <i class="fas fa-plus"></i> Ajouter une catégorie
        </button>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <div class="section-title">Liste des catégories</div>
    </div>

    <ul class="category-list">
        <?php foreach ($categories as $cat): ?>
            <li class="category-item">
                <div class="category-info">
                    <div class="category-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div>
                        <div class="category-name"><?= htmlspecialchars($cat["name"]) ?></div>
                        <div class="category-count"><?= $cat["countObject"] ?> objets</div>
                    </div>
                </div>

                <div class="category-actions">
                    <!-- Modifier -->
                    <a href="/admin-categories-edit?id=<?= $cat["id"] ?>" class="icon-btn" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Supprimer -->
                    <a href="/admin-categories-delete/<?= $cat["id"] ?>" 
                       class="icon-btn danger" 
                       title="Supprimer"
                       onclick="return confirm('Supprimer cette catégorie ?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="modal-overlay" id="categoryModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Ajouter une catégorie</h3>
            <button class="modal-close" id="closeCategoryModal">&times;</button>
        </div>

        <form class="modal-body">
            <div class="form-group">
                <label for="category_name">Nom de la catégorie</label>
                <input type="text" name="category_name" id="category_name" required placeholder="Ex: Informatique">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancelCategoryBtn">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
