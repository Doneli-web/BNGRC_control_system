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


articleSelect.addEventListener("change", updatePrixUnitaire);
quantite.addEventListener("input", calculateTotal);
