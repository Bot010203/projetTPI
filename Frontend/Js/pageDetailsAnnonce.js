const API_URL = 'http://localhost:8000';

// Récupérer l'id de l'annonce depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const annonceId = urlParams.get('id');

async function loadAd() {
    if (!annonceId) {
        showError();
        return;
    }

    updateNavbar();

    try {
        // Récupérer les données de l'annonce et les images
        const annonce = await fetchAd(annonceId);
        const images = await fetchAdImages(annonceId);

        renderAd(annonce, images);

    } catch (error) {
        showError();
    }
}

async function fetchAd(id) {
    // Récupérer les données de l'annonce
    const response = await fetch(`${API_URL}/annonces/${id}`);
    if (!response.ok) throw new Error('Annonce introuvable');
    return await response.json();
}

async function fetchAdImages(id) {
    // Récupérer les images de l'image
    const response = await fetch(`${API_URL}/annonces/${id}/images`);
    return await response.json();
}

function renderAd(annonce, images) {
    //titre de la page
    document.title = `${annonce.title} — Plateforme véhicules`;

    displayAdTypeBadge(annonce.sale);
    fillAdDetails(annonce);
    displayAdImages(images, annonce.title);
    displayUserBlock(annonce.id_user);

    showContent();
}

function displayAdTypeBadge(estVente) {
    const badge = document.getElementById('badge-type');

    if (estVente == 1) {
        badge.textContent = 'Vente';
        badge.classList.add('badge-vente');
    } else {
        badge.textContent = 'Achat';
        badge.classList.add('badge-achat');
    }
}

function fillAdDetails(annonce) {
    // Remplir les informations de l'annonce
    document.getElementById('titre').textContent = annonce.title;
    document.getElementById('prix').textContent = formatPrice(annonce.price);
    document.getElementById('description').textContent = annonce.description || 'Aucune description';
    document.getElementById('marque').textContent = annonce.brand || '—';
    document.getElementById('modele').textContent = annonce.model || '—';
    document.getElementById('annee').textContent = annonce.year_first_registration || '—';
    document.getElementById('ville').textContent = annonce.location || '—';
    document.getElementById('date').textContent = new Date(annonce.date_publication).toLocaleDateString('fr-CH');
}

function formatPrice(prix) {
    if (!prix) return 'Prix non défini';
    return parseFloat(prix).toLocaleString('fr-CH') + ' CHF';
}

function displayAdImages(images, titre) {
    const container = document.getElementById('photo-annonce');

    if (images.length === 0) {
        container.innerHTML = `
            <div class="carousel-placeholder">
                <i class="bi bi-car-front"></i>
            </div>`;
    } else {
        container.innerHTML = `
            <img src="${API_URL}${images[0].path}" alt="${titre}"
                 style="width:100%; height:400px; object-fit:cover; border-radius:8px;">`;
    }
}

function displayUserBlock(idProprietaire) {
    const token = localStorage.getItem('token');
    const utilisateurId = localStorage.getItem('id_user');

    if (!token) {
        showBlock('bloc-non-connecte');
    } else if (parseInt(utilisateurId) === parseInt(idProprietaire)) {
        showBlock('bloc-proprio');
    } else {
        showBlock('bloc-connecte');
    }
}

function showBlock(id) {
    document.getElementById(id).classList.remove('d-none');
}

function showContent() {
    document.getElementById('chargement').classList.add('d-none');
    document.getElementById('contenu').classList.remove('d-none');
}

function showError() {
    document.getElementById('chargement').classList.add('d-none');
    document.getElementById('erreur').classList.remove('d-none');
}

async function sendMessage() {
    const message = document.getElementById('message-text').value.trim();
    const token = localStorage.getItem('token');
    // Vérification du message
    if (!message) {
        showErrorMessage('Veuillez écrire un message');
        return;
    }

    try {
        const response = await fetch(`${API_URL}/annonces/${annonceId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ text: message })
        });

        if (response.ok) {
            showSuccessMessage();
        } else {
            const data = await response.json();
            showErrorMessage(data.error || 'Erreur lors de l\'envoi');
        }

    } catch (error) {
        showErrorMessage('Impossible de contacter le serveur');
    }
}

function showSuccessMessage() {
    document.getElementById('msg-succes').classList.remove('d-none');
    document.getElementById('msg-erreur').classList.add('d-none');
    document.getElementById('message-text').value = '';
}

function showErrorMessage(message) {
    const el = document.getElementById('msg-erreur');
    el.textContent = message;
    el.classList.remove('d-none');
}

function updateNavbar() {

    const token = localStorage.getItem('token');
    const nav = document.getElementById('nav-buttons');

    if (token) {
        nav.innerHTML = `
            <a href="pageGestionAnnonces.html" class="btn">Mes annonces</a>
            <a href="pageConversations.html" class="btn">Messages</a>
            <button class="btn btn-danger" onclick="logout()">Déconnexion</button>
        `;
    } else {
        nav.innerHTML = `
            <a href="pageConnexion.html" class="btn">Connexion</a>
            <a href="pageInscription.html" class="btn btn-dark">Inscription</a>
        `;
    }
}

function logout() {
    localStorage.clear();
    window.location.href = 'pagePrincipale.html';
}

loadAd();