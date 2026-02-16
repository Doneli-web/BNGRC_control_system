<?php
session_start();
if(!isset($_SESSION["admin"]) || $_SESSION["admin"] == null){
    header("Location : /");
    exit;
}

$title = isset($titlePage) ? $titlePage : "Takalotakalo-Page";
$content = isset($contentPage) || $contentPage !== null ? $contentPage : "404";
$contentPath = __DIR__ . '/' . $content . '.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if($content == "404") { ?>
        <link rel="stylesheet" href="/assets/css/style-404.css">
    <?php }else{ ?>
        <link rel="stylesheet" href="/assets/css/adminModel.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php } ?>
    <?php if($content == "admin-objects") { ?>
        <link rel="stylesheet" href="/assets/css/object.css">
        <script src="/assets/js/object.js" defer></script>
        <!-- <script src="/assets/js/admin-objects.js" defer></script> -->
    <?php } else if($content == "admin-categories") { ?>
        <link rel="stylesheet" href="/assets/css/admin-categories.css">
        <script src="/assets/js/admin-categories.js" defer></script>
    <?php } ?>

    <title>TakaloAdmin-<?= $title ?></title>
</head>
<body>
<header class="header">
        <div class="top-bar">
            <div class="logo">
                <i class="fas fa-exchange-alt"></i>
                <h1>Takalo<span>takalo</span> Admin</h1>
            </div>
            
            <div class="user-menu">
                <div class="notification">
                    <i class="far fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">AD</div>
                    <div class="user-name">Admin User</div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="/logout" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Déconnexion
                        </a>
                    </div>
                </div>

            </div>
        </div>
        
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item active" data-page="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Tableau de bord</span>
                </li>
                <li class="nav-item" data-page="categories">
                    <i class="fas fa-list"></i>
                    <span>Catégories</span>
                </li>
                <li class="nav-item" data-page="objects">
                    <i class="fas fa-box"></i>
                    <span>Objets</span>
                </li>
                <li class="nav-item" data-page="transactions">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transactions</span>
                </li>
                <li class="nav-item" data-page="users">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </li>
                <li class="nav-item" data-page="reports">
                    <i class="fas fa-chart-bar"></i>
                    <span>Rapports</span>
                </li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <?php include $contentPath; ?>
    </main>
    <footer class="footer">
        <p>© 2023 Takalotakalo Admin. Plateforme d'échange et de vente. Tous droits réservés.</p>
        <p style="margin-top: 10px; font-size: 13px;">
            <a href="#" style="color: var(--primary-color); margin: 0 10px;">Confidentialité</a> | 
            <a href="#" style="color: var(--primary-color); margin: 0 10px;">Conditions d'utilisation</a> | 
            <a href="#" style="color: var(--primary-color); margin: 0 10px;">Support</a>
        </p>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation
            const navItems = document.querySelectorAll('.nav-item');
            const addObjectBtn = document.getElementById('addObjectBtn');
            
            // Gestion de la navigation active
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Retirer la classe active de tous les items
                    navItems.forEach(navItem => {
                        navItem.classList.remove('active');
                    });
                    
                    // Ajouter la classe active à l'item cliqué
                    this.classList.add('active');
                    
                    // Ici, dans une application réelle, on chargerait le contenu de la page
                    const page = this.getAttribute('data-page');
                    console.log(`Chargement de la page: ${page}`);
                    
                    // Pour cette démo, afficher une alerte
                    if (page !== 'dashboard') {
                        window.location.href = `/admin-${page}`;
                        // alert(`Navigation vers: ${this.querySelector('span').textContent} (Fonctionnalité de navigation à implémenter)`);
                    }else{
                        window.location.href = "admin-home";
                    }
                });
            });
            
            // // Bouton "Ajouter un objet"
            // addObjectBtn.addEventListener('click', function() {
            //     alert("Fonctionnalité d'ajout d'objet - À implémenter avec un formulaire");
            // });
            
            // Gestion des boutons d'action
            const actionButtons = document.querySelectorAll('.icon-btn');
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i').className;
                    
                    if (icon.includes('fa-eye')) {
                        alert("Visualisation de l'objet - À implémenter");
                    } else if (icon.includes('fa-edit')) {
                        alert("Modification - À implémenter");
                    } else if (icon.includes('fa-check')) {
                        alert("Validation de l'objet - À implémenter");
                    } else if (icon.includes('fa-history')) {
                        alert("Historique des transactions - À implémenter");
                    }
                });
            });
            
            // Notification
            const notificationBtn = document.querySelector('.notification');
            notificationBtn.addEventListener('click', function() {
                alert("Vous avez 3 notifications non lues\n• Nouvelle demande d'échange\n• 2 nouveaux objets en attente\n• Message d'un utilisateur");
            });
            
            // Animation d'entrée subtile
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
            
            // Animation pour les sections
            const sections = document.querySelectorAll('.section');
            setTimeout(() => {
                sections.forEach((section, index) => {
                    section.style.opacity = '0';
                    section.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        section.style.opacity = '1';
                        section.style.transform = 'translateY(0)';
                    }, 300 + (100 * index));
                });
            }, 500);
            // Menu profil (déconnexion)
            const userProfile = document.getElementById('userProfile');
            const profileDropdown = document.getElementById('profileDropdown');

            userProfile.addEventListener('click', function (e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            // Fermer le menu si on clique ailleurs
            document.addEventListener('click', function () {
                profileDropdown.classList.remove('show');
            });

        });
    </script>
</body>
</html>