
const API = 'http://localhost:8000';
async function creerCompte() {
    const login = document.getElementById('login').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirm-password').value.trim();

    if(!login || !email || !password || !confirmPassword) {
        afficherMessageErreur('Tous les champs sont requis');
        return;
    }
    if(password !== confirmPassword) {
        afficherMessageErreur('Les mots de passe ne sont pas identiques');
        return;
    }
    try {
        const reponse =await fetch(`${API}/register`, {
            method: 'POST',
            headers:{'Content-Type': 'application/json'},
            body: JSON.stringify({login, email, password, confirm_password: confirmPassword})
        });
    const data = await reponse.json();
    if(!reponse.ok){
        afficherMessageErreur(data.message);
        return;
    }
    window.location.href = 'pageConnexion.html';
    }
    catch (error) {
        afficherMessageErreur('Erreur lors de la création du compte');
    }
}

function afficherMessageErreur(message) {
    const msgErreur = document.getElementById('msg-erreur');
    msgErreur.textContent = message;
    msgErreur.classList.remove('d-none');
}