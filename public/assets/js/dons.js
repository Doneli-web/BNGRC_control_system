
// Variables globales
let donsData = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initCalculs();
    initSearch();
    setCurrentDate();
});

// Navigation
function initNavigation() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
    }
}

// Initialiser les calculs automatiques
function initCalculs() {
    const articleSelect = document.getElementById('idArticle');
    const quantiteInput = document.getElementById('quantite');
    
    if (articleSelect) {
        articleSelect.addEventListener('change', function() {
            updatePrixUnitaire();
            calculerTotal();
        });
    }
    
    if (quantiteInput) {
        quantiteInput.addEventListener('input', function() {
            calculerTotal();
        });
    }
    
    // Calcul initial si des valeurs sont déjà sélectionnées
    if (articleSelect && articleSelect.value && quantiteInput && quantiteInput.value) {
        updatePrixUnitaire();
        calculerTotal();
    }
}

// Initialiser la recherche
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            filterDons(this.value);
        });
    }
}

// Mettre à jour le prix unitaire quand l'article change
function updatePrixUnitaire() {
    const select = document.getElementById('idArticle');
    if (!select) return;
    
    const selected = select.options[select.selectedIndex];
    const prix = selected?.dataset?.prix;
    
    const prixInput = document.getElementById('prixUnitaire');
    if (prixInput) {
        prixInput.value = prix ? Number(prix).toLocaleString('fr-FR') + ' Ar' : '';
    }
}

// Calculer la valeur totale
function calculerTotal() {
    const select = document.getElementById('idArticle');
    if (!select) return;
    
    const selected = select.options[select.selectedIndex];
    const prix = parseFloat(selected?.dataset?.prix) || 0;
    const quantite = parseFloat(document.getElementById('quantite').value) || 0;
    
    const total = prix * quantite;
    const totalInput = document.getElementById('valeurTotale');
    if (totalInput) {
        totalInput.value = total ? total.toLocaleString('fr-FR') + ' Ar' : '';
    }
}

// Set current date
function setCurrentDate() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('dateReception');
    if (dateInput && !dateInput.value) {
        dateInput.value = today;
    }
}

// Reset formulaire
function resetForm() {
    document.getElementById('idArticle').value = '';
    document.getElementById('quantite').value = '';
    document.getElementById('prixUnitaire').value = '';
    document.getElementById('valeurTotale').value = '';
    
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateReception').value = today;
}

// Filtrer les dons
function filterDons(searchTerm) {
    const rows = document.querySelectorAll('#donsTableBody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        // Ignorer la ligne "Aucun don"
        if (row.cells.length === 1 && row.cells[0].colSpan === 7) return;
        
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

// Rafraîchir la page après ajout/suppression
function refreshPage() {
    location.reload();
}

// Exposer les fonctions globalement
window.resetForm = resetForm;
window.filterDons = filterDons;
window.refreshPage = refreshPage;