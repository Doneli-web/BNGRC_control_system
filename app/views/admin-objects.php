<?php
// Récupérer les objets depuis le contrôleur (passés par la route)
$objects = $objects ?? [];

// Récupérer les catégories pour le formulaire d'ajout
$categories = \app\controllers\CategoryController::getAllCategory();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Objets</h2>
        <p>Gérez tous les objets disponibles sur la plateforme</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary" id="filterBtn">
            <i class="fas fa-filter"></i> Filtrer
        </button>
        <button class="btn btn-primary" id="addObjectBtn">
            <i class="fas fa-plus"></i> Ajouter un objet
        </button>
    </div>
</div>

<!-- Modal d'ajout d'objet -->
<div class="modal" id="addObjectModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Ajouter un nouvel objet</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="/admin-object-create" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Nom de l'objet</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="id_category">Catégorie</label>
                    <select id="id_category" name="id_category" class="form-control" required>
                        <option value="">Sélectionner une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['idCategory'] ?? $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="prix_estimatif">Prix estimatif (Ar)</label>
                    <input type="number" id="prix_estimatif" name="prix_estimatif" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="images">Images</label>
                    <input type="file" id="images" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="form-text">Vous pouvez sélectionner plusieurs images</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-add">Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div class="stats-grid">
    <?php
    $total_objects = count($objects);
    $active_objects = 0;
    $pending_objects = 0;
    
    foreach ($objects as $object) {
        // Vous pouvez ajouter un champ 'status' à votre table Objet
        if (isset($object['status'])) {
            if ($object['status'] == 'active') $active_objects++;
            if ($object['status'] == 'pending') $pending_objects++;
        }
    }
    ?>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon blue">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-trend positive">+<?= rand(5, 15) ?>%</div>
        </div>
        <div class="stat-value"><?= $total_objects ?></div>
        <div class="stat-label">Objets totaux</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-trend positive">+<?= rand(5, 15) ?>%</div>
        </div>
        <div class="stat-value"><?= $active_objects ?></div>
        <div class="stat-label">Objets actifs</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-trend">-</div>
        </div>
        <div class="stat-value"><?= $pending_objects ?></div>
        <div class="stat-label">En attente</div>
    </div>
</div>

<div class="objects-grid">
    <?php if (empty($objects)): ?>
        <div class="no-data">
            <i class="fas fa-box-open" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>Aucun objet trouvé</h3>
            <p>Commencez par ajouter un nouvel objet</p>
        </div>
    <?php else: ?>
        <?php foreach ($objects as $object): ?>
        <div class="object-card" data-object-id="<?= $object['idObjet'] ?? $object['id_category'] ?>">
            <div class="object-image">
                <img src="/assets/<?= $object['main_image']?>" 
                     alt="<?= htmlspecialchars($object['nomObjet'] ?? $object['name']) ?>"
                     onerror="this.src='/assets/images/placeholder.jpg'">
                <div class="object-status status <?= $object['status'] ?? 'active' ?>">
                    <?= ucfirst($object['status'] ?? 'Actif') ?>
                </div>
            </div>
            
            <div class="object-content">
                <div class="object-header">
                    <h3 class="object-name"><?= htmlspecialchars($object['nomObjet'] ?? $object['name']) ?></h3>
                    <div class="object-price"><?= number_format($object['prix_estimatif'], 0, ',', ' ') ?> Ar</div>
                </div>
                
                <div class="object-meta">
                    <span class="object-category">
                        <i class="fas fa-tag"></i> 
                        <?php
                        // Récupérer le nom de la catégorie
                        $cat_name = 'Non catégorisé';
                        foreach ($categories as $cat) {
                            if (($cat['idCategory'] ?? $cat['id']) == ($object['idCategorie'] ?? $object['id_category'])) {
                                $cat_name = $cat['name'];
                                break;
                            }
                        }
                        echo htmlspecialchars($cat_name);
                        ?>
                    </span>
                    <span class="object-owner">
                        <i class="fas fa-user"></i> <?= $object['owner'] ?? 'Admin' ?>
                    </span>
                </div>
                
                <div class="object-description-toggle">
                    <label class="toggle-label">
                        <input type="checkbox" class="description-toggle" data-object-id="<?= $object['idObjet'] ?? $object['id_category'] ?>">
                        <span class="toggle-text">Afficher description</span>
                    </label>
                    <div class="object-description" id="description-<?= $object['idObjet'] ?? $object['id_category'] ?>">
                        <?= htmlspecialchars($object['description'] ?? 'Aucune description') ?>
                    </div>
                </div>
                
                <div class="object-actions">
                    <button class="btn btn-secondary view-details" data-object-id="<?= $object['idObjet'] ?? $object['id_category'] ?>">
                        <i class="fas fa-eye"></i> Détails
                    </button>
                    <div class="action-buttons">
                        <button class="icon-btn edit-object" data-object-id="<?= $object['idObjet'] ?? $object['id_category'] ?>" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="icon-btn delete-object" data-object-id="<?= $object['idObjet'] ?? $object['id_category'] ?>" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmer la suppression</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer cet objet ? Cette action est irréversible.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary cancel-delete">Annuler</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</a>
        </div>
    </div>
</div>

<!-- Modal de détails d'objet -->
<div class="modal" id="detailsModal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Détails de l'objet</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body" id="detailsModalBody">
            <!-- Chargé dynamiquement via AJAX -->
        </div>
    </div>
</div>


<!-- Modal d'ajout d'objet -->
<div class="modal-overlay" id="objectModal">
    <div class="modal-box" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Ajouter un objet</h3>
            <button class="modal-close" id="closeObjectModal">&times;</button>
        </div>

        <form class="modal-body" id="objectForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="object_name">Nom de l'objet</label>
                <input type="text" name="name" id="object_name" required placeholder="Ex: iPhone 13 Pro">
            </div>

            <div class="form-group">
                <label for="object_category">Catégorie</label>
                <select name="id_category" id="object_category" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?? $cat['idCategory'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="object_description">Description</label>
                <textarea name="description" id="object_description" rows="4" required placeholder="Description détaillée de l'objet..."></textarea>
            </div>

            <div class="form-group">
                <label for="object_price">Prix estimatif (Ar)</label>
                <input type="number" name="prix_estimatif" id="object_price" required placeholder="Ex: 500000">
            </div>

            <div class="form-group">
                <label for="object_images">Images</label>
                <div class="file-upload-area">
                    <input type="file" name="images[]" id="object_images" multiple accept="image/*">
                    <div class="file-upload-hint">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Cliquez ou glissez vos images ici</p>
                        <small>Formats acceptés: JPG, PNG, GIF (Max 5Mo)</small>
                    </div>
                </div>
                <div id="imagePreviewContainer" class="image-preview-grid"></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancelObjectBtn">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>