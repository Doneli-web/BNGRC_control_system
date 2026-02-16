// ===================================
// BNGRC - Achats Script
// ===================================

const API_BASE_URL = 'http://localhost:8000/api';

// Catalogue des articles avec prix
const articleCatalog = {
    nature: [
        { nom: 'Riz', unite: 'kg', prixUnitaire: 5000 },
        { nom: 'Huile', unite: 'L', prixUnitaire: 12000 },
        { nom: 'Eau potable', unite: 'L', prixUnitaire: 2000 },
        { nom: 'Sucre', unite: 'kg', prixUnitaire: 4500 },
        { nom: 'Haricots', unite: 'kg', prixUnitaire: 6000 }
    ],
    materiel: [
        { nom: 'Tôles', unite: 'unités', prixUnitaire: 25000 },
        { nom: 'Clous', unite: 'kg', prixUnitaire: 15000 },
        { nom: 'Bâches', unite: 'm²', prixUnitaire: 8000 },
        { nom: 'Ciment', unite: 'sacs', prixUnitaire: 35000 }
    ]
};

// Storage
let achatsData = JSON.parse(localStorage.getItem('achats')) || [];
let fraisAchat = parseFloat(localStorage.getItem('fraisAchat')) || 10;
let besoinsRestants = {};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    loadFraisAchat();
    loadArgentDisponible();
    loadVilles();
    loadAchats();
    initForm();
    updateExempleFrais();
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
    const form = document.getElementById('achatForm');
    if (form) {
        form.addEventListener('submit', handleAchatSubmit);
    }
    
    // Update example on frais change
    document.getElementById('fraisAchat').addEventListener('input', updateExempleFrais);
}

// Load et save frais d'achat
function loadFraisAchat() {
    document.getElementById('fraisAchat').value = fraisAchat;
}

function saveFraisAchat() {
    fraisAchat = parseFloat(document.getElementById('fraisAchat').value) || 10;
    localStorage.setItem('fraisAchat', fraisAchat);
    updateExempleFrais();
    calculateAchat(); // Recalculer si un achat est en cours
}

function updateExempleFrais() {
    const base = 100000;
    const frais = parseFloat(document.getElementById('fraisAchat').value) || 10;
    const total = base * (1 + frais / 100);
    document.getElementById('exempleTotal').textContent = formatMoney(total) + ' Ar';
}

// Load argent disponible
async function loadArgentDisponible() {
    try {
        // Récupérer les dons en argent depuis l'API
        const response = await fetch(`${API_BASE_URL}/dons?categorie=argent`);
        if (response.ok) {
            const result = await response.json();
            const donsArgent = result.data || [];
            
            // Calculer le total des dons en argent
            let totalDons = donsArgent.reduce((sum, don) => sum + parseFloat(don.quantite || 0), 0);
            
            // Soustraire les achats déjà effectués
            const totalAchats = achatsData.reduce((sum, achat) => sum + achat.montantTotal, 0);
            
            const disponible = totalDons - totalAchats;
            
            document.getElementById('argentDisponible').textContent = formatMoney(disponible) + ' Ar';
            
            return disponible;
        }
    } catch (error) {
        console.error('Erreur chargement argent:', error);
        // Fallback: calculer depuis localStorage
        const totalAchats = achatsData.reduce((sum, achat) => sum + achat.montantTotal, 0);
        document.getElementById('argentDisponible').textContent = formatMoney(10000000 - totalAchats) + ' Ar';
    }
}

// Load villes
async function loadVilles() {
    try {
        const response = await fetch(`${API_BASE_URL}/villes`);
        if (response.ok) {
            const result = await response.json();
            const villes = result.data || [];
            
            const selectVille = document.getElementById('villeAchat');
            const filterVille = document.querySelector('.list-actions select');
            
            villes.forEach(ville => {
                const option = document.createElement('option');
                option.value = ville.name;
                option.textContent = ville.name;
                selectVille.appendChild(option);
                
                if (filterVille) {
                    const filterOption = option.cloneNode(true);
                    filterVille.appendChild(filterOption);
                }
            });
        }
    } catch (error) {
        console.error('Erreur chargement villes:', error);
        // Fallback
        const villes = ['Antananarivo', 'Toamasina', 'Antsirabe', 'Mahajanga'];
        const selectVille = document.getElementById('villeAchat');
        villes.forEach(ville => {
            const option = document.createElement('option');
            option.value = ville;
            option.textContent = ville;
            selectVille.appendChild(option);
        });
    }
}

// Load besoins restants pour une ville
async function loadBesoinsRestants() {
    const ville = document.getElementById('villeAchat').value;
    if (!ville) return;
    
    try {
        // Récupérer les besoins de cette ville depuis l'API
        const response = await fetch(`${API_BASE_URL}/besoins?ville=${ville}`);
        if (response.ok) {
            const result = await response.json();
            const besoins = result.data || [];
            
            // Calculer les besoins restants par article
            besoinsRestants = {};
            besoins.forEach(besoin => {
                if (!besoinsRestants[besoin.article]) {
                    besoinsRestants[besoin.article] = 0;
                }
                besoinsRestants[besoin.article] += besoin.quantite;
            });
            
            // Soustraire les dons déjà attribués
            const responseDispatch = await fetch(`${API_BASE_URL}/dispatch?ville=${ville}`);
            if (responseDispatch.ok) {
                const dispatchResult = await responseDispatch.json();
                const dispatches = dispatchResult.data || [];
                
                dispatches.forEach(dispatch => {
                    if (besoinsRestants[dispatch.article]) {
                        besoinsRestants[dispatch.article] -= dispatch.quantite_attribuee;
                    }
                });
            }
            
            // Soustraire les achats déjà effectués
            achatsData.forEach(achat => {
                if (achat.ville === ville && besoinsRestants[achat.article]) {
                    besoinsRestants[achat.article] -= achat.quantite;
                }
            });
        }
    } catch (error) {
        console.error('Erreur chargement besoins:', error);
    }
}

// Load articles selon catégorie
function loadArticlesAchat() {
    const categorie = document.getElementById('categorieAchat').value;
    const articleSelect = document.getElementById('articleAchat');
    
    articleSelect.innerHTML = '<option value="">Sélectionner un article</option>';
    document.getElementById('prixUnitaireAchat').value = '';
    document.getElementById('besoinRestant').value = '';
    document.getElementById('purchaseSummary').style.display = 'none';
    
    if (categorie && articleCatalog[categorie]) {
        articleCatalog[categorie].forEach(article => {
            const option = document.createElement('option');
            option.value = article.nom;
            option.textContent = `${article.nom} (${formatMoney(article.prixUnitaire)} Ar/${article.unite})`;
            option.dataset.prix = article.prixUnitaire;
            option.dataset.unite = article.unite;
            articleSelect.appendChild(option);
        });
    }
}

// Update prix unitaire
function updatePrixUnitaireAchat() {
    const articleSelect = document.getElementById('articleAchat');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const ville = document.getElementById('villeAchat').value;
    
    if (selectedOption.dataset.prix) {
        const prix = selectedOption.dataset.prix;
        const unite = selectedOption.dataset.unite;
        document.getElementById('prixUnitaireAchat').value = formatMoney(prix) + ' Ar/' + unite;
        
        // Afficher le besoin restant
        const article = selectedOption.value;
        const restant = besoinsRestants[article] || 0;
        document.getElementById('besoinRestant').value = restant + ' ' + unite + ' restant(s)';
        
        calculateAchat();
    }
}

// Calculate achat
function calculateAchat() {
    const quantite = parseFloat(document.getElementById('quantiteAchat').value) || 0;
    const articleSelect = document.getElementById('articleAchat');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    
    if (!selectedOption || !selectedOption.dataset.prix || quantite <= 0) {
        document.getElementById('purchaseSummary').style.display = 'none';
        return;
    }
    
    const prixUnitaire = parseFloat(selectedOption.dataset.prix);
    const montantBase = quantite * prixUnitaire;
    const frais = parseFloat(document.getElementById('fraisAchat').value) || 10;
    const montantFrais = montantBase * (frais / 100);
    const montantTotal = montantBase + montantFrais;
    
    // Afficher le résumé
    document.getElementById('montantBase').textContent = formatMoney(montantBase) + ' Ar';
    document.getElementById('fraisPercent').textContent = frais;
    document.getElementById('montantFrais').textContent = formatMoney(montantFrais) + ' Ar';
    document.getElementById('montantTotal').textContent = formatMoney(montantTotal) + ' Ar';
    document.getElementById('purchaseSummary').style.display = 'block';
}

// Handle form submit
async function handleAchatSubmit(e) {
    e.preventDefault();
    
    clearAlert();
    
    const ville = document.getElementById('villeAchat').value;
    const categorie = document.getElementById('categorieAchat').value;
    const article = document.getElementById('articleAchat').value;
    const quantite = parseFloat(document.getElementById('quantiteAchat').value);
    
    const articleSelect = document.getElementById('articleAchat');
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const prixUnitaire = parseFloat(selectedOption.dataset.prix);
    const unite = selectedOption.dataset.unite;
    
    // Vérification 1: L'article est-il déjà disponible dans les dons ?
    try {
        const response = await fetch(`${API_BASE_URL}/dons?article=${article}&ville=${ville}`);
        if (response.ok) {
            const result = await response.json();
            const donsDisponibles = result.data || [];
            
            if (donsDisponibles.length > 0) {
                showAlert('error', '❌ Cet article est déjà disponible dans les dons ! Impossible d\'acheter.');
                return;
            }
        }
    } catch (error) {
        console.warn('Vérification dons:', error);
    }
    
    // Vérification 2: Budget suffisant ?
    const montantBase = quantite * prixUnitaire;
    const frais = parseFloat(document.getElementById('fraisAchat').value) || 10;
    const montantTotal = montantBase * (1 + frais / 100);
    
    const argentDisponible = await loadArgentDisponible();
    if (montantTotal > argentDisponible) {
        showAlert('error', `❌ Budget insuffisant ! Disponible: ${formatMoney(argentDisponible)} Ar`);
        return;
    }
    
    // Vérification 3: Quantité ne dépasse pas le besoin ?
    const besoinRestant = besoinsRestants[article] || 0;
    if (quantite > besoinRestant) {
        showAlert('warning', `⚠️ Attention : Le besoin restant est de ${besoinRestant} ${unite}, vous achetez ${quantite} ${unite}`);
    }
    
    // Créer l'achat
    const achat = {
        id: Date.now(),
        date: new Date().toLocaleDateString('fr-FR'),
        ville,
        categorie,
        article,
        quantite,
        unite,
        prixUnitaire,
        montantBase,
        frais: frais,
        montantFrais: montantBase * (frais / 100),
        montantTotal
    };
    
    // Enregistrer dans l'API
    try {
        const response = await fetch(`${API_BASE_URL}/achats`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(achat)
        });
        
        if (response.ok) {
            achatsData.push(achat);
            saveAchats();
            loadAchats();
            loadArgentDisponible();
            resetAchatForm();
            showAlert('success', '✅ Achat enregistré avec succès !');
        }
    } catch (error) {
        // Fallback localStorage
        achatsData.push(achat);
        saveAchats();
        loadAchats();
        loadArgentDisponible();
        resetAchatForm();
        showAlert('success', '✅ Achat enregistré avec succès !');
    }
}

// Save to localStorage
function saveAchats() {
    localStorage.setItem('achats', JSON.stringify(achatsData));
}

// Load achats
function loadAchats() {
    const tbody = document.getElementById('achatsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (achatsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    Aucun achat enregistré pour le moment
                </td>
            </tr>
        `;
        return;
    }
    
    achatsData.forEach(achat => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${achat.date}</td>
            <td><span class="city-tag">${achat.ville}</span></td>
            <td><span class="cat-badge ${achat.categorie}">${getCategorieLabel(achat.categorie)}</span></td>
            <td>${achat.article}</td>
            <td>${achat.quantite} ${achat.unite}</td>
            <td>${formatMoney(achat.prixUnitaire)} Ar</td>
            <td>${formatMoney(achat.montantBase)} Ar</td>
            <td>${achat.frais}% (${formatMoney(achat.montantFrais)} Ar)</td>
            <td><strong style="color: var(--primary)">${formatMoney(achat.montantTotal)} Ar</strong></td>
            <td>
                <div class="action-buttons-small">
                    <button class="btn-icon-small delete" title="Supprimer" onclick="deleteAchat(${achat.id})">
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

// Delete achat
function deleteAchat(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet achat ?')) return;
    
    achatsData = achatsData.filter(a => a.id !== id);
    saveAchats();
    loadAchats();
    loadArgentDisponible();
    showAlert('info', 'Achat supprimé');
}

// Reset form
function resetAchatForm() {
    document.getElementById('achatForm').reset();
    document.getElementById('articleAchat').innerHTML = '<option value="">Sélectionner un article</option>';
    document.getElementById('prixUnitaireAchat').value = '';
    document.getElementById('besoinRestant').value = '';
    document.getElementById('purchaseSummary').style.display = 'none';
    clearAlert();
}

// Filters
function filterAchats(searchTerm) {
    const rows = document.querySelectorAll('#achatsTableBody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

function filterAchatsByVille(ville) {
    const rows = document.querySelectorAll('#achatsTableBody tr');
    
    rows.forEach(row => {
        if (!ville) {
            row.style.display = '';
        } else {
            const villeCell = row.querySelector('.city-tag');
            row.style.display = villeCell && villeCell.textContent === ville ? '' : 'none';
        }
    });
}

function filterAchatsByCategorie(categorie) {
    const rows = document.querySelectorAll('#achatsTableBody tr');
    
    rows.forEach(row => {
        if (!categorie) {
            row.style.display = '';
        } else {
            const catBadge = row.querySelector(`.cat-badge.${categorie}`);
            row.style.display = catBadge ? '' : 'none';
        }
    });
}

// Alert system
function showAlert(type, message) {
    const container = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert-box ${type}`;
    alert.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; font-size: 1.2rem;">×</button>
    `;
    container.appendChild(alert);
    
    setTimeout(() => alert.remove(), 5000);
}

function clearAlert() {
    document.getElementById('alertContainer').innerHTML = '';
}

// Helper functions
function getCategorieLabel(categorie) {
    const labels = { 'nature': 'En nature', 'materiel': 'Matériaux' };
    return labels[categorie] || categorie;
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}
