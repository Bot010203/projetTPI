const API = 'http://localhost:8000';
const idUser = parseInt(localStorage.getItem('id_user'));
let conversationActive = null;
function init() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'pageConnexion.html';
        return;
    }
    fetchConversations();
}
async function fetchConversations() {
    const token = localStorage.getItem('token');
    try {
        const response = await fetch(`${API}/conversations`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const conversations = await response.json();
        displayConversations(conversations);
    } catch (error) {
        console.error('Erreur lors de la récupération des conversations :', error);
    }
}
function displayConversations(conversations) {
    if (conversations.length === 0) {
        document.getElementById('liste-conversations').innerHTML = `
            <div class="empty">
                <i class="bi bi-chat-dots" style="font-size:3rem; display:block; margin-bottom:1rem; opacity:0.3;"></i>
                <p>Vous n'avez pas encore de conversations.</p>
            </div>`;
        return;
    }
    let html = '<div class="conversations-liste">';
    conversations.forEach(conversation => {
        html += conversationCard(conversation);
    });
    html += '</div>';
    document.getElementById('liste-conversations').innerHTML = html;
}
function conversationCard(conversation) {
    const isUnread = conversation.read == 0 && conversation.id_recipient == idUser;
    const otherUserId = conversation.id_sender == idUser ? conversation.id_recipient : conversation.id_sender;

    const date = new Date(conversation.timestamp).toLocaleString('fr-CH', {
        hour: '2-digit',
        minute: '2-digit'
    });

    return `
    <div class="conversation-item ${isUnread ? 'non-lu' : ''}"
        onclick="ouvrirConversation(${conversation.id_advertisement}, ${otherUserId}, '${conversation.title}', '${conversation.login}')">

        <div>
            <div><i class="bi bi-car-front"></i> ${conversation.title}</div>
            <div><i class="bi bi-person"></i> ${conversation.login}</div>
            <div>${conversation.text}</div>
        </div>

        <div>
            <div>${date}</div>
            ${isUnread ? '<span class="badge-nonlu">Non lu</span>' : ''}

            <button class="btn btn-danger btn-sm"
                onclick="event.stopPropagation(); preparerSuppression(${conversation.id_advertisement}, ${otherUserId})">
                <i class="bi bi-trash"></i>
            </button>
        </div>

    </div>`;
}
async function openConversation(idAdvertisement, idCorrespondant, sujet, correspondant) {
    document.getElementById('detail-sujet').textContent = sujet;
    document.getElementById('detail-correspondant').textContent = 'Avec : ' + correspondant;
    document.getElementById('texte-reponse').value = '';
    document.getElementById('msg-detail').classList.add('d-none');

    document.getElementById('page-liste').classList.add('hidden');
    document.getElementById('page-detail').classList.add('active');
    await loadMessages(idAdvertisement, idCorrespondant);
}
async function loadMessages(idAdvertisement, idCorrespondant) {
    const token = localStorage.getItem('token');
    document.getElementById('messages-liste').innerHTML = `
        <div class="loading" style="padding:2rem;">
            <div class="spinner"></div>
        </div>`;
    try {
        const response = await fetch(`${API}/conversations/${idAdvertisement}/${idCorrespondant}`, {
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const messages = await response.json();
        displayMessages(messages);
    } catch (error) {
        document.getElementById('messages-liste').innerHTML =
            '<p style="color:red; padding:1rem;">Erreur chargement des messages</p>';
    }
}