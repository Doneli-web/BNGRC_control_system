===============================================================================
=                                     TODO V2                             =
===============================================================================

BASE:
    - Creation de la base BNGRC (OK) 20s
    
    - Table BNGRC_region(id, name) (OK) 30s
    
    - Table BNGRC_ville(id, name, idRegion) (OK) 30s
    
    - Table BNGRC_typeDon(id, name) (OK) 30s
    
    - Table BNGRC_article(id, name, idType, prix_unitaire) (OK) 30s
    
    - Table BNGRC_besoin(id, idVille, idArticle, quantite, date_de_saisie) (OK) 30s
    
    - Table BNGRC_don(id, idArticle, quantite, date_de_saisie, donateur, type_donateur, statut) (OK) 1min
    
    - Table BNGRC_achat(id, idDon, idBesoin, montant_utilise, frais_pourcentage, frais_montant, montant_total, date_achat) 2min
    
    - Table BNGRC_config(id, cle, valeur, description)  1min
    
    - Table BNGRC_dispatch(id, idDon, idBesoin, quantite_attribuee, date_dispatch, statut) (OK) 2min
    
    - Insertion configuration initiale (frais_achat = 10) (OK) 30s
    
    - Insertion donnees de test pour regions, villes, types, articles, besoins, dons (OK) 3min

===============================================================================
=                            MODULE 1 - ACHATS                                 =
=                            Responsable: BRANDON                              =
===============================================================================

DESCRIPTION:
    - Achat via les dons en argent des besoins en nature et materiaux
    - Frais d'achat configurable (x%)
    - Page des besoins restants pour faire les achats
    - Message d'erreur si don insuffisant
    - Liste des achats filtrable par ville



1. MODELS:
    ----------
    Fichier: app/models/AchatModel.php
        - class AchatModel
        - __construct($db) (OK)
        - add($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total) (OK)
        - getAll() (OK)
        - getById($id) (OK)
        - findAchatsByDon($idDon) (OK)
        - findAchatsByBesoin($idBesoin) (OK)
        - getTotalAchatsParVille($idVille) (OK)
        - getAchatsFiltresParVille($idVille) (OK)
    
    Fichier: app/models/ConfigModel.php
        - class ConfigModel
        - __construct($db)
        - getConfig($cle)
        - updateConfig($cle, $valeur)
        - getFraisAchat()

2. CONTROLLERS:
    ----------
    Fichier: app/controllers/AchatController.php
        - class AchatController
        - getDonsArgentRestants()
        - getBesoinsRestantsNatureMateriaux()
        - calculerAchat()
        - validerAchat()
        - getHistoriqueAchats()
        - getTotalAchatsParVille($idVille)
        - getAchatsParVille($idVille)
    
    Fichier: app/controllers/ConfigController.php
        - class ConfigController
        - getFraisAchat()
        - updateFraisAchat()

3. ROUTES:
    ----------
    Fichier: app/routes/achat_routes.php
        - GET /api/achats/dons-argent
        - GET /api/achats/besoins-restants
        - POST /api/achats/calculer
        - POST /api/achats/effectuer
        - GET /api/achats/historique
        - GET /api/achats/par-ville/:id
        - GET /api/achats/filtre-ville/:id
        - GET /api/config/frais
        - POST /api/config/frais

4. FRONTEND :
    ----------
    Fichier: app/views/achats.php
        - Structure HTML de base avec navigation
        - Carte configuration frais avec input et bouton
        - Carte formulaire achat
        - Select pour les dons en argent
        - Select pour les besoins restants
        - Input pour montant a utiliser
        - Zone d'affichage du calcul (montant, frais, total)
        - Bouton Calculer
        - Bouton Effectuer achat
        - Zone message d'erreur/succes
        - Filtre par ville (select deroulant)
        - Tableau historique des achats
        - Horodatage derniere mise a jour

5. FRONTEND - JAVASCRIPT:
    ----------
    Fichier: public/assets/js/achats.js
        - Variables globales (fraisActuel, donSelectionne, besoinSelectionne, dernierCalcul, villesList)
        - Initialisation au chargement (loadFrais, loadVilles, loadDonsArgent, loadBesoinsRestants, loadHistorique)
        - loadVilles() - appel API GET /api/villes
        - loadFrais() - appel API GET /api/config/frais
        - updateFrais() - appel API POST /api/config/frais
        - loadDonsArgent() - appel API GET /api/achats/dons-argent
        - loadBesoinsRestants() - appel API GET /api/achats/besoins-restants
        - loadDonDetails() - chargement details don
        - loadBesoinDetails() - chargement details besoin
        - calculerAchat() - appel API POST /api/achats/calculer
        - effectuerAchat() - appel API POST /api/achats/effectuer
        - loadHistorique() - appel API GET /api/achats/historique
        - filterByVille() - appel API GET /api/achats/filtre-ville/:id
        - resetForm() - reinitialisation formulaire
        - showMessage() - affichage message
        - refreshData() - rechargement des donnees

6. DESIGN:
    ----------
    - Design BNGRC existant utilise
    - Ajout du lien Achats dans la navigation
    - Style des alertes pour messages succes/erreur
    - Mise en forme du resultat de calcul
    - Style du filtre par ville
    - Responsive design

===============================================================================
=                            MODULE 2 - SIMULATION                            =
=                            Responsable: DONELI                               =
===============================================================================

DESCRIPTION:
    - Page de simulation avec bouton simuler
    - Affichage du resultat de simulation
    - Bouton validation pour dispatcher vraiment les dons
    



1. MODELS:
    ----------
    Fichier: app/models/DispatchModel.php
        - class DispatchModel
        - __construct($db)
        - addSimulation($idDon, $idBesoin, $quantite)
        - clearSimulations()
        - validateSimulations()
        - findSimulations()
        - findValides()
        - getResultatsSimulation()
        - getResultatsParVille()
    
    Fichier: app/models/DonModel.php (ajouts)
        - getDonsDisponiblesTriesParDate()
        - updateQuantiteDon($idDon, $nouvelleQuantite)
    
    Fichier: app/models/BesoinModel.php (ajouts)
        - getBesoinsNonSatisfaitsTriesParDate()
        - updateQuantiteBesoin($idBesoin, $nouvelleQuantite)

2. CONTROLLERS:
    ----------
    Fichier: app/controllers/SimulationController.php
        - class SimulationController
        - getDonsDisponibles()
        - getBesoinsNonSatisfaits()
        - simulerDispatch()
        - getResultatsSimulation()
        - validerDispatch()
        - annulerSimulation()
        - getHistoriqueDispatch()

3. ROUTES:
    ----------
    Fichier: app/routes/simulation_routes.php
        - GET /api/simulation/dons
        - GET /api/simulation/besoins
        - POST /api/simulation/lancer
        - GET /api/simulation/resultats
        - POST /api/simulation/valider
        - POST /api/simulation/annuler
        - GET /api/simulation/historique

4. FRONTEND :
    ----------
    Fichier: app/views/simulation.php
        - Structure HTML de base avec navigation
        - Resume des dons disponibles (carte)
        - Resume des besoins restants (carte)
        - Resume des regles (FIFO - premier arrive, premier servi)
        - Bouton Lancer simulation
        - Barre de progression pendant simulation
        - Zone d'affichage des resultats
        - Tableau des resultats par ville
        - Tableau des resultats par article
        - Bouton Valider (actif apres simulation)
        - Bouton Annuler (actif apres simulation)
        - Bouton Reinitialiser

5. FRONTEND - JAVASCRIPT:
    ----------
    Fichier: public/assets/js/simulation.js
        - Variables globales (resultatsSimulation, simulationEnCours)
        - Initialisation au chargement (loadDonnees)
        - loadDonnees() - appel API GET /api/simulation/dons et /api/simulation/besoins
        - lancerSimulation() - appel API POST /api/simulation/lancer
        - afficherResultats() - traitement et affichage des resultats
        - validerDispatch() - appel API POST /api/simulation/valider
        - annulerSimulation() - appel API POST /api/simulation/annuler
        - reinitialiser() - reset interface
        - updateProgressBar() - mise a jour barre progression
        - showMessage() - affichage message
        - formatMoney() - formatage montant

6. DESIGN:
    ----------
    - Design BNGRC existant utilise
    - Ajout du lien Simulation dans la navigation
    - Style des cartes de resultats
    - Animation de progression
    - Style des tableaux de resultats
    - Badges pour statuts (simulation, valide)

===============================================================================
=                         MODULE 3 - RECAPITULATION                           =
=                         Responsable: ITOKIANA                                =
===============================================================================

DESCRIPTION:
    - Page de recapitulation avec bouton actualiser en Ajax
    - Affichage des besoins totaux en montant
    - Affichage des besoins satisfaits en montant
    - Affichage des besoins restants en montant
    - Donnees dynamiques avec Ajax


1. MODELS:
    ----------
    Fichier: app/models/RecapModel.php
        - class RecapModel
        - __construct($db)
        - getBesoinsTotaux()
        - getBesoinsSatisfaits()
        - getBesoinsRestants()
        - getStatsParVille()
        - getStatsParType()
        - getEvolutionTemporelle()
        - getTauxSatisfactionGlobal()
        - getTopVillesUrgentes()

2. CONTROLLERS:
    ----------
    Fichier: app/controllers/RecapController.php
        - class RecapController
        - getRecapGlobal()
        - getRecapParVille()
        - getRecapParType()
        - getRecapComplet()
        - getEvolution()
        - getTopUrgences()

3. ROUTES:
    ----------
    Fichier: app/routes/recap_routes.php
        - GET /api/recap/global
        - GET /api/recap/par-ville
        - GET /api/recap/par-type
        - GET /api/recap/complet
        - GET /api/recap/evolution
        - GET /api/recap/urgences

4. FRONTEND :
    ----------
    Fichier: app/views/recap.php
        - Structure HTML de base avec navigation
        - En-tete avec titre et bouton actualiser
        - Horodatage derniere mise a jour
        - Indicateur de chargement
        - Cartes statistiques globales (4 cartes)
            * Carte 1: Besoins totaux (montant)
            * Carte 2: Besoins satisfaits (montant)
            * Carte 3: Besoins restants (montant)
            * Carte 4: Taux de satisfaction (%)
        - Section graphiques (Chart.js)
            * Graphique repartition par type
            * Graphique evolution temporelle
        - Tableau recap par ville
            * Colonnes: Ville, Besoins totaux, Satisfaits, Restants, Taux
        - Tableau recap par type
            * Colonnes: Type, Besoins totaux, Satisfaits, Restants, Taux
        - Section alertes (villes urgentes)

5. FRONTEND - JAVASCRIPT:
    ----------
    Fichier: public/assets/js/recap.js
        - Variables globales (data, chartInstances)
        - Initialisation au chargement (loadData, initCharts)
        - loadData() - appel API GET /api/recap/complet
        - updateAffichage() - mise a jour de tous les affichages
        - updateCards() - mise a jour des cartes stats
        - updateTableauVilles() - mise a jour tableau villes
        - updateTableauTypes() - mise a jour tableau types
        - updateCharts() - mise a jour graphiques
        - updateHorodatage() - mise a jour date/heure
        - showLoading() - afficher indicateur chargement
        - hideLoading() - cacher indicateur chargement
        - refreshData() - fonction appelee par bouton actualiser
        - setupAutoRefresh() - configuration refresh automatique (optionnel)
        - formatMoney() - formatage montant
        - showMessage() - affichage message

6. GRAPHIQUES (Chart.js):
    ----------
    - Graphique 1: Repartition par type (camembert/barres)
        - Nature, Materiaux, Argent
    - Graphique 2: Evolution temporelle (ligne)
        - Par mois/semaine
    - Graphique 3: Top villes (barres)
        - Villes avec plus grands besoins

7. DESIGN:
    ----------
    - Design BNGRC existant utilise
    - Ajout du lien Recapitulatif dans la navigation
    - Style des cartes statistiques
    - Integration Chart.js
    - Style du bouton actualiser avec icone
    - Animation de chargement
    - Responsive design pour graphiques

