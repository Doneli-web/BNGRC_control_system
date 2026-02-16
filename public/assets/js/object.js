document.addEventListener('DOMContentLoaded', function() {
    // Navigation vers la page de détails
    const objectCards = document.querySelectorAll('.object-card');
    objectCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Ne pas déclencher la navigation si on clique sur les boutons d'action
            if (!e.target.closest('.object-actions') && 
                !e.target.closest('.object-description-toggle') &&
                !e.target.closest('.icon-btn')) {
                const objectId = this.dataset.objectId;
                window.location.href = `/admin/object-details.php?id=${objectId}`;
            }
        });
    });

    // Boutons "Détails" explicites
    const viewButtons = document.querySelectorAll('.view-details');
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const objectId = this.dataset.objectId;
            window.location.href = `/admin/object-details.php?id=${objectId}`;
        });
    });

    // Toggle des descriptions
    // Toggle des descriptions (ouvrir / fermer)
    const descriptionToggles = document.querySelectorAll('.description-toggle');

    descriptionToggles.forEach(toggle => {
        toggle.addEventListener('change', function (e) {
            e.stopPropagation();

            const description = document.getElementById(
                `description-${this.dataset.objectId}`
            );

            if (this.checked) {
                // Ouvrir
                description.style.maxHeight = description.scrollHeight + 'px';
            } else {
                // Fermer
                description.style.maxHeight = '0';
            }
        });
});



    // Bouton modifier
    const editButtons = document.querySelectorAll('.edit-object');
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const objectId = this.dataset.objectId;
            // Ici vous pouvez ouvrir un modal d'édition ou rediriger vers une page d'édition
            alert(`Modifier l'objet ${objectId}`);
            // window.location.href = `/admin/edit-object.php?id=${objectId}`;
        });
    });

    // Bouton supprimer
    const deleteButtons = document.querySelectorAll('.delete-object');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.querySelector('.cancel-delete');
    const confirmDeleteBtn = document.querySelector('.confirm-delete');
    const closeModalBtn = document.querySelector('.close-modal');
    let objectToDelete = null;

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            objectToDelete = this.dataset.objectId;
            deleteModal.style.display = 'flex';
        });
    });

    // Gestion du modal de suppression
    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.style.display = 'none';
        objectToDelete = null;
    });

    closeModalBtn.addEventListener('click', function() {
        deleteModal.style.display = 'none';
        objectToDelete = null;
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (objectToDelete) {
            // Ici, ajoutez votre logique de suppression (AJAX ou redirection)
            console.log(`Suppression de l'objet ${objectToDelete}`);
            
            // Simuler une suppression avec une animation
            const cardToRemove = document.querySelector(`[data-object-id="${objectToDelete}"]`);
            if (cardToRemove) {
                cardToRemove.style.opacity = '0';
                cardToRemove.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    cardToRemove.remove();
                    // En production, vous feriez une requête AJAX ici
                    alert(`Objet ${objectToDelete} supprimé avec succès`);
                }, 300);
            }
            
            deleteModal.style.display = 'none';
            objectToDelete = null;
        }
    });

    // Fermer le modal en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
            objectToDelete = null;
        }
    });
});