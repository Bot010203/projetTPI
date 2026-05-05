const API = 'http://localhost:8000';
let modifierAnnonceId = null;
let supprimerAnnonceId = null;
function inisialiserPage() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'pageConnexion.html';
        return;
    }
    chargerAnnonces();
}
async function chargerMesAnnonces() {
    const token = localStorage.getItem('token');
    try {
        const reponse = await fetch(`${API}/mes-annonces`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const annonces = await reponse.json();
        afficherAnnonces(annonces);
    } catch (error) {
        afficherErreur();
        document.getElementById('liste-annonces').innerHTML = '';
        '<div class="error"><p>Erreur lors du chargement</p></div>';
    }
}
function afficherAnnonces(annonces) {
    document.getElementById('count-annonces').textContent = annonces.length;
    if (annonces.length === 0) {
        document.getElementById('liste-annonces').innerHTML = `
            <div class="empty">
                <i class="bi bi-megaphone" style="font-size:3rem; display:block; margin-bottom:1rem; opacity:0.3;"></i>
                <p>Vous n'avez pas encore d'annonces.</p>
                <button class="btn btn-dark" style="margin-top:1rem;" onclick="ouvrirModalCreer()">
                    <i class="bi bi-plus-lg"></i> Créer ma première annonce
                </button>
            </div>`;
        return;
    }
    let htmlAffichage = '<div class="annonces-grid">';
    annonces.forEach(annonce => {
        htmlAffichage += carteAnnonce(annonce);
    });
    htmlAffichage += '</div>';
    document.getElementById('liste-annonces').innerHTML = htmlAffichage;
}

//aider avec ia pour faire cette fonction 
function carteAnnonce(annonce) {
    const prix = annonce.price ? parseFloat(annonce.price).toLocaleString('fr-CH') + ' CHF' : 'Prix non défini';
    const date = new Date(annonce.date_publication).toLocaleDateString('fr-CH');
    const badge = annonce.sale == 1
        ? '<span class="badge badge-vente">Vente</span>'
        : '<span class="badge badge-achat">Achat</span>';

    const img = annonce.thumbnail
        ? `<img src="${API}${annonce.thumbnail}" alt="${annonce.title}">`
        : `<div class="card-placeholder"><i class="bi bi-car-front"></i></div>`;

    return `
    <div class="card">
        <a href="pageAnnonce.html?id=${annonce.id_advertisement}" style="text-decoration:none; color:inherit;">
            ${img}
            <div class="card-body">
                ${badge}
                <div class="card-title">${annonce.title}</div>
                <div class="card-desc">${annonce.description || 'Aucune description'}</div>
                <div class="card-meta">
                    ${annonce.location ? '<span><i class="bi bi-geo-alt"></i> ' + annonce.location + '</span>' : ''}
                    ${annonce.brand ? '<span><i class="bi bi-tag"></i> ' + annonce.brand + ' ' + (annonce.model || '') + '</span>' : ''}
                    ${annonce.year_first_registration ? '<span><i class="bi bi-calendar"></i> ' + annonce.year_first_registration + '</span>' : ''}
                </div>
            </div>
            <div class="card-footer">
                <span class="card-price">${prix}</span>
                <span class="card-date">${date}</span>
            </div>
        </a>
        <div class="card-actions">
            <button class="btn btn-warning btn-sm" onclick="ouvrirModalModifier(${annonce.id_advertisement})">
                <i class="bi bi-pencil"></i> Modifier
            </button>
            <button class="btn btn-danger btn-sm" onclick="ouvrirModalSupprimer(${annonce.id_advertisement})">
                <i class="bi bi-trash"></i> Supprimer
            </button>
        </div>
    </div>`;
}
function creer()
{
    modifierAnnonceId = null;
    document.getElementById('modal-title').textContent = 'Créer une annonce';
    viderFormulaire();
    document.getElementById('modal-annonce').classList.add('active');
    document.getElementById('modal-error').classList.add('d-none');
}