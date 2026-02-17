// ===================================
// BNGRC - Achats Management Script
// ===================================

// Variables globales
let fraisActuel = typeof fraisInitial !== 'undefined' ? fraisInitial : 10;
let dernierCalcul = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script achat.js chargé');
    initNavigation();
    initEventListeners();
    loadHistorique(); // Charger l'historique au démarrage
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

// Event Listeners
function initEventListeners() {
    // Don select change
    const donSelect = document.getElementById('donSelect');
    if(donSelect) {
        donSelect.addEventListener('change', loadDonDetails);
    }
    
    // Besoin select change
    const besoinSelect = document.getElementById('besoinSelect');
    if(besoinSelect) {
        besoinSelect.addEventListener('change', loadBesoinDetails);
    }
    
    // Montant input change
    const montantInput = document.getElementById('montantInput');
    if(montantInput) {
        montantInput.addEventListener('input', function() {
            document.getElementById('calculResultat').style.display = 'none';
        });
    }
}

// ===================================
// FONCTIONS POUR LES FRAIS
// ===================================

function updateFrais() {
    const frais = document.getElementById('fraisInput').value;
    
    // Envoi direct sans validation JS
    const formData = new FormData();
    formData.append('frais', frais);
    
    fetch('/achats/frais/update', {
        method: 'POST',
        body: formData
    })
    .then(() => {
        window.location.reload(); // Recharge la page
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur');
    });
}

// ===================================
// FONCTIONS POUR LES SÉLECTIONS
// ===================================

function loadDonDetails() {
    const select = document.getElementById('donSelect');
    if(!select || select.selectedIndex < 0) return;
    
    const option = select.options[select.selectedIndex];
    
    if(option && option.value) {
        const montant = option.dataset.montant;
        const donateur = option.dataset.donateur;
        
        // Mettre à jour le max de l'input montant
        const montantInput = document.getElementById('montantInput');
        if(montantInput) {
            montantInput.max = montant;
            montantInput.placeholder = `Max: ${formatMoney(montant)} Ar`;
        }
    }
}

function loadBesoinDetails() {
    const select = document.getElementById('besoinSelect');
    if(!select || select.selectedIndex < 0) return;
    
    const option = select.options[select.selectedIndex];
    
    if(option && option.value) {
        const article = option.dataset.article;
        const prix = option.dataset.prix;
        const quantite = option.dataset.quantite;
        const ville = option.dataset.ville;
    }
}

// ===================================
// FONCTIONS POUR LES ACHATS
// ===================================

function calculerAchat() {
    const idDon = document.getElementById('donSelect').value;
    const idBesoin = document.getElementById('besoinSelect').value;
    const montant = document.getElementById('montantInput').value;
    
    if(!idDon || !idBesoin || !montant) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    // Validation du montant
    const maxMontant = document.getElementById('montantInput').max;
    if(maxMontant && parseFloat(montant) > parseFloat(maxMontant)) {
        alert(`Le montant ne peut pas dépasser ${formatMoney(maxMontant)} Ar`);
        return;
    }
    
    const formData = new FormData();
    formData.append('idDon', idDon);
    formData.append('idBesoin', idBesoin);
    formData.append('montant', montant);
    
    fetch('/achats/calculer', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if(!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        if(data && data.success) {
            dernierCalcul = data.data;
            
            // Afficher les résultats
            document.getElementById('montantAffiche').textContent = formatMoney(data.data.montant_utilise);
            document.getElementById('quantiteAffiche').textContent = data.data.quantite_achetee;
            document.getElementById('fraisPourcentageAffiche').textContent = data.data.frais_pourcentage;
            document.getElementById('fraisMontantAffiche').textContent = formatMoney(data.data.frais_montant);
            document.getElementById('totalAffiche').textContent = formatMoney(data.data.montant_total);
            
            document.getElementById('calculResultat').style.display = 'block';
            
            // Scroller jusqu'au résultat
            document.getElementById('calculResultat').scrollIntoView({ behavior: 'smooth' });
        } else {
            alert(data?.message || 'Erreur de calcul');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de communication avec le serveur');
    });
}

function effectuerAchat() {
    if(!dernierCalcul) {
        alert('Veuillez d\'abord calculer l\'achat');
        return;
    }
    
    if(!confirm('Confirmer cet achat ?')) {
        return;
    }
    
    const idDon = document.getElementById('donSelect').value;
    const idBesoin = document.getElementById('besoinSelect').value;
    
    const formData = new FormData();
    formData.append('idDon', idDon);
    formData.append('idBesoin', idBesoin);
    formData.append('montant_utilise', dernierCalcul.montant_utilise);
    formData.append('frais_pourcentage', dernierCalcul.frais_pourcentage);
    formData.append('frais_montant', dernierCalcul.frais_montant);
    formData.append('montant_total', dernierCalcul.montant_total);
    
    fetch('/achats/effectuer', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if(response.redirected) {
            window.location.href = response.url;
        } else {
            // Si pas de redirection, recharger quand même
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'achat');
    });
}

function resetFormulaire() {
    document.getElementById('donSelect').value = '';
    document.getElementById('besoinSelect').value = '';
    document.getElementById('montantInput').value = '';
    document.getElementById('montantInput').max = '';
    document.getElementById('montantInput').placeholder = 'Ex: 500000';
    document.getElementById('calculResultat').style.display = 'none';
    dernierCalcul = null;
}

// ===================================
// FONCTIONS POUR L'HISTORIQUE
// ===================================

function loadHistorique() {
    fetch('/achats/liste')
        .then(response => {
            if(!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if(data && data.success) {
                updateHistoriqueTable(data.data);
                updateLastUpdate();
            } else {
                showError('Erreur de chargement des données');
            }
        })
        .catch(error => {
            console.error('Erreur chargement historique:', error);
            showError('Erreur de communication avec le serveur');
        });
}

// ===================================
// FONCTIONS POUR LE FILTRE PAR VILLE
// ===================================

function filterByVille(villeId) {
    // Afficher le chargement
    document.getElementById('historiqueTableBody').innerHTML = `
        <tr>
            <td colspan="6" style="text-align: center;">
                <div class="loading-spinner"></div> Chargement...
            </td>
        </tr>
    `;
    
    let url = '/achats/liste';
    if(villeId) {
        url = '/achats/ville/' + villeId;
    }
    
    fetch(url)
        .then(response => {
            if(!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if(data && data.success) {
                updateHistoriqueTable(data.data);
                updateLastUpdate();
            } else {
                showError('Erreur de chargement des données');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur de communication avec le serveur');
        });
}

function updateHistoriqueTable(achats) {
    const tbody = document.getElementById('historiqueTableBody');
    
    if(!tbody) return;
    
    if(!achats || achats.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aucun achat trouvé</td></tr>';
        return;
    }
    
    let html = '';
    achats.forEach(achat => {
        html += `<tr>
            <td>${formatDateTime(achat.date_achat)}</td>
            <td>${achat.ville_nom || 'Inconnue'}</td>
            <td class="montant-positif">${formatMoney(achat.montant_utilise)} Ar</td>
            <td>${formatMoney(achat.frais_montant)} Ar (${achat.frais_pourcentage}%)</td>
            <td><strong class="montant-positif">${formatMoney(achat.montant_total)} Ar</strong></td>
            <td>
                <a href="/achats/delete/${achat.id}" class="btn-icon-small delete" 
                   onclick="return confirm('Supprimer cet achat ?')"
                   title="Supprimer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                </a>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

// ===================================
// FONCTIONS UTILITAIRES
// ===================================

function formatMoney(amount) {
    if(!amount && amount !== 0) return '0';
    return new Intl.NumberFormat('fr-FR').format(amount);
}

function formatDateTime(dateString) {
    if(!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR') + ' ' + 
               date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
    } catch(e) {
        return dateString;
    }
}

function updateLastUpdate() {
    const now = new Date();
    const lastUpdate = document.getElementById('lastUpdate');
    if(lastUpdate) {
        lastUpdate.textContent = 'Dernière mise à jour: ' + 
            now.toLocaleDateString('fr-FR') + ' ' +
            now.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
    }
}

function showError(message) {
    const tbody = document.getElementById('historiqueTableBody');
    if(tbody) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #c44536;">${message}</td></tr>`;
    }
}

// ===================================
// STYLES POUR LE CHARGEMENT
// ===================================

// Ajouter les styles si pas déjà présents
if(!document.getElementById('achat-styles')) {
    const style = document.createElement('style');
    style.id = 'achat-styles';
    style.textContent = `
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f1ede8;
            border-top: 3px solid #c44536;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        .montant-positif {
            color: #4a8b6f;
            font-weight: 600;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}

// Exposer les fonctions globalement
window.updateFrais = updateFrais;
window.loadDonDetails = loadDonDetails;
window.loadBesoinDetails = loadBesoinDetails;
window.calculerAchat = calculerAchat;
window.effectuerAchat = effectuerAchat;
window.resetFormulaire = resetFormulaire;
window.filterByVille = filterByVille;