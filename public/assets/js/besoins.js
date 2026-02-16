const quantite = document.querySelector("#quantite");
const prixUnitaire = document.querySelector("#prixUnitaire");
const total = document.querySelector("#montantTotal");
const articleSelect = document.querySelector("#article");
const categorieSelect = document.querySelector("#categorie");

async function updateArticleOptions(){
    const idType = categorieSelect.value;
    
    if(!idType) {
        articleSelect.disabled = true;
        articleSelect.innerHTML = '<option value="">Sélectionner une catégorie d\'abord</option>';
        prixUnitaire.value = '';
        total.value = '';
        return;
    }
    
    try {
        const response = await fetch("/api/articles/by-type/" + idType);
        const articles = await response.json();
        
        articleSelect.innerHTML = '<option value="">Sélectionner un article</option>';
        
        articles.forEach(article => {
            const option = document.createElement('option');
            option.value = article.id;
            option.textContent = article.name;
            articleSelect.appendChild(option);
        });
        
        // Activer le select article
        articleSelect.disabled = false;
        
        // Réinitialiser les champs
        prixUnitaire.value = '';
        total.value = '';
    } catch(error) {
        console.error('Erreur lors du chargement des articles:', error);
        articleSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        articleSelect.disabled = true;
    }
}

async function updatePrixUnitaire(){
    const id = articleSelect.value;
    
    if(!id) {
        prixUnitaire.value = '';
        total.value = '';
        return;
    }
    
    try {
        const response = await fetch("/api/article/" + id);
        const data = await response.json();
        prixUnitaire.value = parseFloat(data.prix_unitaire);
        calculateTotal();
    } catch(error) {
        console.error('Erreur lors du chargement du prix:', error);
    }
}

function calculateTotal(){
    total.value = parseFloat(prixUnitaire.value) * parseFloat(quantite.value);
}

function filterBesoins(searchTerm) {
    const rows = document.querySelectorAll('#besoinsTableBody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

function filterByVille(ville) {
    const rows = document.querySelectorAll('#besoinsTableBody tr');
    
    rows.forEach(row => {
        if (!ville) {
            row.style.display = '';
        } else {
            const villeCell = row.querySelector('.city-tag');
            row.style.display = villeCell && villeCell.textContent === ville ? '' : 'none';
        }
    });
}

function resetForm() {
    document.getElementById('besoinForm').reset();
    document.getElementById('article').innerHTML = '<option value="">Sélectionner un article</option>';
    document.getElementById('prixUnitaire').value = '';
    document.getElementById('montantTotal').value = '';
}


articleSelect.addEventListener("change", updatePrixUnitaire);
quantite.addEventListener("input", calculateTotal);
categorieSelect.addEventListener("change", updateArticleOptions);
