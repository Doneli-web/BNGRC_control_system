<?php
ini_set('display_errors', 1);
if(!isset($_SESSION["user"]) || $_SESSION["user"] == null){
    header("Location : /login-user");
    exit;
}

$title = isset($titlePage) ? $titlePage : "Takalotakalo-Page";
$content = isset($contentPage) || $contentPage !== null ? $contentPage : "404";
$contentPath = __DIR__ . '/' . $content . '.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Takalo-takalo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Ton CSS -->
    <link rel="stylesheet" href="/assets/css/user-model.css">
    <link rel="stylesheet" href="/assets/css/<?= $content ?>.css">
</head> 
<body>

<!-- Header fixe (titre + user seulement) -->
<header class="app-header">
    <div class="header-left">
        <i class="fas fa-exchange-alt logo-icon"></i>
        <div class="site-title">Takalo<span>takalo</span></div>
    </div>
    <div class="user-profile" id="userProfile">
        <div class="user-avatar"><?= substr($user["username"] ?? "U", 0, 1) ?></div>
        <div class="user-name"><?php echo $user["username"] ?? "Utilisateur"; ?></div>

        <div class="profile-dropdown" id="profileDropdown">
            <a href="/logout" class="dropdown-item">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </div>
</header>

<!-- Sidebar (navigation gauche) -->
<aside class="sidebar">
    <ul class="sidebar-nav">
        <li class="sidebar-item active">
            <i class="fas fa-box"></i>
            <span>Mes objets</span>
        </li>
        <li class="sidebar-item">
            <i class="fas fa-right-left"></i>
            <span>Faire un échange</span>
        </li>
        <li class="sidebar-item">
            <i class="fas fa-users"></i>
            <span>Objets des utilisateurs</span>
        </li>
    </ul>
</aside>

<!-- Contenu principal -->
<main class="main-content">
    <?php include $contentPath; ?>    
</main>

</body>
</html>
