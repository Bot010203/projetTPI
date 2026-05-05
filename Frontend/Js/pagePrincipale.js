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
    const location = document.getElementById('f-location').value;
    const yearMin = document.getElementById('f-year-min').value;
    const yearMax = document.getElementById('f-year-max').value;

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
    if (sale) {
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

    afficherChargement();

    try {
        const reponse = await fetch(url);
        const annonces = await reponse.json();
        afficherAnnonces(annonces);
    } catch (error) {
        afficherErreur();
    }
}

function afficherChargement() {
    document.getElementById('liste-annonces').innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Chargement...</p>
        </div>`;
}

function afficherErreur() {
    document.getElementById('liste-annonces').innerHTML =
        `<div class="error"><p>Erreur lors du chargement</p></div>`;
}

function afficherAnnonces(annonces) {
    document.getElementById('count-annonces').textContent = annonces.length;

    if (annonces.length === 0) {
        document.getElementById('liste-annonces').innerHTML =
            `<div class="empty"><p>Aucune annonce trouvée</p></div>`;
        return;
    }

    let html = '<div class="annonces-grid">';
    for (let i = 0; i < annonces.length; i++) {
        html += carteAnnonce(annonces[i]);
    }
    html += '</div>';
    document.getElementById('liste-annonces').innerHTML = html;
}

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
    <a href="pageAnnonce.html?id=${annonce.id_advertisement}" class="card-link">
        <div class="card">
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
        </div>
    </a>`;
}

function resetFiltres() {
    document.getElementById('f-title').value = '';
    document.getElementById('f-brand').value = '';
    document.getElementById('f-model').value = '';
    document.getElementById('f-price-min').value = '';
    document.getElementById('f-price-max').value = '';
    document.getElementById('f-sale').value = '';
    document.getElementById('f-sort-by').value = 'date_publication';
    document.getElementById('f-sort-order').value = 'DESC';
    document.getElementById('f-location').value = '';
    document.getElementById('f-year-min').value = '';
    document.getElementById('f-year-max').value = '';
    chargerAnnonces();
}

chargerAnnonces();