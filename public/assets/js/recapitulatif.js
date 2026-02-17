// recapitulatif.js
// AJAX refresh for recapitulatif.php
(function(){
    const btnRefresh = document.getElementById('btnRefresh');
    const lastUpdateEl = document.getElementById('lastUpdate');
    const montantTotalEl = document.getElementById('montantTotal');
    const montantSatisfaitEl = document.getElementById('montantSatisfait');
    const montantRestantEl = document.getElementById('montantRestant');
    const progressBar = document.getElementById('progressBar');
    const tauxSatisfactionEl = document.getElementById('tauxSatisfaction');
    const autoToggle = document.getElementById('autoRefreshToggle');

    let autoRefresh = false;
    let autoInterval = null;

    function formatMoney(v){
        return Number(v).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' Ar';
    }

    function setLoading(isLoading){
        if(btnRefresh){
            if(isLoading){
                btnRefresh.classList.add('loading');
                btnRefresh.disabled = true;
            } else {
                btnRefresh.classList.remove('loading');
                btnRefresh.disabled = false;
            }
        }
    }

    async function fetchRecap(){
        setLoading(true);
        try{
            const res = await fetch('/api/recap', {cache: 'no-store'});
            if(!res.ok) throw new Error('Network response not ok');
            const data = await res.json();

            // Expecting: { totalBesoin: number, totalSatisfait: number }
            const total = parseFloat(data.totalBesoin || 0);
            const satisfied = parseFloat(data.totalSatisfait || 0);
            const remaining = Math.max(0, total - satisfied);

            if(montantTotalEl) montantTotalEl.textContent = formatMoney(total);
            if(montantSatisfaitEl) montantSatisfaitEl.textContent = formatMoney(satisfied);
            if(montantRestantEl) montantRestantEl.textContent = formatMoney(remaining);

            // Progress bar
            const pct = total > 0 ? Math.min(100, Math.round((satisfied / total) * 100)) : 0;
            if(progressBar) progressBar.style.width = pct + '%';
            if(tauxSatisfactionEl) tauxSatisfactionEl.textContent = pct + '%';

            const now = new Date();
            if(lastUpdateEl) lastUpdateEl.textContent = 'Dernière mise à jour: ' + now.toLocaleString('fr-FR');

            return data;
        }catch(err){
            console.error('Recap fetch error', err);
            return null;
        } finally{
            setLoading(false);
        }
    }

    function toggleAutoRefresh(){
        autoRefresh = !autoRefresh;
        if(autoToggle){
            autoToggle.classList.toggle('active', autoRefresh);
        }
        if(autoRefresh){
            autoInterval = setInterval(fetchRecap, 30000);
        } else {
            if(autoInterval) clearInterval(autoInterval);
            autoInterval = null;
        }
    }

    // Bind events
    if(btnRefresh) btnRefresh.addEventListener('click', fetchRecap);
    if(autoToggle) autoToggle.addEventListener('click', toggleAutoRefresh);

    // Parse money string from element like "1 234.56 Ar"
    function parseMoneyFromElement(el){
        if(!el) return 0;
        const txt = (el.textContent || '').replace(/\s|Ar/g, '').trim();
        if(txt === '') return 0;
        // Ensure dot decimal
        const n = parseFloat(txt);
        return isNaN(n) ? 0 : n;
    }

    function initializeFromServerRendered(){
        const total = parseMoneyFromElement(montantTotalEl);
        const satisfied = parseMoneyFromElement(montantSatisfaitEl);
        const remaining = parseMoneyFromElement(montantRestantEl) || Math.max(0, total - satisfied);

        // Update formatted texts (normalize)
        if(montantTotalEl) montantTotalEl.textContent = formatMoney(total);
        if(montantSatisfaitEl) montantSatisfaitEl.textContent = formatMoney(satisfied);
        if(montantRestantEl) montantRestantEl.textContent = formatMoney(remaining);

        const pct = total > 0 ? Math.min(100, Math.round((satisfied / total) * 100)) : 0;
        if(progressBar) progressBar.style.width = pct + '%';
        if(tauxSatisfactionEl) tauxSatisfactionEl.textContent = pct + '%';
    }

    // Expose functions for inline onclick attributes in the template
    window.refreshData = fetchRecap;
    window.toggleAutoRefresh = toggleAutoRefresh;

    // Initial load: use server-rendered PHP values first
    document.addEventListener('DOMContentLoaded', function(){
        initializeFromServerRendered();
    });
})();
function displaySummary(calc) {
    // Montants
    document.getElementById('montantTotal').textContent = formatMoney(calc.total) + ' Ar';
    document.getElementById('montantSatisfait').textContent = formatMoney(calc.satisfait) + ' Ar';
    document.getElementById('montantRestant').textContent = formatMoney(calc.restant) + ' Ar';
    
    // Détails
    const nbBesoins = Object.values(calc.parVille).length;
    document.getElementById('detailTotal').textContent = `${nbBesoins} ville(s) concernée(s)`;
    
    const tauxSatisfaction = calc.total > 0 ? Math.round((calc.satisfait / calc.total) * 100) : 0;
    document.getElementById('detailSatisfait').textContent = `${tauxSatisfaction}% du total`;
    document.getElementById('detailRestant').textContent = 
        calc.restant > 0 ? 'À compléter par dons ou achats' : 'Tous les besoins sont couverts !';
    
    // Progress bar
    document.getElementById('tauxSatisfaction').textContent = tauxSatisfaction + '%';
    const progressBar = document.getElementById('progressBar');
    progressBar.style.width = tauxSatisfaction + '%';
    if (tauxSatisfaction === 100) {
        progressBar.classList.add('complete');
    } else {
        progressBar.classList.remove('complete');
    }
    
    // Animation
    animateValue(document.getElementById('montantTotal'), 0, calc.total);
    animateValue(document.getElementById('montantSatisfait'), 0, calc.satisfait);
    animateValue(document.getElementById('montantRestant'), 0, calc.restant);
}

function showError(message) {
    alert(message);
}

// // Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
