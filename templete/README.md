# Système de Gestion des Dons - BNGRC

## 📋 Description du Projet

Application web complète pour le Bureau National de Gestion des Risques et des Catastrophes (BNGRC) permettant de gérer la collecte et la distribution des dons aux sinistrés par ville.

**Projet Final S3 - Février 2026**

## ✨ Fonctionnalités Principales

### 1. Tableau de bord (index.html)
- Vue d'ensemble des villes et de leurs besoins
- Statistiques en temps réel (nombre de villes, dons reçus, taux d'attribution)
- Analyse détaillée par catégorie (En nature, Matériaux, Argent)
- Filtrage et tri dynamiques
- Export des données en CSV

### 2. Gestion des Besoins (besoins.html)
- Saisie des besoins par ville
- Catégorisation: En nature, Matériaux, Argent
- Articles prédéfinis avec prix unitaires fixes
- Niveaux de priorité (Urgent, Important, Normal)
- Calcul automatique des montants
- Liste complète avec filtres et recherche
- Édition et suppression des besoins

### 3. Gestion des Dons (dons.html)
- Enregistrement des dons reçus
- Informations sur les donateurs (nom, type, contact)
- Catégories et articles correspondant aux besoins
- Suivi du statut (En attente, Distribué, Partiellement distribué)
- Statistiques des dons
- Filtres multiples (catégorie, statut, recherche)

### 4. Simulation de Distribution (simulation.html)
- Algorithme de dispatch automatique
- Distribution par ordre de date de réception
- Matching intelligent entre dons et besoins
- Visualisation en 5 étapes
- Console de logs en temps réel
- Résultats détaillés par ville
- Statistiques globales
- Export des résultats

### 5. Gestion des Villes (villes.html)
- Liste des villes concernées
- Informations détaillées sur chaque ville
- Vue synthétique des besoins par ville
- Statistiques par localité

## 🎨 Design & Interface

### Caractéristiques du Design
- **Palette de couleurs**: Chaude et professionnelle (rouge terre cuite, vert forêt, ocre)
- **Typographie**: 
  - Titres: Crimson Pro (serif élégant)
  - Corps: Work Sans (sans-serif moderne)
- **Animations**: Transitions fluides et micro-interactions
- **Responsive**: Adapté à tous les écrans (desktop, tablette, mobile)
- **Accessibilité**: Contrastes respectés, navigation claire

### Composants Visuels
- Cartes avec effets hover
- Barres de progression animées
- Badges de statut colorés
- Tableaux interactifs
- Formulaires stylisés avec validation visuelle
- Notifications toast
- Modales d'information

## 🗂️ Structure des Fichiers

```
bngrc-gestion-dons/
├── index.html              # Tableau de bord principal
├── besoins.html            # Gestion des besoins
├── dons.html               # Gestion des dons
├── simulation.html         # Simulation de distribution
├── villes.html             # Gestion des villes
├── styles.css              # Styles CSS unifiés
├── script.js               # JavaScript du tableau de bord
├── besoins.js              # JavaScript des besoins
├── dons.js                 # JavaScript des dons
├── simulation.js           # JavaScript de la simulation
└── README.md               # Ce fichier
```

## 🚀 Installation et Utilisation

### Prérequis
- Un navigateur web moderne (Chrome, Firefox, Safari, Edge)
- Pas de serveur requis (fonctionne en local)

### Installation
1. Téléchargez tous les fichiers du projet
2. Placez-les dans un même dossier
3. Ouvrez `index.html` dans votre navigateur

### Utilisation
1. **Enregistrer les besoins**:
   - Allez dans "Besoins"
   - Remplissez le formulaire (ville, catégorie, article, quantité)
   - Le prix unitaire est automatique
   - Cliquez sur "Enregistrer le besoin"

2. **Enregistrer les dons**:
   - Allez dans "Dons"
   - Indiquez le donateur et le type de don
   - Sélectionnez la catégorie et l'article
   - Entrez la quantité et la date de réception
   - Cliquez sur "Enregistrer le don"

3. **Lancer une simulation**:
   - Allez dans "Simulation"
   - Cliquez sur "Lancer la simulation"
   - Observez le processus en 5 étapes
   - Consultez les résultats et exportez-les

4. **Consulter le tableau de bord**:
   - Vue d'ensemble sur la page d'accueil
   - Filtrez par type ou statut
   - Exportez les données si nécessaire

## 💾 Stockage des Données

Les données sont stockées localement dans le navigateur via **localStorage**:
- `besoins`: Liste de tous les besoins enregistrés
- `dons`: Liste de tous les dons reçus

**Note**: Les données sont persistantes tant que le localStorage n'est pas vidé.

## 📊 Règles de Gestion

### Prix Unitaires Fixes
Chaque article a un prix unitaire qui ne change jamais:

**En nature:**
- Riz: 5,000 Ar/kg
- Huile: 12,000 Ar/L
- Eau potable: 2,000 Ar/L
- Sucre: 4,500 Ar/kg
- Sel: 1,500 Ar/kg
- Haricots: 6,000 Ar/kg
- Farine: 4,000 Ar/kg

**Matériaux:**
- Tôles: 25,000 Ar/unité
- Clous: 15,000 Ar/kg
- Bâches: 8,000 Ar/m²
- Planches: 12,000 Ar/unité
- Ciment: 35,000 Ar/sac
- Sable: 45,000 Ar/m³

**Argent:**
- Don financier: montant libre

### Algorithme de Distribution
1. Les dons sont triés par ordre de date de réception
2. Pour chaque don, le système recherche les besoins correspondants
3. La distribution se fait en priorité par:
   - Correspondance article/catégorie
   - Disponibilité du besoin
   - Ordre chronologique des besoins
4. Si un don ne peut être complètement distribué, le reste est signalé

## 🛠️ Technologies Utilisées

- **HTML5**: Structure sémantique
- **CSS3**: 
  - Variables CSS personnalisées
  - Flexbox & Grid
  - Animations et transitions
  - Design responsive
- **JavaScript ES6+**:
  - Manipulation du DOM
  - LocalStorage API
  - Fonctions asynchrones
  - Gestion d'événements

## 📱 Compatibilité

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Opera 76+

## 🔮 Évolutions Possibles

### Phase 2 - Backend & Base de Données
- [ ] Connexion à une base de données (MySQL/PostgreSQL)
- [ ] API REST pour les opérations CRUD
- [ ] Authentification des utilisateurs
- [ ] Gestion des rôles (admin, opérateur, lecteur)

### Phase 3 - Fonctionnalités Avancées
- [ ] Génération de rapports PDF
- [ ] Notifications par email/SMS
- [ ] Tracking GPS des livraisons
- [ ] Intégration de cartes interactives
- [ ] Historique détaillé des actions
- [ ] Dashboard analytique avec graphiques

### Phase 4 - Mobile
- [ ] Application mobile (React Native/Flutter)
- [ ] Mode hors-ligne avec synchronisation
- [ ] Scan de codes-barres pour les stocks
- [ ] Photos des distributions

### Phase 5 - Intelligence Artificielle
- [ ] Prédiction des besoins futurs
- [ ] Optimisation de la distribution
- [ ] Détection d'anomalies
- [ ] Recommandations automatiques

## 👥 Équipe de Développement

**Projet S3 - ITU**
- Groupe de 3 étudiants
- Durée: 26 heures
- Début: 16 février 2026, 13h
- Fin: 17 février 2026, 16h

## 📄 Licence

Projet académique - ITU 2026

## 🆘 Support

Pour toute question ou problème:
1. Consultez ce README
2. Vérifiez la console du navigateur (F12)
3. Vérifiez que le localStorage est activé
4. Contactez l'équipe de développement

## 🎯 Objectifs Pédagogiques Atteints

✅ Conception d'interface utilisateur moderne et professionnelle
✅ Manipulation avancée du DOM avec JavaScript
✅ Gestion de données avec localStorage
✅ Algorithmes de traitement et dispatch
✅ Design responsive et accessible
✅ Animations et interactions fluides
✅ Architecture modulaire et maintenable
✅ Documentation complète

---

**Développé avec ❤️ pour le BNGRC et les sinistrés de Madagascar**
