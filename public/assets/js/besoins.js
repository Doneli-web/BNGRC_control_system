const quantite = document.querySelector("#quantite");
const prixUnitaire = document.querySelector("#prixUnitaire");
const total = document.querySelector("#montantTotal");
const articleSelect = document.querySelector("#article");

async function updatePrixUnitaire(){
    const id = articleSelect.value;
    const response = await fetch("/api/article/" + id);
    const data = await response.json();
    prixUnitaire.value = parseFloat(data.prix_unitaire);
    calculateTotal();
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
