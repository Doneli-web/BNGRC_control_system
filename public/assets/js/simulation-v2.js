// ===================================
// BNGRC - Simulation V2 Script
// ===================================

const API_BASE_URL = '';

let simulationResults = null;
let simulationMode = null; // 'preview' ou 'validate'
let simulationRunning = false;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    checkDataAvailability();
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

// Check data availability
async function checkDataAvailability() {
    // Vérifie s'il y a des besoins et dons en base
    try {
        const res = await fetch(`/api/dispatch/simulatePreview`, { method: 'POST' });
        if (res.ok) {
            const result = await res.json();
            if ((result.statistics && result.statistics.attributions_creees > 0) || (result.data && result.data.length > 0)) {
                addLog(`[INFO] Données chargées depuis la base`, 'info');
                document.getElementById('btnPreview').disabled = false;
            } else {
                updateStatus('⚠️ Attention', 'Aucun besoin ou don disponible en base', 'warning');
                document.getElementById('btnPreview').disabled = true;
            }
        } else {
            updateStatus('⚠️ Erreur serveur', 'Impossible de charger les données', 'warning');
            document.getElementById('btnPreview').disabled = true;
        }
    } catch (e) {
        updateStatus('⚠️ Erreur', 'Impossible de charger les données', 'warning');
        document.getElementById('btnPreview').disabled = true;
    }
}

// ==========================================
// PREVIEW SIMULATION
// ==========================================

async function previewSimulation() {
    if (simulationRunning) return;
    
    simulationMode = 'preview';
    simulationRunning = true;
    
    // Disable buttons
    document.getElementById('btnPreview').disabled = true;
    document.getElementById('btnValidate').disabled = true;
    document.getElementById('btnReset').disabled = true;
    
    updateStatus('🔍 Prévisualisation en cours...', 'Calcul des attributions sans modification des données', 'info');
    clearLog();
    addLog('[PREVIEW] Mode prévisualisation activé', 'info');
    
    try {
        // Appel API en mode preview
        const response = await fetch(`/api/dispatch/simulatePreview`, { method: 'POST' });
        
        if (response.ok) {
            const result = await response.json();
            simulationResults = result;
            
            addLog('[SUCCESS] Simulation calculée avec succès', 'success');
            addLog(`[INFO] ${result.statistics.attributions_creees} attributions prévues`, 'info');
            
            displayResults(result, 'preview');
            displayStatistics(result.statistics);
            
            // Activer le bouton de validation
            document.getElementById('btnValidate').disabled = false;
            
            updateStatus('✅ Prévisualisation terminée', 
                'Vérifiez les résultats ci-dessous. Cliquez sur "Valider" pour appliquer la distribution.', 
                'success');
            
        } else {
            throw new Error('Erreur serveur');
        }
        
    } catch (error) {
        addLog('[ERROR] Erreur: ' + error.message, 'error');
        
        updateStatus('Erreur lors de la prévisualisation', error.message, 'error');
    }
    
    simulationRunning = false;
    document.getElementById('btnPreview').disabled = false;
    document.getElementById('btnReset').disabled = false;
}

// ==========================================
// VALIDATE SIMULATION
// ==========================================

async function validateSimulation() {
    if (!simulationResults) {
        alert('Veuillez d\'abord prévisualiser la simulation !');
        return;
    }
    
    if (!confirm('⚠️ ATTENTION: Cette action va dispatcher réellement les dons aux besoins. Continuer ?')) {
        return;
    }
    
    simulationMode = 'validate';
    simulationRunning = true;
    
    document.getElementById('btnPreview').disabled = true;
    document.getElementById('btnValidate').disabled = true;
    document.getElementById('btnReset').disabled = true;
    
    updateStatus('⚙️ Validation en cours...', 'Enregistrement des attributions dans la base de données', 'info');
    addLog('[VALIDATE] Mode validation activé', 'success');
    addLog('[VALIDATE] Dispatch des dons en cours...', 'info');
    
    try {
        // Appel API en mode validate
        const response = await fetch(`/api/dispatch/simulate`, {
            method: 'POST'
        });
        
        if (response.ok) {
            const result = await response.json();
            
            addLog('[SUCCESS] Dispatch validé et enregistré !', 'success');
            addLog(`[INFO] ${result.statistics.attributions_creees} attributions créées`, 'success');
            
            displayResults(result, 'validate');
            displayStatistics(result.statistics);
            
            updateStatus('🎉 Validation terminée !', 
                'Les dons ont été distribués avec succès. Consultez le récapitulatif pour plus de détails.', 
                'success');
            
            // Proposer d'aller au récapitulatif
            setTimeout(() => {
                if (confirm('Voulez-vous consulter le récapitulatif complet ?')) {
                    window.location.href = '/recapitulatif';
                }
            }, 2000);
            
        } else {
            throw new Error('Erreur serveur');
        }
        
    } catch (error) {
        addLog('[ERROR] Erreur: ' + error.message, 'error');
        
        updateStatus('Erreur lors de la validation', error.message, 'error');
    // Suppression du fallback localStorage : tout vient de l'API
        data: results;
        statistics: {
            attributions_creees: totalAttributions;
            dons_utilises: tousLesDons.length;
            total_dons: tousLesDons.length;
            villes_servies: Object.keys(results).length
        }
    };
}

// ==========================================
// DISPLAY FUNCTIONS
// ==========================================

async function displayResults(results, mode) {
    const resultsPanel = document.getElementById('resultsPanel');
    const resultsGrid = document.getElementById('resultsGrid');
    const modeBadge = document.getElementById('modeBadge');

    resultsPanel.style.display = 'block';
    resultsGrid.innerHTML = '';

    if (mode === 'preview') {
        modeBadge.className = 'mode-badge preview';
        modeBadge.textContent = '👁️ Prévisualisation';
    } else {
        modeBadge.className = 'mode-badge final';
        modeBadge.textContent = '✅ Validé';
    }

    // Fetch all cities for name mapping
    const villesList = await fetch('/api/dispatch/villes').then(r => r.json()).then(data => data.villes || []);
    const villesMap = {};
    villesList.forEach(v => {
        villesMap[v.id] = v.name;
    });

    // Regrouper les attributions par ville
    const attributions = results.data || [];
    let villes = {};
    // Charger les besoins pour info
    const besoins = await fetch('/api/dispatch/besoins').then(r => r.json());
        besoins.forEach(b => {
            if (!villes[b.idVille]) villes[b.idVille] = { totalBesoin: 0, totalAttribue: 0, attributions: [] };
            villes[b.idVille].totalBesoin += b.quantite * 1; // quantité
        });
        attributions.forEach(attr => {
            // Trouver la ville du besoin
            const besoin = besoins.find(b => b.id == attr.idBesoin);
            if (besoin) {
                villes[besoin.idVille].totalAttribue += attr.quantite * 1;
                villes[besoin.idVille].attributions.push(attr);
            }
        });
        Object.keys(villes).forEach((villeId, index) => {
            const cityData = villes[villeId];
            const tauxCouverture = cityData.totalBesoin > 0
                ? Math.round((cityData.totalAttribue / cityData.totalBesoin) * 100)
                : 0;
            const card = document.createElement('div');
            card.className = 'result-card';
            card.style.animationDelay = `${index * 0.1}s`;
            const villeName = villesMap[villeId];
            card.innerHTML = `
                <h4>${villeName}</h4>
                <div style="margin: 1rem 0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Besoin total:</span>
                        <strong>${formatMoney(cityData.totalBesoin)} Ar</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Attribué:</span>
                        <strong style="color: var(--success)">${formatMoney(cityData.totalAttribue)} Ar</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Reste:</span>
                        <strong style="color: ${cityData.totalBesoin - cityData.totalAttribue > 0 ? 'var(--danger)' : 'var(--success)'}">
                            ${formatMoney(cityData.totalBesoin - cityData.totalAttribue)} Ar
                        </strong>
                    </div>
                </div>
                <div class="progress-bar" style="margin-top: 1rem;">
                    <div class="progress-fill ${tauxCouverture === 100 ? 'complete' : ''}" 
                         style="width: ${tauxCouverture}%"></div>
                </div>
                <div style="text-align: center; margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                    ${tauxCouverture}% couvert (${cityData.attributions.length} attribution${cityData.attributions.length > 1 ? 's' : ''})
                </div>
            `;
            resultsGrid.appendChild(card);
        });
}

function displayStatistics(stats) {
    const statsPanel = document.getElementById('statsPanel');
    const statsGrid = document.getElementById('statsGrid');
    
    statsPanel.style.display = 'block';
    statsGrid.innerHTML = '';
    
    const statsArray = [
        { icon: '📊', label: 'Attributions créées', value: stats.attributions_creees || 0 },
        { icon: '📦', label: 'Dons utilisés', value: `${stats.dons_utilises || 0} / ${stats.total_dons || 0}` },
        { icon: '🏘️', label: 'Villes servies', value: stats.villes_servies || 0 },
        { icon: '📈', label: 'Taux de couverture', value: stats.taux_couverture_besoins || 'N/A' }
    ];
    
    statsArray.forEach((stat, index) => {
        const box = document.createElement('div');
        box.className = 'stat-box';
        box.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
        
        box.innerHTML = `
            <div class="stat-icon-large">${stat.icon}</div>
            <div class="stat-content">
                <h3>${stat.label}</h3>
                <p class="stat-number">${stat.value}</p>
            </div>
        `;
        
        statsGrid.appendChild(box);
    });
}

// ==========================================
// RESET & UTILITIES
// ==========================================

function resetSimulation() {
    if (simulationResults && !confirm('Réinitialiser la simulation ? Les résultats non validés seront perdus.')) {
        return;
    }
    
    simulationResults = null;
    simulationMode = null;
    
    document.getElementById('resultsPanel').style.display = 'none';
    document.getElementById('statsPanel').style.display = 'none';
    document.getElementById('btnPreview').disabled = false;
    document.getElementById('btnValidate').disabled = true;
    document.getElementById('btnReset').disabled = false;
    
    updateStatus('Prêt à simuler', 'Choisissez une action ci-dessous', 'info');
    clearLog();
    addLog('[INFO] Système réinitialisé et prêt...', 'info');
    addLog('[INFO] Mode prévisualisation disponible', 'info');
}

function updateStatus(title, message, type = 'info') {
    const status = document.getElementById('simStatus');
    
    let bgColor = 'var(--bg-tertiary)';
    let textColor = 'var(--text-primary)';
    
    if (type === 'success') {
        bgColor = 'rgba(74, 139, 111, 0.1)';
        textColor = 'var(--success)';
    } else if (type === 'warning') {
        bgColor = 'rgba(217, 127, 58, 0.1)';
        textColor = 'var(--warning)';
    }
    
    status.style.background = bgColor;
    status.innerHTML = `
        <h3 style="color: ${textColor}">${title}</h3>
        <p>${message}</p>
    `;
}

function addLog(message, type = 'info') {
    const container = document.getElementById('logContainer');
    const entry = document.createElement('div');
    entry.className = `log-entry log-${type}`;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
    
    container.appendChild(entry);
    container.scrollTop = container.scrollHeight;
}

function clearLog() {
    document.getElementById('logContainer').innerHTML = '';
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}
