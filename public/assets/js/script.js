// ===================================
// BNGRC - Dashboard Script
// ===================================

// Sample data for cities
let citiesData = [];

document.addEventListener('DOMContentLoaded', async function() {
    initNavigation();
    await loadDashboardData(); // ⚡ important
    renderCities();
    updateStats();
    initFilters();
});

async function loadDashboardData() {
    try {
        const response = await fetch('/dashboard');
        const data = await response.json();

        citiesData = transformData(data);

    } catch (error) {
        console.error("Erreur chargement dashboard:", error);
    }
}

function transformData(rows) {
    const villes = {};

    rows.forEach(row => {
        if (!row.ville) return;

        if (!villes[row.ville]) {
            villes[row.ville] = {
                name: row.ville,
                besoins: [],
                status: "good"
            };
        }

        villes[row.ville].besoins.push({
            categorie: detectCategory(row.article),
            article: row.article,
            quantite: Number(row.quantite || 0),
            unite: "",
            prixUnitaire: Number(row.prix_unitaire || 0),
            attribue: Number(row.attribue || 0)
        });
    });

    return Object.values(villes);
}

function detectCategory(article) {
    const nature = ['Riz', 'Huile', 'Eau'];
    const materiel = ['Tôle', 'Clou', 'Bois'];

    if (nature.includes(article)) return 'nature';
    if (materiel.includes(article)) return 'materiel';
    return 'argent';
}


// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    renderCities();
    updateStats();
    initFilters();
});

// Navigation functionality
function initNavigation() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
    }
}

// Render city cards
function renderCities(filteredData = citiesData) {
    const citiesGrid = document.getElementById('citiesGrid');
    if (!citiesGrid) return;
    
    citiesGrid.innerHTML = '';
    
    filteredData.forEach((city, index) => {
        const totalBesoins = calculateTotalBesoins(city.besoins);
        const totalAttribue = calculateTotalAttribue(city.besoins);
        const tauxCouverture = totalBesoins > 0 ? Math.round((totalAttribue / totalBesoins) * 100) : 0;
        
        const cityCard = document.createElement('div');
        cityCard.className = 'city-card';
        cityCard.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
        
        cityCard.innerHTML = `
            <div class="city-header">
                <h3 class="city-name">${city.name}</h3>
                <span class="city-status ${city.status}">
                    ${getStatusLabel(city.status)}
                </span>
            </div>
            
            <div class="needs-summary">
                ${city.besoins.map(besoin => {
                    const montantBesoin = besoin.quantite * besoin.prixUnitaire;
                    const montantAttribue = besoin.attribue * besoin.prixUnitaire;
                    return `
                        <div class="need-item">
                            <span class="need-label">${besoin.article}</span>
                            <span class="need-value">${besoin.attribue}/${besoin.quantite} ${besoin.unite}</span>
                        </div>
                    `;
                }).join('')}
            </div>
            
            <div class="progress-group">
                <div class="progress-label">
                    <span>Couverture globale</span>
                    <span>${tauxCouverture}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill ${tauxCouverture === 100 ? 'complete' : ''}" 
                         style="width: ${tauxCouverture}%"></div>
                </div>
            </div>
            
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
                <div class="need-item" style="border: none;">
                    <span class="need-label">Budget nécessaire</span>
                    <span class="need-value" style="color: var(--primary);">
                        ${formatMoney(totalBesoins - totalAttribue)} Ar
                    </span>
                </div>
            </div>
        `;
        
        citiesGrid.appendChild(cityCard);
    });
}

// Calculate totals
function calculateTotalBesoins(besoins) {
    return besoins.reduce((total, b) => total + (b.quantite * b.prixUnitaire), 0);
}

function calculateTotalAttribue(besoins) {
    return besoins.reduce((total, b) => total + (b.attribue * b.prixUnitaire), 0);
}

// Update statistics
function updateStats() {
    const totalVillesElement = document.getElementById('totalVilles');
    const totalVilles = citiesData.length > 0 ? citiesData.length : (parseInt(totalVillesElement.textContent) || 0);
    const totalDons = document.getElementById("totalDons").textContent;
    
    let totalBesoinsGlobal = 0;
    let totalAttribueGlobal = 0;
    
    citiesData.forEach(city => {
        totalBesoinsGlobal += calculateTotalBesoins(city.besoins);
        totalAttribueGlobal += calculateTotalAttribue(city.besoins);
    });
    
    const tauxAttribution = totalBesoinsGlobal > 0 
        ? Math.round((totalAttribueGlobal / totalBesoinsGlobal) * 100) 
        : 0;
    
    animateValue('totalVilles', 0, totalVilles, 1000);
    animateValue('totalDons', parseInt(totalDons) || 0, parseInt(totalDons) || 0, 500);
    animateValue('tauxAttribution', 0, tauxAttribution, 2000, '%');
}

// Animate number values
function animateValue(id, start, end, duration, suffix = '') {
    const element = document.getElementById(id);
    if (!element) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current) + suffix;
    }, 16);
}

// Filter functionality
function initFilters() {
    const filterType = document.getElementById('filterType');
    const filterStatut = document.getElementById('filterStatut');
    
    if (filterType) {
        filterType.addEventListener('change', applyFilters);
    }
    
    if (filterStatut) {
        filterStatut.addEventListener('change', applyFilters);
    }
}

function applyFilters() {
    const typeFilter = document.getElementById('filterType')?.value || 'all';
    const statutFilter = document.getElementById('filterStatut')?.value || 'all';
    
    let filtered = [...citiesData];
    
    // Filter by status
    if (statutFilter !== 'all') {
        filtered = filtered.filter(city => {
            const totalBesoins = calculateTotalBesoins(city.besoins);
            const totalAttribue = calculateTotalAttribue(city.besoins);
            const taux = totalBesoins > 0 ? (totalAttribue / totalBesoins) : 0;
            
            switch(statutFilter) {
                case 'urgent':
                    return taux < 0.5;
                case 'partiel':
                    return taux >= 0.5 && taux < 1;
                case 'complet':
                    return taux >= 1;
                default:
                    return true;
            }
        });
    }
    
    // Filter by type
    if (typeFilter !== 'all') {
        filtered = filtered.filter(city => {
            return city.besoins.some(b => b.categorie === typeFilter);
        });
    }
    
    renderCities(filtered);
}

// Helper functions
function getStatusLabel(status) {
    const labels = {
        'urgent': 'Urgent',
        'warning': 'Modéré',
        'good': 'Bon'
    };
    return labels[status] || status;
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-MG').format(amount);
}

// Export data functionality
function exportData() {
    const data = citiesData.map(city => {
        const totalBesoins = calculateTotalBesoins(city.besoins);
        const totalAttribue = calculateTotalAttribue(city.besoins);
        
        return {
            ville: city.name,
            total_besoins: totalBesoins,
            total_attribue: totalAttribue,
            reste: totalBesoins - totalAttribue,
            taux_couverture: Math.round((totalAttribue / totalBesoins) * 100) + '%',
            status: city.status
        };
    });
    
    // Convert to CSV
    const csv = convertToCSV(data);
    downloadCSV(csv, 'tableau-bord-bngrc.csv');
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

// Smooth scroll for internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});





// Réinitialisation complète (avec état initial)
function resetComplet() {
    if(!confirm('⚠️ Attention ! Cette action va restaurer les quantités initiales et remettre à zéro toutes les distributions. Voulez-vous continuer ?')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="loading-spinner"></span> Réinitialisation complète...';
    btn.disabled = true;
    
    fetch('/reset-complet')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showNotification('✅ ' + data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('❌ Erreur de communication avec le serveur', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}



// Fonction pour afficher les notifications
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4a8b6f' : '#c44536'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        font-weight: 500;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Ajouter les styles si pas déjà présents
if(!document.getElementById('reset-styles')) {
    const style = document.createElement('style');
    style.id = 'reset-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}
