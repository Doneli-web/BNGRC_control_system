document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments du formulaire

    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const googleLoginBtn = document.getElementById('googleLogin');
    const resetPasswordBtn = document.getElementById('resetPassword');
    const ssoLoginBtn = document.getElementById('ssoLogin');
    const signUpBtn = document.getElementById('signUp');
    const loginBtn = document.getElementById('loginBtn');
    
    // Fonctions de validation
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function validatePassword(password) {
        return password.length >= 6;
    }
    
    // Affichage des messages d'erreur
    function showError(inputElement, errorElement, message) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        inputElement.classList.remove('input-success');
        inputElement.classList.add('input-error');
    }
    
    function hideError(inputElement, errorElement) {
        errorElement.style.display = 'none';
        inputElement.classList.remove('input-error');
        inputElement.classList.add('input-success');
    }

    async function submit(){
        const fd = new FormData(loginForm);
        const response = await fetch("/admin-verification", {
            body: fd,
            method: "POST"
        });
        return response.json();
    }
    
    // Validation en temps réel
    emailInput.addEventListener('input', function() {
        if (validateEmail(emailInput.value)) {
            hideError(emailInput, emailError);
        } else {
            showError(emailInput, emailError, 'Veuillez entrer une adresse email valide');
        }
    });
    
    passwordInput.addEventListener('input', function() {
        if (validatePassword(passwordInput.value)) {
            hideError(passwordInput, passwordError);
        } else {
            showError(passwordInput, passwordError, 'Le mot de passe doit contenir au moins 6 caractères');
        }
    });
    
    // Validation à la soumission du formulaire
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Validation de l'email
        if (!validateEmail(emailInput.value)) {
            showError(emailInput, emailError, 'Veuillez entrer une adresse email valide');
            isValid = false;
        } else {
            hideError(emailInput, emailError);
        }
        
        // Validation du mot de passe
        if (!validatePassword(passwordInput.value)) {
            showError(passwordInput, passwordError, 'Le mot de passe doit contenir au moins 6 caractères');
            isValid = false;
        } else {
            hideError(passwordInput, passwordError);
        }
        
        // Si tout est valide, simuler la connexion
        if (isValid) {
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion en cours...';
            loginBtn.disabled = true;
            const result = await submit();
            loginBtn.innerHTML = "Se connecter"
            loginBtn.disabled = false;
            if(result.success){
                loginForm.reset();
                window.location.href = "/admin-home";
            }else{
                alert("Erreur lors de l'authentification\n Mot de passe ou identifiant invalide");
            }
        }
    });
    
    // Gestion des boutons supplémentaires
    googleLoginBtn.addEventListener('click', function() {
        alert('Fonctionnalité "Continuer avec Google" - Cela nécessiterait une intégration avec Google OAuth en production.');
    });
    
    resetPasswordBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const email = prompt('Veuillez entrer votre adresse email pour réinitialiser votre mot de passe:');
        if (email && validateEmail(email)) {
            alert(`Un lien de réinitialisation a été envoyé à ${email} (simulation)`);
        } else if (email) {
            alert('Adresse email invalide');
        }
    });
    
    ssoLoginBtn.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Connexion SSO - Cela nécessiterait une intégration avec un fournisseur d\'identité en production.');
    });
    
    signUpBtn.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Création de compte - Redirection vers la page d\'inscription (simulation).');
    });
    
    // Animation subtile au chargement
    const loginContainer = document.querySelector('.login-container');
    loginContainer.style.opacity = '0';
    loginContainer.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        loginContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        loginContainer.style.opacity = '1';
        loginContainer.style.transform = 'translateY(0)';
    }, 100);
});