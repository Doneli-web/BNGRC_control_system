// ===================================
// BNGRC - Simulation V2 Refactor
// ===================================

let simulationResults = null;
let simulationMode = null; // 'preview' ou 'validate'
let simulationRunning = false;

// Init
document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    checkDataAvailability();
});

// =====================
// NAVIGATION
// =====================
function initNavigation() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.querySelector('.nav-menu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
    }
}

// =====================
// CHECK DATA
// =====================
async function checkDataAvailability() {
    try {
        const res = await fetch(`/api/dispatch/preview`, { method: 'POST' });
        const result = await res.json();

        if ((result.statistics?.attributions_creees > 0) || (result.data?.length > 0)) {
            addLog('[INFO] Données chargées depuis la base', 'info');
            document.getElementById('btnPreview').disabled = false;
        } else {
            updateStatus('⚠️ Attention', 'Aucun besoin ou don disponible en base', 'warning');
            document.getElementById('btnPreview').disabled = true;
        }
    } catch {
        updateStatus('⚠️ Erreur', 'Impossible de charger les données', 'warning');
        document.getElementById('btnPreview').disabled = true;
    }
}

// =====================
// PREVIEW
// =====================
async function previewSimulation() {
    if (simulationRunning) return;
    simulationMode = 'preview';
    simulationRunning = true;

    disableButtons();

    updateStatus('🔍 Prévisualisation en cours...', 'Calcul des attributions sans modification des données', 'info');
    clearLog();
    addLog('[PREVIEW] Mode prévisualisation activé', 'info');

    try {
        const response = await fetch(`/api/dispatch/preview`, { method: 'POST' });
        if (!response.ok) throw new Error('Erreur serveur');
        const result = await response.json();

        simulationResults = result;

        addLog('[SUCCESS] Simulation calculée avec succès', 'success');
        addLog(`[INFO] ${result.statistics.attributions_creees} attributions prévues`, 'info');

        await displayResults(result, 'preview');
        displayStatistics(result.statistics);

        document.getElementById('btnValidate').disabled = false;

        updateStatus('✅ Prévisualisation terminée', 
            'Vérifiez les résultats ci-dessous. Cliquez sur "Valider" pour appliquer la distribution.', 
            'success');
    } catch (error) {
        addLog('[ERROR] Erreur: ' + error.message, 'error');
        updateStatus('Erreur lors de la prévisualisation', error.message, 'error');
    }

    simulationRunning = false;
    enableButtons();
}

// =====================
// VALIDATE
// =====================
async function validateSimulation() {
    if (!simulationResults) {
        alert('Veuillez d\'abord prévisualiser la simulation !');
        return;
    }
    if (!confirm('⚠️ ATTENTION: Cette action va dispatcher réellement les dons aux besoins. Continuer ?')) return;

    simulationMode = 'validate';
    simulationRunning = true;
    disableButtons();

    updateStatus('⚙️ Validation en cours...', 'Enregistrement des attributions dans la base de données', 'info');
    addLog('[VALIDATE] Mode validation activé', 'success');

    try {
        const response = await fetch(`/api/dispatch/simulate`, { method: 'POST' });
        if (!response.ok) throw new Error('Erreur serveur');
        const result = await response.json();

        simulationResults = result;

        addLog('[SUCCESS] Dispatch validé et enregistré !', 'success');
        addLog(`[INFO] ${result.statistics.attributions_creees} attributions créées`, 'success');

        await displayResults(result, 'validate');
        displayStatistics(result.statistics);

        updateStatus('🎉 Validation terminée !', 
            'Les dons ont été distribués avec succès. Consultez le récapitulatif pour plus de détails.', 
            'success');

        setTimeout(() => {
            if (confirm('Voulez-vous consulter le récapitulatif complet ?')) {
                window.location.href = '/recapitulatif';
            }
        }, 2000);

    } catch (error) {
        addLog('[ERROR] Erreur: ' + error.message, 'error');
        updateStatus('Erreur lors de la validation', error.message, 'error');
    }

    simulationRunning = false;
    enableButtons();
}

// =====================
// DISPLAY
// =====================
async function displayResults(results, mode) {
    const resultsPanel = document.getElementById('resultsPanel');
    const resultsGrid = document.getElementById('resultsGrid');
    const modeBadge = document.getElementById('modeBadge');

    resultsPanel.style.display = 'block';
    resultsGrid.innerHTML = '';

    modeBadge.className = mode === 'preview' ? 'mode-badge preview' : 'mode-badge final';
    modeBadge.textContent = mode === 'preview' ? '👁️ Prévisualisation' : '✅ Validé';

    const villesList = await fetch('/api/dispatch/villes').then(r => r.json()).then(d => d.villes || []);
    const villesMap = {};
    villesList.forEach(v => villesMap[v.id] = v.name);

    const attributions = results.data || [];
    const besoins = await fetch('/api/dispatch/besoins').then(r => r.json());
    let villes = {};

    besoins.forEach(b => {
        if (!villes[b.idVille]) villes[b.idVille] = { totalBesoin: 0, totalAttribue: 0, attributions: [] };
        villes[b.idVille].totalBesoin += b.quantite * 1;
    });

    attributions.forEach(attr => {
        const besoin = besoins.find(b => b.id == attr.idBesoin);
        if (besoin) {
            villes[besoin.idVille].totalAttribue += attr.quantite * 1;
            villes[besoin.idVille].attributions.push(attr);
        }
    });

    Object.keys(villes).forEach((IdVille, index) => {
        const cityData = villes[IdVille];
        const tauxCouverture = cityData.totalBesoin > 0
            ? Math.round((cityData.totalAttribue / cityData.totalBesoin) * 100)
            : 0;
        const card = document.createElement('div');
        card.className = 'result-card';
        card.style.animationDelay = `${index * 0.1}s`;
        const villeName = villesMap[IdVille] || `Ville ${IdVille}`;
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
        { icon: '📊', label: 'Attributions créées', value: stats.attributions_crees || 0 },
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

// =====================
// RESET / UTILS
// =====================
function resetSimulation() {
    if (simulationResults && !confirm('Réinitialiser la simulation ? Les résultats non validés seront perdus.')) return;

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

function disableButtons() {
    document.getElementById('btnPreview').disabled = true;
    document.getElementById('btnValidate').disabled = true;
    document.getElementById('btnReset').disabled = true;
}

function enableButtons() {
    document.getElementById('btnPreview').disabled = false;
    document.getElementById('btnReset').disabled = false;
}

function updateStatus(title, message, type = 'info') {
    const status = document.getElementById('simStatus');
    let bg = 'var(--bg-tertiary)';
    let color = 'var(--text-primary)';

    if (type === 'success') { bg='rgba(74,139,111,0.1)'; color='var(--success)'; }
    if (type === 'warning') { bg='rgba(217,127,58,0.1)'; color='var(--warning)'; }

    status.style.background = bg;
    status.innerHTML = `<h3 style="color:${color}">${title}</h3><p>${message}</p>`;
}

function addLog(message, type='info') {
    const container = document.getElementById('logContainer');
    const entry = document.createElement('div');
    entry.className = `log-entry log-${type}`;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
    container.appendChild(entry);
    container.scrollTop = container.scrollHeight;
}

function clearLog() { document.getElementById('logContainer').innerHTML = ''; }

function formatMoney(amount) { return new Intl.NumberFormat('fr-MG').format(amount); }
