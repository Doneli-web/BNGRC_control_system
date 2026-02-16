// ===================================
// BNGRC - Récapitulatif Script
// ===================================

const API_BASE_URL = 'http://localhost:8000/api';

let autoRefreshEnabled = false;
let autoRefreshInterval = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    refreshData();
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

// ==========================================
// REFRESH DATA (Ajax)
// ==========================================

async function refreshData() {
    const btnRefresh = document.getElementById('btnRefresh');
    btnRefresh.classList.add('loading');
    btnRefresh.disabled = true;
    
    try {
        // Charger les données depuis l'API ou localStorage
        const besoins = await loadBesoins();
        const dispatches = await loadDispatches();
        const achats = await loadAchats();
        
        // Calculer les montants
        const calculations = calculateAmounts(besoins, dispatches, achats);
        
        // Afficher les résultats
        displaySummary(calculations);
        displayCategoryDetails(calculations);
        displayCityDetails(calculations);
        
        // Mettre à jour le timestamp
        updateLastUpdate();
        
    } catch (error) {
        console.error('Erreur refresh:', error);
        showError('Erreur lors du chargement des données');
    } finally {
        btnRefresh.classList.remove('loading');
        btnRefresh.disabled = false;
    }
}

// ==========================================
// LOAD DATA
// ==========================================

async function loadBesoins() {
    try {
        const response = await fetch(`${API_BASE_URL}/besoins`);
        if (response.ok) {
            const result = await response.json();
            return result.data || [];
        }
    } catch (error) {
        console.warn('API non disponible, utilisation localStorage');
    }
    
    // Fallback localStorage
    return JSON.parse(localStorage.getItem('besoins')) || [];
}

async function loadDispatches() {
    try {
        const response = await fetch(`${API_BASE_URL}/dispatch`);
        if (response.ok) {
            const result = await response.json();
            return result.data || [];
        }
    } catch (error) {
        console.warn('API non disponible, utilisation localStorage');
    }
    
    // Fallback localStorage
    return JSON.parse(localStorage.getItem('dispatchResults'))?.data || {};
}

async function loadAchats() {
    try {
        const response = await fetch(`${API_BASE_URL}/achats`);
        if (response.ok) {
            const result = await response.json();
            return result.data || [];
        }
    } catch (error) {
        console.warn('API non disponible, utilisation localStorage');
    }
    
    // Fallback localStorage
    return JSON.parse(localStorage.getItem('achats')) || [];
}

// ==========================================
// CALCULATE AMOUNTS
// ==========================================

function calculateAmounts(besoins, dispatches, achats) {
    const calculations = {
        total: 0,
        satisfait: 0,
        restant: 0,
        parCategorie: {},
        parVille: {}
    };
    
    // 1. Calculer les besoins totaux
    besoins.forEach(besoin => {
        const montant = besoin.quantite * besoin.prixUnitaire;
        calculations.total += montant;
        
        // Par catégorie
        if (!calculations.parCategorie[besoin.categorie]) {
            calculations.parCategorie[besoin.categorie] = {
                total: 0,
                satisfait: 0,
                restant: 0
            };
        }
        calculations.parCategorie[besoin.categorie].total += montant;
        
        // Par ville
        if (!calculations.parVille[besoin.ville]) {
            calculations.parVille[besoin.ville] = {
                total: 0,
                satisfait: 0,
                restant: 0
            };
        }
        calculations.parVille[besoin.ville].total += montant;
    });
    
    // 2. Calculer les montants satisfaits par les dispatches
    if (Array.isArray(dispatches)) {
        dispatches.forEach(dispatch => {
            const montant = dispatch.quantite_attribuee * dispatch.prix_unitaire;
            calculations.satisfait += montant;
            
            // Par catégorie
            const categorie = getCategorieFromArticle(dispatch.article);
            if (calculations.parCategorie[categorie]) {
                calculations.parCategorie[categorie].satisfait += montant;
            }
            
            // Par ville
            if (calculations.parVille[dispatch.ville]) {
                calculations.parVille[dispatch.ville].satisfait += montant;
            }
        });
    } else {
        // Format object from simulation
        Object.values(dispatches).forEach(villeData => {
            calculations.satisfait += villeData.totalAttribue || 0;
        });
    }
    
    // 3. Ajouter les achats aux montants satisfaits
    achats.forEach(achat => {
        const montant = achat.montantBase; // Sans les frais
        calculations.satisfait += montant;
        
        // Par catégorie
        if (calculations.parCategorie[achat.categorie]) {
            calculations.parCategorie[achat.categorie].satisfait += montant;
        }
        
        // Par ville
        if (calculations.parVille[achat.ville]) {
            calculations.parVille[achat.ville].satisfait += montant;
        }
    });
    
    // 4. Calculer les restants
    calculations.restant = calculations.total - calculations.satisfait;
    
    Object.keys(calculations.parCategorie).forEach(cat => {
        const data = calculations.parCategorie[cat];
        data.restant = data.total - data.satisfait;
    });
    
    Object.keys(calculations.parVille).forEach(ville => {
        const data = calculations.parVille[ville];
        data.restant = data.total - data.satisfait;
    });
    
    return calculations;
}

// ==========================================
// DISPLAY FUNCTIONS
// ==========================================

function displaySummary(calc) {
    // Montants
    document.getElementById('montantTotal').textContent = formatMoney(calc.total) + ' Ar';
    document.getElementById('montantSatisfait').textContent = formatMoney(calc.satisfait) + ' Ar';
    document.getElementById('montantRestant').textContent = formatMoney(calc.restant) + ' Ar';
    
    // Détails
    const nbBesoins = Object.values(calc.parVille).length;
    document.getElementById('detailTotal').textContent = `${nbBesoins} ville(s) concernée(s)`;
    
    const tauxSatisfaction = calc.total > 0 ? Math.round((calc.satisfait / calc.total) * 100) : 0;
    document.getElementById('detailSatisfait').textContent = `${tauxSatisfaction}% du total`;
    document.getElementById('detailRestant').textContent = 
        calc.restant > 0 ? 'À compléter par dons ou achats' : 'Tous les besoins sont couverts !';
    
    // Progress bar
    document.getElementById('tauxSatisfaction').textContent = tauxSatisfaction + '%';
    const progressBar = document.getElementById('progressBar');
    progressBar.style.width = tauxSatisfaction + '%';
    if (tauxSatisfaction === 100) {
        progressBar.classList.add('complete');
    } else {
        progressBar.classList.remove('complete');
    }
    
    // Animation
    animateValue(document.getElementById('montantTotal'), 0, calc.total);
    animateValue(document.getElementById('montantSatisfait'), 0, calc.satisfait);
    animateValue(document.getElementById('montantRestant'), 0, calc.restant);
}

function displayCategoryDetails(calc) {
    const container = document.getElementById('categoryDetails');
    container.innerHTML = '';
    
    const categories = {
        'nature': { label: 'En nature', icon: '🌾' },
        'materiel': { label: 'Matériaux', icon: '🔨' },
        'argent': { label: 'Argent', icon: '💰' }
    };
    
    Object.keys(calc.parCategorie).forEach(catKey => {
        const data = calc.parCategorie[catKey];
        const cat = categories[catKey] || { label: catKey, icon: '📦' };
        const taux = data.total > 0 ? Math.round((data.satisfait / data.total) * 100) : 0;
        
        const card = document.createElement('div');
        card.className = 'category-card';
        card.innerHTML = `
            <div class="category-header">
                <h3>${cat.icon} ${cat.label}</h3>
                <span class="category-badge ${taux >= 80 ? 'success' : taux >= 50 ? 'warning' : 'urgent'}">
                    ${taux}%
                </span>
            </div>
            <div class="category-stats">
                <div class="progress-group">
                    <div class="progress-label">
                        <span>Total</span>
                        <span>${formatMoney(data.total)} Ar</span>
                    </div>
                </div>
                <div class="progress-group">
                    <div class="progress-label">
                        <span>Satisfait</span>
                        <span>${formatMoney(data.satisfait)} Ar</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${taux === 100 ? 'complete' : ''}" style="width: ${taux}%"></div>
                    </div>
                </div>
                <div class="progress-group">
                    <div class="progress-label">
                        <span>Restant</span>
                        <span style="color: var(--danger)">${formatMoney(data.restant)} Ar</span>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

function displayCityDetails(calc) {
    const tbody = document.getElementById('cityDetailsTable');
    tbody.innerHTML = '';
    
    if (Object.keys(calc.parVille).length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    Aucune donnée disponible
                </td>
            </tr>
        `;
        return;
    }
    
    Object.keys(calc.parVille).forEach(ville => {
        const data = calc.parVille[ville];
        const taux = data.total > 0 ? Math.round((data.satisfait / data.total) * 100) : 0;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><span class="city-tag">${ville}</span></td>
            <td><strong>${formatMoney(data.total)} Ar</strong></td>
            <td style="color: var(--success)"><strong>${formatMoney(data.satisfait)} Ar</strong></td>
            <td style="color: ${data.restant > 0 ? 'var(--danger)' : 'var(--success)'}">
                <strong>${formatMoney(data.restant)} Ar</strong>
            </td>
            <td>
                <div class="progress-bar" style="width: 100px;">
                    <div class="progress-fill ${taux === 100 ? 'complete' : ''}" 
                         style="width: ${taux}%"></div>
                </div>
                <small style="margin-left: 0.5rem;">${taux}%</small>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// ==========================================
// AUTO REFRESH
// ==========================================

function toggleAutoRefresh() {
    const toggle = document.getElementById('autoRefreshToggle');
    autoRefreshEnabled = !autoRefreshEnabled;
    
    if (autoRefreshEnabled) {
        toggle.classList.add('active');
        autoRefreshInterval = setInterval(refreshData, 30000); // 30 secondes
    } else {
        toggle.classList.remove('active');
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }
}

// ==========================================
// UTILITIES
// ==========================================

function updateLastUpdate() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('fr-FR');
    document.getElementById('lastUpdate').textContent = `Dernière mise à jour: ${timeStr}`;
}

function animateValue(element, start, end, duration = 1000) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = formatMoney(Math.round(current)) + ' Ar';
    }, 16);
}

function getCategorieFromArticle(article) {
    const nature = ['Riz', 'Huile', 'Eau potable', 'Sucre', 'Haricots', 'Farine'];
    const materiel = ['Tôles', 'Clous', 'Bâches', 'Ciment', 'Planches'];
    
    if (nature.includes(article)) return 'nature';
    if (materiel.includes(article)) return 'materiel';
    return 'argent';
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}

function showError(message) {
    alert(message);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
