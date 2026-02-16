// ===================================
// BNGRC - Villes Management Script
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initSearch();
    initSort();
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

// Recherche
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            filterVilles(this.value);
        });
    }
}

// Tri
function initSort() {
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortVilles(this.value);
        });
    }
}

// Filtrer les villes
function filterVilles(searchTerm) {
    const cards = document.querySelectorAll('.city-card');
    const term = searchTerm.toLowerCase();
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(term) ? '' : 'none';
    });
}

// Trier les villes
function sortVilles(criteria) {
    const grid = document.getElementById('villesGrid');
    const cards = Array.from(document.querySelectorAll('.city-card'));
    
    cards.sort((a, b) => {
        if (criteria === 'nom') {
            return a.dataset.nom.localeCompare(b.dataset.nom);
        } else if (criteria === 'besoins') {
            return (parseInt(b.dataset.besoins) || 0) - (parseInt(a.dataset.besoins) || 0);
        } else if (criteria === 'priorite') {
            const prioriteOrder = { 'urgent': 0, 'important': 1, 'normal': 2 };
            const prioriteA = prioriteOrder[a.dataset.priorite] || 2;
            const prioriteB = prioriteOrder[b.dataset.priorite] || 2;
            return prioriteA - prioriteB;
        }
        return 0;
    });
    
    grid.innerHTML = '';
    cards.forEach(card => grid.appendChild(card));
}

// Formater monnaie
function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}

// Exposer les fonctions globalement
window.filterVilles = filterVilles;
window.sortVilles = sortVilles;
window.formatMoney = formatMoney;

