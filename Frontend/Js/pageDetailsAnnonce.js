const API = 'http://localhost:8000';

// Récupère l'id dans l'URL
const params = new URLSearchParams(window.location.search);
const idAnnonce = params.get('id');


async function init() {
    if (!idAnnonce) {
        afficherErreur();
        return;
    }

    mettreAJourNavbar();

    try {
        // Charge l'annonce
        const resAnnonce = await fetch(`${API}/annonces/${idAnnonce}`);
        if (!resAnnonce.ok) { afficherErreur(); return; }
        const annonce = await resAnnonce.json();

        // Charge les images
        const resImages = await fetch(`${API}/annonces/${idAnnonce}/images`);
        const images = await resImages.json();

        afficherAnnonce(annonce, images);

    } catch (e) {
        afficherErreur();
    }
}

function afficherAnnonce(a, images) {
    document.title = a.title + ' — Plateforme véhicules';

    const badge = document.getElementById('badge-type');
    if (a.sale == 1) {
        badge.textContent = 'Vente';
        badge.classList.add('bg-success');
    } else {
        badge.textContent = 'Achat';
        badge.classList.add('bg-primary');
    }

    document.getElementById('titre').textContent = a.title;
    document.getElementById('prix').textContent = a.price ? parseFloat(a.price).toLocaleString('fr-CH') + ' CHF' : 'Prix non défini';
    document.getElementById('description').textContent = a.description || 'Aucune description';
    document.getElementById('marque').textContent = a.brand || '—';
    document.getElementById('modele').textContent = a.model || '—';
    document.getElementById('annee').textContent = a.year_first_registration || '—';
    document.getElementById('ville').textContent = a.location || '—';
    document.getElementById('date').textContent = new Date(a.date_publication).toLocaleDateString('fr-CH');

    const inner = document.getElementById('carousel-inner');
    if (images.length === 0) {
        inner.innerHTML = `
                    <div class="carousel-item active">
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height:400px;">
                            <i class="bi bi-car-front text-white" style="font-size:5rem;"></i>
                        </div>
                    </div>`;
    } else {
        inner.innerHTML = images.map((img, i) => `
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                        <img src="${API}${img.path}" class="d-block w-100"
                             style="height:400px; object-fit:cover; border-radius:0.375rem;"
                             alt="Photo ${i + 1}">
                    </div>`).join('');
    }

    const token = localStorage.getItem('token');
    const idUser = localStorage.getItem('id_user');

    if (!token) {
        document.getElementById('bloc-non-connecte').classList.remove('d-none');
    } else if (idUser && parseInt(idUser) === parseInt(a.id_user)) {
        document.getElementById('bloc-proprio').classList.remove('d-none');
    } else {
        document.getElementById('bloc-connecte').classList.remove('d-none');
    }

    document.getElementById('chargement').classList.add('d-none');
    document.getElementById('contenu').classList.remove('d-none');
}

function afficherErreur() {
    document.getElementById('chargement').classList.add('d-none');
    document.getElementById('erreur').classList.remove('d-none');
}


async function envoyerMessage() {
    const text = document.getElementById('message-text').value.trim();
    const token = localStorage.getItem('token');

    if (!text) {
        const el = document.getElementById('msg-erreur');
        el.textContent = 'Veuillez écrire un message';
        el.classList.remove('d-none');
        return;
    }

    try {
        const res = await fetch(`${API}/annonces/${idAnnonce}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ text })
        });

        if (res.ok) {
            document.getElementById('msg-succes').classList.remove('d-none');
            document.getElementById('msg-erreur').classList.add('d-none');
            document.getElementById('message-text').value = '';
        } else {
            const data = await res.json();
            const el = document.getElementById('msg-erreur');
            el.textContent = data.error || 'Erreur lors de l\'envoi';
            el.classList.remove('d-none');
        }
    } catch (e) {
        const el = document.getElementById('msg-erreur');
        el.textContent = 'Impossible de contacter le serveur';
        el.classList.remove('d-none');
    }
}


function mettreAJourNavbar() {
    const token = localStorage.getItem('token');
    const nav = document.getElementById('nav-buttons');

    if (token) {
        nav.innerHTML = `
                    <a href="mesAnnonces.html" class="btn btn-outline-dark btn-sm me-2">Mes annonces</a>
                    <a href="conversations.html" class="btn btn-outline-dark btn-sm me-2">Messages</a>
                    <button class="btn btn-outline-dark btn-sm" onclick="deconnexion()">Déconnexion</button>`;
    } else {
        nav.innerHTML = `
                    <a href="pageConnexion.html" class="btn btn-outline-dark btn-sm me-2">Connexion</a>
                    <a href="pageInscription.html" class="btn btn-dark btn-sm">Inscription</a>`;
    }
}

function deconnexion() {
    localStorage.removeItem('token');
    localStorage.removeItem('id_user');
    window.location.href = 'index.html';
}

init();