// ===================================
// BNGRC - Simulation V2 Script
// ===================================

const API_BASE_URL = 'http://localhost:8000/api';

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
function checkDataAvailability() {
    const besoins = JSON.parse(localStorage.getItem('besoins')) || [];
    const dons = JSON.parse(localStorage.getItem('dons')) || [];
    
    if (besoins.length === 0 || dons.length === 0) {
        updateStatus('⚠️ Attention', 'Veuillez enregistrer des besoins et des dons avant la simulation', 'warning');
        document.getElementById('btnPreview').disabled = true;
    } else {
        addLog(`[INFO] ${besoins.length} besoins et ${dons.length} dons chargés`, 'info');
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
        const response = await fetch(`${API_BASE_URL}/dispatch/simulate?mode=preview`, {
            method: 'POST'
        });
        
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
        
        // Fallback: simulation locale
        addLog('[INFO] Utilisation de la simulation locale', 'info');
        const localResults = runLocalSimulation();
        simulationResults = localResults;
        
        displayResults(localResults, 'preview');
        displayStatistics(localResults.statistics);
        
        document.getElementById('btnValidate').disabled = false;
        updateStatus('✅ Prévisualisation terminée (mode local)', 
            'Vérifiez les résultats. Cliquez sur "Valider" pour enregistrer.', 
            'success');
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
        const response = await fetch(`${API_BASE_URL}/dispatch/simulate`, {
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
                    window.location.href = 'recapitulatif.html';
                }
            }, 2000);
            
        } else {
            throw new Error('Erreur serveur');
        }
        
    } catch (error) {
        addLog('[ERROR] Erreur: ' + error.message, 'error');
        
        // Fallback: enregistrement local
        addLog('[INFO] Enregistrement local des résultats', 'warning');
        localStorage.setItem('dispatchResults', JSON.stringify(simulationResults));
        
        addLog('[SUCCESS] Résultats sauvegardés localement', 'success');
        updateStatus('✅ Validation terminée (mode local)', 
            'Les résultats ont été enregistrés. Consultez le récapitulatif.', 
            'success');
    }
    
    simulationRunning = false;
    document.getElementById('btnReset').disabled = false;
}

// ==========================================
// LOCAL SIMULATION (Fallback)
// ==========================================

function runLocalSimulation() {
    const besoins = JSON.parse(localStorage.getItem('besoins')) || [];
    const dons = JSON.parse(localStorage.getItem('dons')) || [];
    const achats = JSON.parse(localStorage.getItem('achats')) || [];
    
    // Combiner dons normaux + achats (convertis en dons)
    const tousLesDons = [...dons];
    
    achats.forEach(achat => {
        tousLesDons.push({
            id: 'achat_' + achat.id,
            article: achat.article,
            quantite: achat.quantite,
            date_de_saisie: achat.date,
            source: 'achat'
        });
    });
    
    // Trier par date
    tousLesDons.sort((a, b) => new Date(a.date_de_saisie) - new Date(b.date_de_saisie));
    
    // Grouper besoins par ville
    const besoinsParVille = {};
    besoins.forEach(b => {
        if (!besoinsParVille[b.ville]) {
            besoinsParVille[b.ville] = [];
        }
        besoinsParVille[b.ville].push({
            ...b,
            quantiteRestante: b.quantite
        });
    });
    
    // Algorithme de dispatch
    const results = {};
    let totalAttributions = 0;
    
    Object.keys(besoinsParVille).forEach(ville => {
        results[ville] = {
            besoins: besoinsParVille[ville],
            attributions: [],
            totalAttribue: 0,
            totalBesoin: besoinsParVille[ville].reduce((sum, b) => sum + b.montantTotal, 0)
        };
    });
    
    tousLesDons.forEach((don, index) => {
        let donQtyRestante = don.quantite;
        
        addLog(`[PROCESS] Don #${index + 1}: ${don.quantite} ${don.article}`, 'info');
        
        Object.keys(results).forEach(ville => {
            results[ville].besoins.forEach(besoin => {
                if (donQtyRestante <= 0) return;
                if (besoin.article !== don.article) return;
                if (besoin.quantiteRestante <= 0) return;
                
                const qtyAttribuee = Math.min(donQtyRestante, besoin.quantiteRestante);
                const montantAttribue = qtyAttribuee * besoin.prixUnitaire;
                
                results[ville].attributions.push({
                    article: don.article,
                    quantite: qtyAttribuee,
                    montant: montantAttribue,
                    source: don.source || 'don'
                });
                
                results[ville].totalAttribue += montantAttribue;
                besoin.quantiteRestante -= qtyAttribuee;
                donQtyRestante -= qtyAttribuee;
                totalAttributions++;
                
                addLog(`  → ${qtyAttribuee} attribués à ${ville}`, 'success');
            });
        });
    });
    
    return {
        success: true,
        data: results,
        statistics: {
            attributions_creees: totalAttributions,
            dons_utilises: tousLesDons.length,
            total_dons: tousLesDons.length,
            villes_servies: Object.keys(results).length
        }
    };
}

// ==========================================
// DISPLAY FUNCTIONS
// ==========================================

function displayResults(results, mode) {
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
    
    const data = results.data || results;
    
    Object.keys(data).forEach((ville, index) => {
        const cityData = data[ville];
        const tauxCouverture = cityData.totalBesoin > 0 
            ? Math.round((cityData.totalAttribue / cityData.totalBesoin) * 100) 
            : 0;
        
        const card = document.createElement('div');
        card.className = 'result-card';
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
            <h4>${ville}</h4>
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
