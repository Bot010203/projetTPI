
const API = 'http://localhost:8000';
async function createAccount() {
    const login = document.getElementById('login').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirm-password').value.trim();

    //Vérification des champs
    if (!login || !email || !password || !confirmPassword) {
        displayErrorMessage('Tous les champs sont requis');
        return;
    }
    if (password !== confirmPassword) {
        displayErrorMessage('Les mots de passe ne sont pas identiques');
        return;
    }
    try {
        const reponse = await fetch(`${API}/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ login, email, password, confirm_password: confirmPassword })
        });
        const data = await reponse.json();
        if (!reponse.ok) {
            //Affichage de l'erreur retournée par l'API ou d'un message                                            
            displayErrorMessage(typeof data === 'string' ? data : data.message || data.error || 'Erreur');
            return;
        }
        window.location.href = 'pageConnexion.html';
    }
    catch (error) {
        displayErrorMessage('Erreur lors de la création du compte');
    }
}

function displayErrorMessage(message) {
    const msgErreur = document.getElementById('msg-erreur');
    msgErreur.textContent = message;
    msgErreur.classList.remove('d-none');
}