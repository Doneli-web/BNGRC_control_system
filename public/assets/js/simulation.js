// ===================================
// BNGRC - Simulation Script
// ===================================

let simulationResults = [];
let simulationRunning = false;

// Initialize page
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

// Check if data is available
function checkDataAvailability() {
    const besoins = JSON.parse(localStorage.getItem('besoins')) || [];
    const dons = JSON.parse(localStorage.getItem('dons')) || [];
    
    if (besoins.length === 0 || dons.length === 0) {
        updateStatus('Attention', 'Veuillez d\'abord enregistrer des besoins et des dons avant de lancer la simulation', 'warning');
        document.getElementById('btnStart').disabled = true;
    } else {
        addLog(`Données chargées: ${besoins.length} besoins, ${dons.length} dons`, 'info');
    }
}

// Start simulation
async function startSimulation() {
    if (simulationRunning) return;
    
    simulationRunning = true;
    document.getElementById('btnStart').disabled = true;
    document.getElementById('btnReset').disabled = true;
    document.getElementById('btnExport').disabled = true;
    
    updateStatus('Simulation en cours...', 'Traitement des données', 'info');
    clearLog();
    
    // Step 1: Load data
    await simulateStep(1, 'Chargement des données');
    const besoins = JSON.parse(localStorage.getItem('besoins')) || [];
    const dons = JSON.parse(localStorage.getItem('dons')) || [];
    addLog(`Chargé: ${besoins.length} besoins et ${dons.length} dons`, 'success');
    await delay(800);
    
    // Step 2: Sort and prepare
    await simulateStep(2, 'Tri des dons par date');
    const sortedDons = sortDonsByDate(dons);
    addLog(`Dons triés par ordre chronologique`, 'success');
    await delay(800);
    
    // Step 3: Group by city
    await simulateStep(3, 'Regroupement des besoins par ville');
    const besoinsParVille = groupBesoinsParVille(besoins);
    addLog(`Besoins regroupés pour ${Object.keys(besoinsParVille).length} villes`, 'success');
    await delay(800);
    
    // Step 4: Distribution
    await simulateStep(4, 'Distribution des dons');
    const results = distribueDons(sortedDons, besoinsParVille);
    simulationResults = results;
    addLog(`Distribution complétée`, 'success');
    await delay(800);
    
    // Step 5: Finalize
    await simulateStep(5, 'Génération des résultats');
    await delay(500);
    
    displayResults(results);
    displayStatistics(results, sortedDons, besoins);
    
    updateStatus('Simulation terminée', 'Distribution effectuée avec succès', 'success');
    document.getElementById('btnReset').disabled = false;
    document.getElementById('btnExport').disabled = false;
    
    addLog('='.repeat(50), 'info');
    addLog('SIMULATION TERMINÉE', 'success');
    
    simulationRunning = false;
}

// Sort dons by date
function sortDonsByDate(dons) {
    return [...dons].sort((a, b) => {
        const dateA = new Date(a.dateReception);
        const dateB = new Date(b.dateReception);
        return dateA - dateB;
    });
}

// Group besoins par ville
function groupBesoinsParVille(besoins) {
    const grouped = {};
    
    besoins.forEach(besoin => {
        if (!grouped[besoin.ville]) {
            grouped[besoin.ville] = [];
        }
        grouped[besoin.ville].push({
            ...besoin,
            quantiteRestante: besoin.quantite,
            montantRestant: besoin.montantTotal
        });
    });
    
    return grouped;
}

// Distribute dons
function distribueDons(dons, besoinsParVille) {
    const results = {};
    const donsUtilises = [];
    
    // Initialize results for each city
    Object.keys(besoinsParVille).forEach(ville => {
        results[ville] = {
            besoins: [...besoinsParVille[ville]],
            attributions: [],
            totalAttribue: 0,
            totalBesoin: besoinsParVille[ville].reduce((sum, b) => sum + b.montantTotal, 0)
        };
    });
    
    // Process each don
    dons.forEach((don, index) => {
        let quantiteRestante = don.quantite;
        const attribution = {
            donId: don.id,
            donateur: don.donateur,
            article: don.article,
            quantiteInitiale: don.quantite,
            repartition: []
        };
        
        addLog(`Traitement don #${index + 1}: ${don.quantite} ${don.unite} ${don.article} de ${don.donateur}`, 'info');
        
        // Try to match with besoins
        Object.keys(results).forEach(ville => {
            const villeBesoins = results[ville].besoins;
            
            villeBesoins.forEach(besoin => {
                if (quantiteRestante > 0 && 
                    besoin.article === don.article && 
                    besoin.categorie === don.categorie &&
                    besoin.quantiteRestante > 0) {
                    
                    const quantiteAttribuee = Math.min(quantiteRestante, besoin.quantiteRestante);
                    const montantAttribue = quantiteAttribuee * besoin.prixUnitaire;
                    
                    attribution.repartition.push({
                        ville: ville,
                        quantite: quantiteAttribuee,
                        montant: montantAttribue
                    });
                    
                    results[ville].attributions.push({
                        article: don.article,
                        quantite: quantiteAttribuee,
                        unite: don.unite,
                        montant: montantAttribue,
                        donateur: don.donateur,
                        date: don.date
                    });
                    
                    results[ville].totalAttribue += montantAttribue;
                    
                    besoin.quantiteRestante -= quantiteAttribuee;
                    besoin.montantRestant -= montantAttribue;
                    quantiteRestante -= quantiteAttribuee;
                    
                    addLog(`  → Attribué ${quantiteAttribuee} ${don.unite} à ${ville}`, 'success');
                }
            });
        });
        
        if (attribution.repartition.length > 0) {
            donsUtilises.push(attribution);
            
            if (quantiteRestante > 0) {
                addLog(`  ⚠ ${quantiteRestante} ${don.unite} non attribués (pas de besoin correspondant)`, 'warning');
            }
        } else {
            addLog(`  ⚠ Don non attribué: aucun besoin correspondant`, 'warning');
        }
    });
    
    return results;
}

// Display results
function displayResults(results) {
    const resultsPanel = document.getElementById('resultsPanel');
    const resultsGrid = document.getElementById('resultsGrid');
    
    resultsPanel.style.display = 'block';
    resultsGrid.innerHTML = '';
    
    Object.keys(results).forEach((ville, index) => {
        const data = results[ville];
        const tauxCouverture = data.totalBesoin > 0 
            ? Math.round((data.totalAttribue / data.totalBesoin) * 100) 
            : 0;
        
        const card = document.createElement('div');
        card.className = 'result-card';
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
            <h4>${ville}</h4>
            <div class="result-item">
                <span>Besoin total:</span>
                <strong>${formatMoney(data.totalBesoin)} Ar</strong>
            </div>
            <div class="result-item">
                <span>Montant attribué:</span>
                <strong style="color: var(--success)">${formatMoney(data.totalAttribue)} Ar</strong>
            </div>
            <div class="result-item">
                <span>Reste à couvrir:</span>
                <strong style="color: ${data.totalBesoin - data.totalAttribue > 0 ? 'var(--danger)' : 'var(--success)'}">
                    ${formatMoney(data.totalBesoin - data.totalAttribue)} Ar
                </strong>
            </div>
            <div style="margin-top: 1rem;">
                <div class="progress-label">
                    <span>Taux de couverture</span>
                    <span>${tauxCouverture}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill ${tauxCouverture === 100 ? 'complete' : ''}" 
                         style="width: ${tauxCouverture}%"></div>
                </div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                ${data.attributions.length} attribution(s)
            </div>
        `;
        
        resultsGrid.appendChild(card);
    });
}

// Display statistics
function displayStatistics(results, dons, besoins) {
    const statsPanel = document.getElementById('statsPanel');
    const statsGrid = document.getElementById('statsGrid');
    
    statsPanel.style.display = 'block';
    statsGrid.innerHTML = '';
    
    const totalBesoin = Object.values(results).reduce((sum, r) => sum + r.totalBesoin, 0);
    const totalAttribue = Object.values(results).reduce((sum, r) => sum + r.totalAttribue, 0);
    const tauxGlobal = totalBesoin > 0 ? Math.round((totalAttribue / totalBesoin) * 100) : 0;
    
    const donsUtilises = dons.filter(don => {
        return Object.values(results).some(r => 
            r.attributions.some(a => a.donateur === don.donateur && a.article === don.article)
        );
    }).length;
    
    const stats = [
        {
            icon: '🎯',
            label: 'Taux de couverture global',
            value: `${tauxGlobal}%`,
            change: tauxGlobal >= 75 ? 'Excellent' : tauxGlobal >= 50 ? 'Bon' : 'Insuffisant'
        },
        {
            icon: '💰',
            label: 'Montant total distribué',
            value: `${formatMoney(totalAttribue)} Ar`,
            change: `sur ${formatMoney(totalBesoin)} Ar`
        },
        {
            icon: '📦',
            label: 'Dons utilisés',
            value: `${donsUtilises} / ${dons.length}`,
            change: `${Math.round((donsUtilises / dons.length) * 100)}% utilisés`
        },
        {
            icon: '🏘️',
            label: 'Villes servies',
            value: `${Object.keys(results).length}`,
            change: `${besoins.length} besoins traités`
        }
    ];
    
    stats.forEach((stat, index) => {
        const box = document.createElement('div');
        box.className = 'stat-box';
        box.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
        
        box.innerHTML = `
            <div class="stat-icon-large">${stat.icon}</div>
            <div class="stat-content">
                <h3>${stat.label}</h3>
                <p class="stat-number">${stat.value}</p>
                <span class="stat-change">${stat.change}</span>
            </div>
        `;
        
        statsGrid.appendChild(box);
    });
}

// Simulate step
async function simulateStep(step, message) {
    const steps = document.querySelectorAll('.progress-step');
    
    // Mark previous steps as complete
    steps.forEach((s, i) => {
        if (i < step - 1) {
            s.classList.add('complete');
            s.classList.remove('active');
        }
    });
    
    // Mark current step as active
    steps[step - 1].classList.add('active');
    
    addLog(`[Étape ${step}/5] ${message}...`, 'info');
}

// Update status
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

// Add log entry
function addLog(message, type = 'info') {
    const container = document.getElementById('logContainer');
    const entry = document.createElement('div');
    entry.className = `log-entry log-${type}`;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
    
    container.appendChild(entry);
    container.scrollTop = container.scrollHeight;
}

// Clear log
function clearLog() {
    document.getElementById('logContainer').innerHTML = '';
}

// Reset simulation
function resetSimulation() {
    simulationResults = [];
    
    // Reset progress
    document.querySelectorAll('.progress-step').forEach(step => {
        step.classList.remove('active', 'complete');
    });
    
    // Hide results
    document.getElementById('resultsPanel').style.display = 'none';
    document.getElementById('statsPanel').style.display = 'none';
    
    // Reset status
    updateStatus('Prêt à simuler', 'Cliquez sur "Lancer la simulation" pour démarrer la répartition automatique', 'info');
    
    // Reset buttons
    document.getElementById('btnStart').disabled = false;
    document.getElementById('btnReset').disabled = true;
    document.getElementById('btnExport').disabled = true;
    
    // Clear log and add initial message
    clearLog();
    addLog('Système réinitialisé et prêt...', 'info');
    
    checkDataAvailability();
}

// Export results
function exportResults() {
    if (simulationResults.length === 0) {
        alert('Aucun résultat à exporter. Veuillez d\'abord lancer la simulation.');
        return;
    }
    
    const data = [];
    
    Object.keys(simulationResults).forEach(ville => {
        const result = simulationResults[ville];
        data.push({
            ville: ville,
            besoin_total: result.totalBesoin,
            montant_attribue: result.totalAttribue,
            reste_a_couvrir: result.totalBesoin - result.totalAttribue,
            taux_couverture: Math.round((result.totalAttribue / result.totalBesoin) * 100) + '%',
            nombre_attributions: result.attributions.length
        });
    });
    
    const csv = convertToCSV(data);
    downloadCSV(csv, `simulation-distribution-${new Date().toISOString().split('T')[0]}.csv`);
    
    addLog('Résultats exportés avec succès', 'success');
}

// Helper functions
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}

function convertToCSV(data) {
    if (data.length === 0) return '';
    
    const headers = Object.keys(data[0]);
    const rows = data.map(obj => 
        headers.map(header => JSON.stringify(obj[header] || '')).join(',')
    );
    
    return [headers.join(','), ...rows].join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(blob, filename);
    } else {
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
}
