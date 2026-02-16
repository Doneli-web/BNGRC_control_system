// ===================================
// BNGRC - Dashboard Script
// ===================================

// Sample data for cities
const citiesData = [
    {
        name: "Antananarivo",
        besoins: [
            { categorie: "nature", article: "Riz", quantite: 500, unite: "kg", prixUnitaire: 5000, attribue: 350 },
            { categorie: "nature", article: "Huile", quantite: 200, unite: "L", prixUnitaire: 12000, attribue: 120 },
            { categorie: "materiel", article: "Tôles", quantite: 100, unite: "unités", prixUnitaire: 25000, attribue: 100 }
        ],
        status: "urgent"
    },
    {
        name: "Toamasina",
        besoins: [
            { categorie: "nature", article: "Riz", quantite: 800, unite: "kg", prixUnitaire: 5000, attribue: 600 },
            { categorie: "materiel", article: "Bâches", quantite: 50, unite: "m²", prixUnitaire: 8000, attribue: 30 }
        ],
        status: "warning"
    },
    {
        name: "Antsirabe",
        besoins: [
            { categorie: "nature", article: "Eau potable", quantite: 1000, unite: "L", prixUnitaire: 2000, attribue: 1000 },
            { categorie: "argent", article: "Don financier", quantite: 1, unite: "", prixUnitaire: 2000000, attribue: 2000000 }
        ],
        status: "good"
    },
    {
        name: "Mahajanga",
        besoins: [
            { categorie: "nature", article: "Riz", quantite: 300, unite: "kg", prixUnitaire: 5000, attribue: 100 },
            { categorie: "materiel", article: "Clous", quantite: 20, unite: "kg", prixUnitaire: 15000, attribue: 15 }
        ],
        status: "urgent"
    },
    {
        name: "Fianarantsoa",
        besoins: [
            { categorie: "nature", article: "Huile", quantite: 150, unite: "L", prixUnitaire: 12000, attribue: 100 },
            { categorie: "materiel", article: "Tôles", quantite: 80, unite: "unités", prixUnitaire: 25000, attribue: 60 }
        ],
        status: "warning"
    },
    {
        name: "Toliara",
        besoins: [
            { categorie: "nature", article: "Riz", quantite: 600, unite: "kg", prixUnitaire: 5000, attribue: 200 },
            { categorie: "argent", article: "Don financier", quantite: 1, unite: "", prixUnitaire: 3000000, attribue: 1500000 }
        ],
        status: "urgent"
    }
];

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
    const totalVilles = citiesData.length;
    const totalDons = 847; // From sample data
    
    let totalBesoinsGlobal = 0;
    let totalAttribueGlobal = 0;
    
    citiesData.forEach(city => {
        totalBesoinsGlobal += calculateTotalBesoins(city.besoins);
        totalAttribueGlobal += calculateTotalAttribue(city.besoins);
    });
    
    const tauxAttribution = totalBesoinsGlobal > 0 
        ? Math.round((totalAttribueGlobal / totalBesoinsGlobal) * 100) 
        : 0;
    
    // Update stat cards with animation
    animateValue('totalVilles', 0, totalVilles, 1000);
    animateValue('totalDons', 0, totalDons, 1500);
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
