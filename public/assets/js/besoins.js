// ===================================
// BNGRC - Besoins Management Script
// ===================================

// Article catalog with prices
const articleCatalog = {
    nature: [
        { nom: 'Riz', unite: 'kg', prixUnitaire: 5000 },
        { nom: 'Huile', unite: 'L', prixUnitaire: 12000 },
        { nom: 'Eau potable', unite: 'L', prixUnitaire: 2000 },
        { nom: 'Sucre', unite: 'kg', prixUnitaire: 4500 },
        { nom: 'Sel', unite: 'kg', prixUnitaire: 1500 },
        { nom: 'Haricots', unite: 'kg', prixUnitaire: 6000 },
        { nom: 'Farine', unite: 'kg', prixUnitaire: 4000 }
    ],
    materiel: [
        { nom: 'Tôles', unite: 'unités', prixUnitaire: 25000 },
        { nom: 'Clous', unite: 'kg', prixUnitaire: 15000 },
        { nom: 'Bâches', unite: 'm²', prixUnitaire: 8000 },
        { nom: 'Planches', unite: 'unités', prixUnitaire: 12000 },
        { nom: 'Ciment', unite: 'sacs', prixUnitaire: 35000 },
        { nom: 'Sable', unite: 'm³', prixUnitaire: 45000 }
    ],
    argent: [
        { nom: 'Don financier', unite: '', prixUnitaire: 1 }
    ]
};

// Storage for besoins
let besoinsData = JSON.parse(localStorage.getItem('besoins')) || [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initForm();
    loadBesoins();
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

// Initialize form
function initForm() {
    const form = document.getElementById('besoinForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

// Set current date
function setCurrentDate() {
    const today = new Date().toISOString().split('T')[0];
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (!input.value) {
            input.value = today;
        }
    });
}

// Update article options based on category
function updateArticleOptions() {
    const categorie = document.getElementById('categorie').value;
    const articleSelect = document.getElementById('article');
    const prixUnitaireInput = document.getElementById('prixUnitaire');
    const quantiteInput = document.getElementById('quantite');
    
    // Reset fields
    articleSelect.innerHTML = '<option value="">Sélectionner un article</option>';
    prixUnitaireInput.value = '';
    quantiteInput.value = '';
    document.getElementById('montantTotal').value = '';
    
    if (categorie && articleCatalog[categorie]) {
        articleCatalog[categorie].forEach(article => {
            const option = document.createElement('option');
            option.value = article.nom;
            option.textContent = `${article.nom} ${article.unite ? '(' + article.unite + ')' : ''}`;
            option.dataset.unite = article.unite;
            option.dataset.prix = article.prixUnitaire;
            articleSelect.appendChild(option);
        });
    }
}

// Update prix unitaire when article is selected
function updatePrixUnitaire() {
    const articleSelect = document.getElementById('article');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const prixUnitaireInput = document.getElementById('prixUnitaire');
    
    if (selectedOption.dataset.prix) {
        prixUnitaireInput.value = selectedOption.dataset.prix;
        calculateTotal();
    }
}

// Calculate total amount
function calculateTotal() {
    const quantite = parseFloat(document.getElementById('quantite').value) || 0;
    const prixUnitaire = parseFloat(document.getElementById('prixUnitaire').value) || 0;
    const montantTotal = quantite * prixUnitaire;
    
    document.getElementById('montantTotal').value = formatMoney(montantTotal) + ' Ar';
}

// Handle form submission
function handleFormSubmit(e) {
    e.preventDefault();
    
    const ville = document.getElementById('ville').value;
    const categorie = document.getElementById('categorie').value;
    const article = document.getElementById('article').value;
    const quantite = parseFloat(document.getElementById('quantite').value);
    const prixUnitaire = parseFloat(document.getElementById('prixUnitaire').value);
    const priorite = document.querySelector('input[name="priorite"]:checked').value;
    const notes = document.getElementById('notes').value;
    
    // Get unite from selected option
    const articleSelect = document.getElementById('article');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const unite = selectedOption.dataset.unite || '';
    
    const besoin = {
        id: Date.now(),
        date: new Date().toLocaleDateString('fr-FR'),
        ville,
        categorie,
        article,
        quantite,
        unite,
        prixUnitaire,
        montantTotal: quantite * prixUnitaire,
        priorite,
        notes
    };
    
    besoinsData.push(besoin);
    saveBesoins();
    loadBesoins();
    resetForm();
    
    showNotification('Besoin enregistré avec succès!', 'success');
}

// Save to localStorage
function saveBesoins() {
    localStorage.setItem('besoins', JSON.stringify(besoinsData));
}

// Load and display besoins
function loadBesoins() {
    const tbody = document.getElementById('besoinsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (besoinsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    Aucun besoin enregistré pour le moment
                </td>
            </tr>
        `;
        return;
    }
    
    besoinsData.forEach(besoin => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${besoin.date}</td>
            <td><span class="city-tag">${besoin.ville}</span></td>
            <td><span class="cat-badge ${besoin.categorie}">${getCategorieLabel(besoin.categorie)}</span></td>
            <td>${besoin.article}</td>
            <td>${besoin.quantite} ${besoin.unite}</td>
            <td>${formatMoney(besoin.prixUnitaire)} Ar</td>
            <td><strong>${formatMoney(besoin.montantTotal)} Ar</strong></td>
            <td><span class="priority-badge ${besoin.priorite}">${getPrioriteLabel(besoin.priorite)}</span></td>
            <td>
                <div class="action-buttons-small">
                    <button class="btn-icon-small" title="Modifier" onclick="editBesoin(${besoin.id})">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="btn-icon-small delete" title="Supprimer" onclick="deleteBesoin(${besoin.id})">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Edit besoin
function editBesoin(id) {
    const besoin = besoinsData.find(b => b.id === id);
    if (!besoin) return;
    
    document.getElementById('ville').value = besoin.ville;
    document.getElementById('categorie').value = besoin.categorie;
    updateArticleOptions();
    
    setTimeout(() => {
        document.getElementById('article').value = besoin.article;
        updatePrixUnitaire();
        document.getElementById('quantite').value = besoin.quantite;
        document.querySelector(`input[name="priorite"][value="${besoin.priorite}"]`).checked = true;
        document.getElementById('notes').value = besoin.notes || '';
        
        // Delete old entry
        deleteBesoin(id, true);
    }, 100);
    
    // Scroll to form
    document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
}

// Delete besoin
function deleteBesoin(id, silent = false) {
    if (!silent && !confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?')) {
        return;
    }
    
    besoinsData = besoinsData.filter(b => b.id !== id);
    saveBesoins();
    loadBesoins();
    
    if (!silent) {
        showNotification('Besoin supprimé', 'info');
    }
}

// Reset form
function resetForm() {
    document.getElementById('besoinForm').reset();
    document.getElementById('article').innerHTML = '<option value="">Sélectionner un article</option>';
    document.getElementById('prixUnitaire').value = '';
    document.getElementById('montantTotal').value = '';
}

// Filter besoins
function filterBesoins(searchTerm) {
    const rows = document.querySelectorAll('#besoinsTableBody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

// Filter by ville
function filterByVille(ville) {
    const rows = document.querySelectorAll('#besoinsTableBody tr');
    
    rows.forEach(row => {
        if (!ville) {
            row.style.display = '';
        } else {
            const villeCell = row.querySelector('.city-tag');
            row.style.display = villeCell && villeCell.textContent === ville ? '' : 'none';
        }
    });
}

// Helper functions
function getCategorieLabel(categorie) {
    const labels = {
        'nature': 'En nature',
        'materiel': 'Matériaux',
        'argent': 'Argent'
    };
    return labels[categorie] || categorie;
}

function getPrioriteLabel(priorite) {
    const labels = {
        'urgent': 'Urgent',
        'important': 'Important',
        'normal': 'Normal'
    };
    return labels[priorite] || priorite;
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? 'var(--success)' : 'var(--info)'};
        color: white;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
