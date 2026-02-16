<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takalotakalo Admin - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/adminLogin.css">
    <script src="/assets/js/validation-signUp.js" defer></script>
</head>
<body>
    <div class="login-container">
        <div class="left-panel">
            <div class="logo">
                <i class="fas fa-exchange-alt"></i>
                <h1>Takalo-<span>takalo</span> Utilisateur</h1>
            </div>
            
            <div class="welcome-text">
                <h2>Bienvenue sur la plateforme d'echange</h2>
                <p>Gérez vos échanges et ventes en toute simplicité.</p>
            </div>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <p>Plateforme sécurisée pour vos transactions</p>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <p>Analytiques détaillées de vos échanges</p>
                </div>
                <div class="feature">
                    <i class="fas fa-users"></i>
                    <p>Gestion simplifiée des utilisateurs</p>
                </div>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="login-header">
                <h2>Connexion à votre compte</h2>
                <p>Entrez vos identifiants pour vous inscrire et obtenir un espace personnel d'echange</p>
            </div>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Votre nom d'utilisateur">
                    </div>
                    <div class="error-message" id="usernameError">L'utilisateur doit au moin contenir 6 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email"  class="form-control" placeholder="votre@email.com">
                    </div>
                    <div class="error-message" id="emailError">Veuillez entrer une adresse email valide</div>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Votre mot de passe">
                    </div>
                    <div class="error-message" id="passwordError">Le mot de passe doit contenir au moins 6 caractères</div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="loginBtn">Se connecter</button>
                
                <div class="login-links">
                    <a href="/" id="admin">Se connecter en tant qu'admin</a>
                    <a href="/login-user" id="signin">Vous avez deja un compte ?</a>
                </div>
            </form>
            
            <div class="form-footer">
                <p>En vous connectant, vous acceptez nos <a href="#">Conditions d'utilisation</a> et notre <a href="#">Politique de confidentialité</a></p>
            </div>
        </div>
    </div>
</body>
</html>