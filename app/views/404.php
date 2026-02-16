<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 - Page non trouvée</title>
    <link rel="stylesheet" href="/assets/css/style-404.css">
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>Désolé, la page que vous recherchez n’existe pas ou a été déplacée.</p>
        <a href="/" onclick="goBack(event)" class="btn">Retour à l’accueil</a>
    </div>

    <script>
        function goBack(event) {
            event.preventDefault();
            // utiliser l'historique du navigateur
            if (document.referrer && document.referrer !== '') {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        }
    </script>
</body>
</html>
