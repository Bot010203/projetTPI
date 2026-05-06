/*
* Nom du projet : ProjetTPI
* Auteur : Paul Chiacchiari
* Date : 22.04.2026
* Nom fichier : pagePrincipale.js
* But : Script pour gérer l'affichage des annonces sur la page principale
*/
const API = 'http://localhost:8000';

async function loadAds() {
    //Avoir l'URL + un parametre
    let url = API + '/annonces?';

    //Récuperer les valeurs des filtres
    const title = document.getElementById('title').value;
    const brand = document.getElementById('brand').value;
    const model = document.getElementById('model').value;
    const priceMin = document.getElementById('price-min').value;
    const priceMax = document.getElementById('price-max').value;
    const sale = document.getElementById('sale').value;
    const sortBy = document.getElementById('sort-by').value;
    const sortOrd = document.getElementById('sort-order').value;
    const location = document.getElementById('location').value;
    const yearMin = document.getElementById('year-min').value;
    const yearMax = document.getElementById('year-max').value;

    //Ajouter les paramètres à l'URL dynamiquement 
    if (title) {
        url += 'title=' + title + '&';
    }
    if (brand) {
        url += 'brand=' + brand + '&';
    }
    if (model) {
        url += 'model=' + model + '&';
    }
    if (priceMin) {
        url += 'price_min=' + priceMin + '&';
    }
    if (priceMax) {
        url += 'price_max=' + priceMax + '&';
    }
    if (sale !== '') {
        url += 'sale=' + sale + '&';
    }
    if (sortBy) {
        url += 'sort_by=' + sortBy + '&';
    }
    if (sortOrd) {
        url += 'sort_order=' + sortOrd + '&';
    }
    if (location) {
        url += 'location=' + location + '&';
    }
    if (yearMin) {
        url += 'year_min=' + yearMin + '&';
    }
    if (yearMax) {
        url += 'year_max=' + yearMax + '&';
    }

    showLoading();

    try {
        const reponse = await fetch(url);
        const annonces = await reponse.json();
        renderAds(annonces);
    } catch (error) {
        showError();
    }
}

function showLoading() {
    document.getElementById('liste-annonces').innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Chargement...</p>
        </div>`;
}

function showError() {
    document.getElementById('liste-annonces').innerHTML =
        `<div class="error"><p>Erreur lors du chargement</p></div>`;
}

function renderAds(annonces) {
    document.getElementById('count-annonces').textContent = annonces.length;

    if (annonces.length === 0) {
        document.getElementById('liste-annonces').innerHTML =
            `<div class="empty"><p>Aucune annonce trouvée</p></div>`;
        return;
    }

    let html = '<div class="annonces-grid">';
    for (let i = 0; i < annonces.length; i++) {
        html += createAdCard(annonces[i]);
    }
    html += '</div>';
    document.getElementById('liste-annonces').innerHTML = html;
}
//Aide avec ia pour la création de la carte d'annonce
function createAdCard(annonce) {

    let prix;

    if (annonce.price) {
        prix = parseFloat(annonce.price).toLocaleString('fr-CH') + ' CHF';
    } else {
        prix = 'Prix non défini';
    }


    let date = new Date(annonce.date_publication).toLocaleDateString('fr-CH');


    let badge;

    if (annonce.sale == 1) {
        badge = '<span class="badge badge-vente">Vente</span>';
    } else {
        badge = '<span class="badge badge-achat">Achat</span>';
    }


    let img;

    if (annonce.thumbnail) {
        img = `<img src="${API}${annonce.thumbnail}" alt="${annonce.title}">`;
    } else {
        img = `<div class="card-placeholder"><i class="bi bi-car-front"></i></div>`;
    }


    let description;

    if (annonce.description) {
        description = annonce.description;
    } else {
        description = 'Aucune description';
    }


    let locationHTML = '';

    if (annonce.location) {
        locationHTML = '<span><i class="bi bi-geo-alt"></i> ' + annonce.location + '</span>';
    }

    let brandHTML = '';

    if (annonce.brand) {
        let modele = '';

        if (annonce.model) {
            modele = annonce.model;
        }

        brandHTML = '<span><i class="bi bi-tag"></i> ' + annonce.brand + ' ' + modele + '</span>';
    }

    let yearHTML = '';

    if (annonce.year_first_registration) {
        yearHTML = '<span><i class="bi bi-calendar"></i> ' + annonce.year_first_registration + '</span>';
    }


    return `
        <a href="pageDetailsAnnonce.html?id=${annonce.id_advertisement}" class="card-link">
        <div class="card">
            ${img}

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
                <span class="card-price">${prix}</span>
                <span class="card-date">${date}</span>
            </div>
        </div>
        </a>`;
}
//reset les filtres et recharge les annonces
function resetFiltres() {
    document.getElementById('title').value = '';
    document.getElementById('brand').value = '';
    document.getElementById('model').value = '';
    document.getElementById('price-min').value = '';
    document.getElementById('price-max').value = '';
    document.getElementById('sale').value = '';
    document.getElementById('sort-by').value = 'date_publication';
    document.getElementById('sort-order').value = 'DESC';
    document.getElementById('location').value = '';
    document.getElementById('year-min').value = '';
    document.getElementById('year-max').value = '';
    loadAds();
}
function loginUser() {
    const token = localStorage.getItem('token');
    const login = localStorage.getItem('login');

    const h1 = document.querySelector('h1');

    if (token && login) {
        h1.textContent = `Bienvenue ${login} sur la Plateforme d'annonces pour véhicules`;
    } else {
        h1.textContent = "Bienvenue sur la Plateforme d'annonces pour véhicules";
    }
}
function redirectToLogin() {
    window.location.href = 'pageConnexion.html';
}

function logout() {
    localStorage.clear();
    redirectToLogin();
}
loginUser();
loadAds();