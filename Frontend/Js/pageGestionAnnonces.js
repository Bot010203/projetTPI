/*
* Nom du projet : ProjetTPI
* Auteur : Paul Chiacchiari
* Date : 22.04.2026
* Nom fichier : pageGestionAnnonces.js
* But : Script pour gérer la gestion des annonces de l'utilisateur
*/
const API_URL = 'http://localhost:8000';

let annonceEnEditionId = null;
let annonceASupprimerId = null;

function initPage() {
    const token = localStorage.getItem('token');
    // Vérification de l'authentification
    if (!token) {
        redirectToLogin();
        return;
    }

    loadMyAds();
}

async function loadMyAds() {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/mes-annonces`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });

        const annonces = await response.json();
        renderAdsList(annonces);

    } catch (error) {
        showLoadError();
    }
}

function renderAdsList(annonces) {
    document.getElementById('count-annonces').textContent = annonces.length;
    // Afficher un message si aucune annonce n'est trouvée
    if (annonces.length === 0) {
        renderEmptyAds();
        return;
    }

    let html = '';
    for (let i = 0; i < annonces.length; i++) {
        html += createAdCard(annonces[i]);
    }

    document.getElementById('liste-annonces').innerHTML =
        `<div class="annonces-grid">${html}</div>`;
}

function renderEmptyAds() {
    document.getElementById('liste-annonces').innerHTML = `
        <div class="empty">
            <i class="bi" style="font-size:3rem;"></i>
            <p>Vous n'avez pas encore d'annonces.</p>
            <button class="btn btn-dark" onclick="openCreateAdModal()">
                <i class="bi bi-plus-lg"></i> Créer une annonce
            </button>
        </div>`;
}
//Aide avec Ia pour la création de la carte d'annonce
function createAdCard(annonce) {
    const prix = formatPrice(annonce.price);
    const date = formatDate(annonce.date_publication);
    const badge = createBadge(annonce.sale);
    const image = createImage(annonce);

    let description = annonce.description;
    if (!description) {
        description = 'Aucune description';
    }

    let locationHTML = '';
    if (annonce.location) {
        locationHTML = `<span><i class="bi bi-geo-alt"></i> ${annonce.location}</span>`;
    }

    let brandHTML = '';
    if (annonce.brand) {
        let modele = annonce.model;
        if (!modele) {
            modele = '';
        }
        brandHTML = `<span><i class="bi bi-tag"></i> ${annonce.brand} ${modele}</span>`;
    }

    let yearHTML = '';
    if (annonce.year_first_registration) {
        yearHTML = `<span><i class="bi bi-calendar"></i> ${annonce.year_first_registration}</span>`;
    }

    return `
    <div class="card">
        <a href="pageDetailsAnnonce.html?id=${annonce.id_advertisement}">
            ${image}
            <div class="card-body">
                ${badge}
                <div class="card-title">${annonce.title}</div>
                <div class="card-desc">${description}</div>
                <div class="card-meta">
                    ${locationHTML}
                    ${brandHTML}
                    ${yearHTML}
                </div>
            </div>
            <div class="card-footer">
                <span>${prix}</span>
                <span>${date}</span>
            </div>
        </a>

        <div class="card-actions">
            <button class="btn btn-warning btn-sm" onclick="openEditAdModal(${annonce.id_advertisement})">
                Modifier
            </button>
            <button class="btn btn-danger btn-sm" onclick="openDeleteAdModal(${annonce.id_advertisement})">
                Supprimer
            </button>
        </div>
    </div>`;
}

function formatPrice(prix)
// Formater le prix  avec séparation des milliers
{
    if (prix) {
        return parseFloat(prix).toLocaleString('fr-CH') + ' CHF';
    }
    return 'Prix non défini';
}

function formatDate(date) {
    // Formater la date 
    return new Date(date).toLocaleDateString('fr-CH');
}

function createBadge(estVente) {
    if (estVente == 1) {
        return '<span class="badge badge-vente">Vente</span>';
    } else {
        return '<span class="badge badge-achat">Achat</span>';
    }
}

function createImage(annonce) {
    // Afficher l'image de l'annonce ou un placeholder
    if (annonce.thumbnail) {
        return `<img src="${API_URL}${annonce.thumbnail}" alt="${annonce.title}">`;
    }

    return `<div class="card-placeholder"><i class="bi bi-car-front"></i></div>`;
}

function openCreateAdModal() {
    annonceEnEditionId = null;
    openAdModal('Nouvelle annonce');
}

async function openEditAdModal(idAnnonce) {
    annonceEnEditionId = idAnnonce;
    openAdModal('Modifier l\'annonce');

    try {
        const response = await fetch(`${API_URL}/annonces/${idAnnonce}`);
        const annonce = await response.json();

        fillForm(annonce);

    } catch {
        showGlobalMessage('Impossible de charger l\'annonce', 'danger');
    }
}

function openAdModal(titre) {
    // Ouvrir le modal de création/édition d'annonce
    document.getElementById('modal-titre').textContent = titre;
    document.getElementById('modal-erreur').classList.add('d-none');
    document.getElementById('modal-annonce').classList.add('active');
}

function fillForm(a) {
    // Remplir le formulaire avec les données de l'annonce
    document.getElementById('m-title').value = a.title || '';
    document.getElementById('m-description').value = a.description || '';
    document.getElementById('m-sale').value = a.sale ?? '1';
    document.getElementById('m-location').value = a.location || '';
    document.getElementById('m-brand').value = a.brand || '';
    document.getElementById('m-model').value = a.model || '';
    document.getElementById('m-price').value = a.price || '';
    document.getElementById('m-year').value = a.year_first_registration || '';
}

async function saveAd() {
    const token = localStorage.getItem('token');
    const titre = document.getElementById('m-title').value.trim();

    if (!titre) {
        showModalError('Le titre est obligatoire');
        return;
    }

    const data = getFormData();

    let url;
    let method;

    if (annonceEnEditionId) {
        url = `${API_URL}/annonces/${annonceEnEditionId}`;
        method = 'PUT';
    } else {
        url = `${API_URL}/annonces`;
        method = 'POST';
    }

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            let message = result.error;
            if (!message) {
                message = 'Erreur';
            }
            showModalError(message);
            return;
        }
        if (!annonceEnEditionId && result.id_advertisement) {
            await uploadImages(result.id_advertisement, token);
        }
        closeAdModal();
        showGlobalMessage('Annonce sauvegardée !', 'success');
        loadMyAds();

    } catch {
        showModalError('Erreur serveur');
    }
}
async function uploadImages(idAnnonce, token) {
    const input = document.getElementById('m-images');
    if (!input || input.files.length === 0) return;

    for (let i = 0; i < input.files.length; i++) {
        const formData = new FormData();
        formData.append('image', input.files[i]);

        await fetch(`${API_URL}/annonces/${idAnnonce}/images`, {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: formData
        });
    }
}
function getFormData() {
    // Récupérer les données du formulaire et les prépare pour l'API
    let price = parseFloat(document.getElementById('m-price').value);
    if (!price) {
        price = null;
    }

    let year = parseInt(document.getElementById('m-year').value);
    if (!year) {
        year = null;
    }

    return {
        // Les champs obligatoires
        title: document.getElementById('m-title').value.trim(),
        description: document.getElementById('m-description').value.trim(),
        sale: parseInt(document.getElementById('m-sale').value),
        location: document.getElementById('m-location').value.trim(),
        brand: document.getElementById('m-brand').value.trim(),
        model: document.getElementById('m-model').value.trim(),
        price: price,
        year_first_registration: year
    };
}

function openDeleteAdModal(idAnnonce) {
    annonceASupprimerId = idAnnonce;
    document.getElementById('modal-supprimer').classList.add('active');
}

function closeDeleteModal() {
    annonceASupprimerId = null;
    document.getElementById('modal-supprimer').classList.remove('active');
}

async function confirmDeleteAd() {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_URL}/annonces/${annonceASupprimerId}`, {
            method: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token }
        });

        closeDeleteModal();

        if (response.ok) {
            showGlobalMessage('Annonce supprimée', 'success');
            loadMyAds();
        } else {
            showGlobalMessage('Erreur suppression', 'danger');
        }

    } catch {
        showGlobalMessage('Erreur serveur', 'danger');
    }
}

function showGlobalMessage(message, type) {
    // Afficher un message global en haut de la page
    const messageGlobal = document.getElementById('msg-global');
    messageGlobal.textContent = message;
    messageGlobal.className = `alert alert-${type}`;
    messageGlobal.classList.remove('d-none');

    setTimeout(function () {
        messageGlobal.classList.add('d-none');
    }, 4000);
}

function showModalError(message) {
    const messageGlobal = document.getElementById('modal-erreur');
    messageGlobal.textContent = message;
    messageGlobal.classList.remove('d-none');
}

function showLoadError() {
    document.getElementById('liste-annonces').innerHTML =
        '<div class="error">Erreur de chargement</div>';
}

function redirectToLogin() {
    window.location.href = 'pageConnexion.html';
}

function logout() {
    localStorage.clear();
    redirectToLogin();
}

document.getElementById('modal-annonce').addEventListener('click', function (e) {
    if (e.target.id === 'modal-annonce') {
        closeAdModal();
    }
});

document.getElementById('modal-supprimer').addEventListener('click', function (e) {
    if (e.target.id === 'modal-supprimer') {
        closeDeleteModal();
    }
});

function closeAdModal() {
    document.getElementById('modal-annonce').classList.remove('active');
    document.getElementById('m-images').value = '';
    annonceEnEditionId = null;
}

initPage();