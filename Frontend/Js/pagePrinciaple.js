const API = 'http://localhost:8000';
async function chargerAnnonces() {
    let url = API + '/annonces?';

    const title = document.getElementById('f-title').value;
    const brand = document.getElementById('f-brand').value;
    const model = document.getElementById('f-model').value;
    const priceMin = document.getElementById('f-price-min').value;
    const priceMax = document.getElementById('f-price-max').value;
    const sale = document.getElementById('f-sale').value;
    const sortBy = document.getElementById('f-sort-by').value;
    const sortOrd = document.getElementById('f-sort-order').value;

    if (title != '') {
        url += 'title=' + title + '&';
    }
    if (brand != '') {
        url += 'brand=' + brand + '&';
    }
    if (model != '') {
        url += 'model=' + model + '&';
    }
    if (priceMin != '') {
        url += 'price_min=' + priceMin + '&';
    }
    if (priceMax != '') {
        url += 'price_max=' + priceMax + '&';
    }
    if (sale != '') {
        url += 'sale=' + sale + '&';
    }
    if (sortBy != '') {
        url += 'sort_by=' + sortBy + '&';
    }
    if (sortOrd != '') {
        url += 'sort_order=' + sortOrd + '&';
    }
    afficherChargement();

    try {
        const reponse = await fetch(url);
        const annonces = await reponse.json();
        afficherAnnonces(annonces);
    } catch (error) {
        afficherErreur();
    }

}
/**
    * Affiche un message de chargement pendant que les annonces sont récupérées
    */
function afficherChargement() {
    document.getElementById('liste-annonces').innerHTML =
        '<p>Chargement...</p>';
}
/**
 * Affiche un message d'erreur si la récupération des annonces échoue
 */
function afficherErreur() {
    document.getElementById('liste-annonces').innerHTML =
        `<p style="color:red;"> Erreur lors du chargement </p>`;
}
/**
 * 
 * @param {*} annonces 
 * @returns 
 */
function afficherAnnonces(annonces) {
    document.getElementById('count-annonces').textContent = annonces.length;
    if (annonces.length === 0) {
        document.getElementById('liste-annonces').innerHTML =
            `<p>Aucune annonce trouvée</p>`;
        return;
    }
    let row = '<div class="row">';
    for (let i = 0; i < annonces.length; i++) {
        row += carteAnnonce(annonces[i]);
    }
    row += '</div>';
    document.getElementById('liste-annonces').innerHTML = row;
}
/**
 * 
 * @param {*} annonce 
 * @returns 
 */
function carteAnnonce(annonce) {
    let prix = annonce.price ? annonce.price + ' CHF' : 'pas défini';
    let img = '';
    if (annonce.thumbnail) {
        img = '<img src="' + API + annonce.thumbnail + '" class="card-img-top" style="height:200px; object-fit:cover;">';
    } else {
        img = '<div style="height:150px; background:#ccc;"></div>';
    }
    return `
    <div class="col-md-4">
        <div class="card">
            ${img}
            <div class="card-body">
                <h5>${annonce.title}</h5>
                <p>${annonce.description || ''}</p>
                <p>${annonce.location || ''}</p>
                <strong>${prix}</strong>
            </div>
        </div>
    </div>
    `;
}
/**
 * Réinitialise tous les champs de filtre et recharge les annonces sans filtres
 */
function resetFiltres() {
    document.getElementById('f-title').value = '';
    document.getElementById('f-brand').value = '';
    document.getElementById('f-model').value = '';
    document.getElementById('f-price-min').value = '';
    document.getElementById('f-price-max').value = '';
    document.getElementById('f-sale').value = '';
    document.getElementById('f-sort-by').value = 'date_publication';
    document.getElementById('f-sort-order').value = 'DESC';
    chargerAnnonces();
}
chargerAnnonces();