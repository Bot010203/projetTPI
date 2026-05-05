const API = 'http://localhost:8000';
async function getConnected() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    //Vérification des champs
    if (!email || !password) {
        displayErrorMessage('Tous les champs sont requis');
        return;
    }
    //Appel à l'API pour se connecter
    try {
        const reponse = await fetch(`${API}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await reponse.json();
        if (!reponse.ok) {
            //Affichage de l'erreur retournée par l'API ou d'un message générique
            displayErrorMessage(typeof data === 'string' ? data : data.message || data.error || 'Erreur');
            return;
        }
        localStorage.setItem('token', data.token);
        localStorage.setItem('id_user', data.id_user);
        window.location.href = 'pageAnnonce.html';
    } catch (error) {
        displayErrorMessage('Erreur lors de la connexion');
    }
}
function displayErrorMessage(message) {
    const msgErreur = document.getElementById('msg-erreur');
    msgErreur.textContent = message;
    msgErreur.classList.remove('d-none');
}