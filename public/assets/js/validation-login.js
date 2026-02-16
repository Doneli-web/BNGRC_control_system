document.addEventListener("DOMContentLoaded", ()=>{
    const form = document.querySelector("#registerForm");
    if(!form)return;

    const username = document.querySelector("#nom");
    const errorDiv = document.querySelector("#nomError");

    function checkUsername(){
        if(!username.value.trim())return false
        return true;
    }

    function setError(element, errorPanel){
        element.style.border = "1px solid red";
        const h1 = document.createElement("h1");
        h1.textContent = "Veuillez remplir le formulaire";
        errorPanel.appendChild(h1);
    }

    function setSuccess(element){
        element.style.border = "1px solid green";
    }

    function clearError(){
        if(errorDiv && errorDiv.lastElementChild){
            errorDiv.lastElementChild.remove();
        }
        
    }

    async function login(username){
        let link = window.location.href+"/register"
        const response = await fetch(link, {
            method : "POST",
            body: username,
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        return response;
    }

    username.addEventListener('blur', ()=>{
        clearError();
        if(!checkUsername()){
            setError(username, errorDiv);
        }   
        setSuccess(username);
    });

    form.addEventListener('submit', async (e)=>{
        e.preventDefault();

        clearError();
        if(!checkUsername()){
            setError(username, errorDiv);
            return;
        }
        const response = await login();
        alert("response");
    });
});