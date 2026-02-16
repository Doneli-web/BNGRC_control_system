// ===================================
// BNGRC - Dons Management Script
// ===================================

// Article catalog (same as besoins)
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

// Storage for dons
let donsData = JSON.parse(localStorage.getItem('dons')) || [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initForm();
    loadDons();
    updateStatistics();
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
    const form = document.getElementById('donForm');
    if (form) {
        form.addEventListener('submit', handleDonFormSubmit);
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

// Update article options based on category
function updateDonArticleOptions() {
    const categorie = document.getElementById('categorieDon').value;
    const articleSelect = document.getElementById('articleDon');
    const prixUnitaireInput = document.getElementById('prixUnitaireDon');
    const quantiteInput = document.getElementById('quantiteDon');
    
    // Reset fields
    articleSelect.innerHTML = '<option value="">Sélectionner un article</option>';
    prixUnitaireInput.value = '';
    quantiteInput.value = '';
    document.getElementById('montantTotalDon').value = '';
    
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
function updateDonPrixUnitaire() {
    const articleSelect = document.getElementById('articleDon');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const prixUnitaireInput = document.getElementById('prixUnitaireDon');
    
    if (selectedOption.dataset.prix) {
        prixUnitaireInput.value = selectedOption.dataset.prix;
        calculateDonTotal();
    }
}

// Calculate total amount
function calculateDonTotal() {
    const quantite = parseFloat(document.getElementById('quantiteDon').value) || 0;
    const prixUnitaire = parseFloat(document.getElementById('prixUnitaireDon').value) || 0;
    const montantTotal = quantite * prixUnitaire;
    
    document.getElementById('montantTotalDon').value = formatMoney(montantTotal) + ' Ar';
}

// Handle form submission
function handleDonFormSubmit(e) {
    e.preventDefault();
    
    const donateur = document.getElementById('donateur').value;
    const typeDonateur = document.getElementById('typeDonateur').value;
    const categorie = document.getElementById('categorieDon').value;
    const article = document.getElementById('articleDon').value;
    const quantite = parseFloat(document.getElementById('quantiteDon').value);
    const prixUnitaire = parseFloat(document.getElementById('prixUnitaireDon').value);
    const dateReception = document.getElementById('dateReception').value;
    const contact = document.getElementById('contact').value;
    const notes = document.getElementById('notesDon').value;
    
    // Get unite from selected option
    const articleSelect = document.getElementById('articleDon');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const unite = selectedOption.dataset.unite || '';
    
    const don = {
        id: Date.now(),
        date: new Date(dateReception).toLocaleDateString('fr-FR'),
        donateur,
        typeDonateur,
        categorie,
        article,
        quantite,
        unite,
        prixUnitaire,
        valeurTotale: quantite * prixUnitaire,
        dateReception,
        contact,
        notes,
        statut: 'en_attente',
        quantiteDistribuee: 0
    };
    
    donsData.push(don);
    saveDons();
    loadDons();
    updateStatistics();
    resetDonForm();
    
    showNotification('Don enregistré avec succès!', 'success');
}

// Save to localStorage
function saveDons() {
    localStorage.setItem('dons', JSON.stringify(donsData));
}

// Load and display dons
function loadDons() {
    const tbody = document.getElementById('donsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (donsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    Aucun don enregistré pour le moment
                </td>
            </tr>
        `;
        return;
    }
    
    // Sort by date (most recent first)
    const sortedDons = [...donsData].sort((a, b) => 
        new Date(b.dateReception) - new Date(a.dateReception)
    );
    
    sortedDons.forEach(don => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${don.date}</td>
            <td>
                <div class="donor-info">
                    <strong>${don.donateur}</strong>
                    <small>${getTypeDonateur(don.typeDonateur)}</small>
                </div>
            </td>
            <td><span class="type-badge ${don.typeDonateur}">${getTypeDonateur(don.typeDonateur)}</span></td>
            <td><span class="cat-badge ${don.categorie}">${getCategorieLabel(don.categorie)}</span></td>
            <td>${don.article}</td>
            <td>${don.quantite} ${don.unite}</td>
            <td><strong>${formatMoney(don.valeurTotale)} Ar</strong></td>
            <td><span class="status-badge ${don.statut}">${getStatutLabel(don.statut)}</span></td>
            <td>
                <div class="action-buttons-small">
                    <button class="btn-icon-small" title="Voir détails" onclick="viewDonDetails(${don.id})">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button class="btn-icon-small" title="Modifier" onclick="editDon(${don.id})">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="btn-icon-small delete" title="Supprimer" onclick="deleteDon(${don.id})">
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

// Update statistics
function updateStatistics() {
    const totalDons = donsData.length;
    const valeurTotale = donsData.reduce((sum, don) => sum + don.valeurTotale, 0);
    const donateursUniques = new Set(donsData.map(d => d.donateur)).size;
    const donsDistribues = donsData.filter(d => d.statut === 'distribue').length;
    
    // Update stat boxes if they exist
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length >= 4) {
        animateValue(statNumbers[0], 0, totalDons, 1000);
        animateValue(statNumbers[1], 0, valeurTotale / 1000000, 1000, 'M Ar', 1);
        animateValue(statNumbers[2], 0, donateursUniques, 1000);
        animateValue(statNumbers[3], 0, donsDistribues, 1000);
    }
}

// Animate value
function animateValue(element, start, end, duration, suffix = '', decimals = 0) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = current.toFixed(decimals) + (suffix || '');
    }, 16);
}

// View don details
function viewDonDetails(id) {
    const don = donsData.find(d => d.id === id);
    if (!don) return;
    
    const modal = `
        <div class="modal-overlay" onclick="closeModal()">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2>Détails du don</h2>
                    <button onclick="closeModal()" class="btn-icon">✕</button>
                </div>
                <div class="modal-body">
                    <div class="detail-grid">
                        <div><strong>Donateur:</strong> ${don.donateur}</div>
                        <div><strong>Type:</strong> ${getTypeDonateur(don.typeDonateur)}</div>
                        <div><strong>Date:</strong> ${don.date}</div>
                        <div><strong>Catégorie:</strong> ${getCategorieLabel(don.categorie)}</div>
                        <div><strong>Article:</strong> ${don.article}</div>
                        <div><strong>Quantité:</strong> ${don.quantite} ${don.unite}</div>
                        <div><strong>Valeur unitaire:</strong> ${formatMoney(don.prixUnitaire)} Ar</div>
                        <div><strong>Valeur totale:</strong> ${formatMoney(don.valeurTotale)} Ar</div>
                        <div><strong>Statut:</strong> ${getStatutLabel(don.statut)}</div>
                        ${don.contact ? `<div><strong>Contact:</strong> ${don.contact}</div>` : ''}
                        ${don.notes ? `<div style="grid-column: 1 / -1;"><strong>Notes:</strong><br>${don.notes}</div>` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    addModalStyles();
}

// Close modal
function closeModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) modal.remove();
}

// Add modal styles
function addModalStyles() {
    if (document.getElementById('modal-styles')) return;
    
    const style = document.createElement('style');
    style.id = 'modal-styles';
    style.textContent = `
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }
        .modal-content {
            background: var(--bg-secondary);
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.3s ease-out;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .modal-body {
            padding: 1.5rem;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

// Edit don
function editDon(id) {
    const don = donsData.find(d => d.id === id);
    if (!don) return;
    
    document.getElementById('donateur').value = don.donateur;
    document.getElementById('typeDonateur').value = don.typeDonateur;
    document.getElementById('categorieDon').value = don.categorie;
    updateDonArticleOptions();
    
    setTimeout(() => {
        document.getElementById('articleDon').value = don.article;
        updateDonPrixUnitaire();
        document.getElementById('quantiteDon').value = don.quantite;
        document.getElementById('dateReception').value = don.dateReception;
        document.getElementById('contact').value = don.contact || '';
        document.getElementById('notesDon').value = don.notes || '';
        
        // Delete old entry
        deleteDon(id, true);
    }, 100);
    
    // Scroll to form
    document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
}

// Delete don
function deleteDon(id, silent = false) {
    if (!silent && !confirm('Êtes-vous sûr de vouloir supprimer ce don ?')) {
        return;
    }
    
    donsData = donsData.filter(d => d.id !== id);
    saveDons();
    loadDons();
    updateStatistics();
    
    if (!silent) {
        showNotification('Don supprimé', 'info');
    }
}

// Reset form
function resetDonForm() {
    document.getElementById('donForm').reset();
    document.getElementById('articleDon').innerHTML = '<option value="">Sélectionner un article</option>';
    document.getElementById('prixUnitaireDon').value = '';
    document.getElementById('montantTotalDon').value = '';
    setCurrentDate();
}

// Filter dons
function filterDons(searchTerm) {
    const rows = document.querySelectorAll('#donsTableBody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

// Filter by categorie
function filterDonsByCategorie(categorie) {
    const rows = document.querySelectorAll('#donsTableBody tr');
    
    rows.forEach(row => {
        if (!categorie) {
            row.style.display = '';
        } else {
            const catBadge = row.querySelector(`.cat-badge.${categorie}`);
            row.style.display = catBadge ? '' : 'none';
        }
    });
}

// Filter by statut
function filterDonsByStatut(statut) {
    const rows = document.querySelectorAll('#donsTableBody tr');
    
    rows.forEach(row => {
        if (!statut) {
            row.style.display = '';
        } else {
            const statusBadge = row.querySelector(`.status-badge.${statut}`);
            row.style.display = statusBadge ? '' : 'none';
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

function getTypeDonateur(type) {
    const labels = {
        'particulier': 'Particulier',
        'entreprise': 'Entreprise',
        'ong': 'ONG',
        'gouvernement': 'Gouvernement',
        'international': 'Organisation internationale'
    };
    return labels[type] || type;
}

function getStatutLabel(statut) {
    const labels = {
        'en_attente': 'En attente',
        'distribue': 'Distribué',
        'partiel': 'Partiellement distribué'
    };
    return labels[statut] || statut;
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
