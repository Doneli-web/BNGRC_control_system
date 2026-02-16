document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.getElementById("addObjectBtn");
    const modal = document.getElementById("objectModal");
    const closeBtn = document.getElementById("closeObjectModal");
    const cancelBtn = document.getElementById("cancelObjectBtn");
    const form = document.getElementById("objectForm");
    const fileInput = document.getElementById("object_images");
    const previewContainer = document.getElementById("imagePreviewContainer");

    // Ouvrir le modal
    addBtn.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    // Fermer le modal
    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
        resetForm();
    });

    cancelBtn.addEventListener("click", function () {
        modal.style.display = "none";
        resetForm();
    });

    // Fermer le modal en cliquant sur l'overlay
    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
            resetForm();
        }
    });

    // Aperçu des images avant upload
    fileInput.addEventListener("change", function(e) {
        previewContainer.innerHTML = "";
        const files = Array.from(e.target.files);
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Aperçu ${index + 1}">
                        <button type="button" class="remove-image" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                    
                    // Ajouter l'événement pour supprimer l'image
                    previewItem.querySelector('.remove-image').addEventListener('click', function() {
                        previewItem.remove();
                        // Recréer le FileList sans le fichier supprimé
                        const dt = new DataTransfer();
                        const remainingFiles = Array.from(fileInput.files).filter((_, i) => i !== index);
                        remainingFiles.forEach(f => dt.items.add(f));
                        fileInput.files = dt.files;
                    });
                };
                
                reader.readAsDataURL(file);
            }
        });
    });

    // Soumettre le formulaire
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            // Désactiver le bouton pendant l'envoi
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
            
            const response = await fetch("/admin-object-create", {
                method: "POST",
                body: formData
            });
            
            if (response.ok) {
                // Fermer le modal
                modal.style.display = "none";
                resetForm();
                
                // Afficher un message de succès
                showNotification("Objet ajouté avec succès!", "success");
                
                // Recharger la page après un court délai
                setTimeout(() => {
                    window.location.href = "/admin-objects";
                }, 1000);
            } else {
                throw new Error("Erreur lors de l'ajout");
            }
        } catch (error) {
            console.error("Erreur:", error);
            showNotification("Erreur lors de l'ajout de l'objet", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Fonction pour réinitialiser le formulaire
    function resetForm() {
        form.reset();
        previewContainer.innerHTML = "";
    }

    // Fonction pour afficher les notifications
    function showNotification(message, type = "success") {
        // Créer l'élément de notification
        const notification = document.createElement("div");
        notification.className = `notification-popup ${type}`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Animation d'entrée
        setTimeout(() => notification.classList.add("show"), 100);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.classList.remove("show");
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Ajouter la validation du prix en temps réel
    const priceInput = document.getElementById("object_price");
    priceInput.addEventListener("input", function() {
        let value = this.value.replace(/\s/g, '').replace(/\D/g, '');
        if (value) {
            // Formater avec des espaces tous les 3 chiffres
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }
    });
});