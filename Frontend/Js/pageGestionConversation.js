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
        html += createConversationCard(conversation);
    });
    html += '</div>';

    document.getElementById('liste-conversations').innerHTML = html;
}

function createConversationCard(conversation) {
    const isUnread = conversation.read == 0 && conversation.id_recipient == idUser;
    const otherUserId = conversation.id_sender == idUser
        ? conversation.id_recipient
        : conversation.id_sender;

    const date = new Date(conversation.timestamp).toLocaleString('fr-CH', {
        hour: '2-digit',
        minute: '2-digit'
    });

    return `
    <div class="conversation-item ${isUnread ? 'non-lu' : ''}"
        onclick="openConversation(${conversation.id_advertisement}, ${otherUserId}, '${conversation.title}', '${conversation.login}')">

        <div>
            <div><i class="bi bi-car-front"></i> ${conversation.title}</div>
            <div><i class="bi bi-person"></i> ${conversation.login}</div>
            <div>${conversation.text}</div>
        </div>

        <div>
            <div>${date}</div>
            ${isUnread ? '<span class="badge-nonlu">Non lu</span>' : ''}

            <button class="btn btn-danger btn-sm"
                onclick="event.stopPropagation(); prepareDeleteConversation(${conversation.id_advertisement}, ${otherUserId})">
                <i class="bi bi-trash"></i>
            </button>
        </div>

    </div>`;
}

async function openConversation(idAdvertisement, idCorrespondant, sujet, correspondant) {
    conversationActive = { idAdvertisement, idCorrespondant };

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
        <div class="loading">
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
            '<p style="color:red;">Erreur chargement des messages</p>';
    }
}

function displayMessages(messages) {
    if (messages.length === 0) {
        document.getElementById('messages-liste').innerHTML =
            '<p style="text-align:center; color:#888; padding:2rem;">Aucun message</p>';
        return;
    }

    const sorted = [...messages].reverse();
    let html = '';

    sorted.forEach(msg => {
        const isSend = msg.id_sender == idUser;
        const className = isSend ? 'message-envoye' : 'message-recu';

        const date = new Date(msg.timestamp).toLocaleString('fr-CH', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const nonLuBadge = !isSend && !msg.read
            ? '<span class="non-lu-indicator">Non lu</span>'
            : '';

        html += `
        <div class="message-item ${className}">
            <div class="message-bubble">${msg.text}</div>
            <div class="message-meta">
                <span>${date}</span>
                ${nonLuBadge}
            </div>
        </div>`;
    });

    document.getElementById('messages-liste').innerHTML = html;
}

async function sendReply() {
    const token = localStorage.getItem('token');
    const texte = document.getElementById('texte-reponse').value.trim();

    if (!texte) return;

    try {
        const response = await fetch(
            `${API}/annonces/${conversationActive.idAdvertisement}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({
                text: texte,
                id_recipient: conversationActive.idCorrespondant  
            })
        }
        );

        if (response.ok) {
            document.getElementById('texte-reponse').value = '';
            await loadMessages(conversationActive.idAdvertisement, conversationActive.idCorrespondant);
        } else {
            const data = await response.json();
            document.getElementById('msg-detail').textContent = data.error || 'Erreur';
        }

    } catch (error) {
        document.getElementById('msg-detail').textContent = 'Erreur serveur';
    }
}

function prepareDeleteConversation(idAdvertisement, idCorrespondant) {
    conversationActive = { idAdvertisement, idCorrespondant };
    openDeleteModal();
}

function openDeleteModal() {
    document.getElementById('modal-supprimer').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('modal-supprimer').classList.remove('active');
}

async function confirmDeleteConversation() {
    const token = localStorage.getItem('token');

    try {
        const res = await fetch(
            `${API}/conversations/${conversationActive.idAdvertisement}/${conversationActive.idCorrespondant}`,
            {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            }
        );

        closeDeleteModal();

        if (res.ok) {
            goBackToList();
            showGlobalMessage('Conversation supprimée', 'success');
            fetchConversations();
        } else {
            showGlobalMessage('Erreur lors de la suppression', 'danger');
        }

    } catch (error) {
        closeDeleteModal();
        showGlobalMessage('Impossible de contacter le serveur', 'danger');
    }
}

function goBackToList() {
    document.getElementById('page-detail').classList.remove('active');
    document.getElementById('page-liste').classList.remove('hidden');
    conversationActive = null;
    fetchConversations();
}

function showGlobalMessage(message, type) {
    const el = document.getElementById('msg-global');
    el.textContent = message;
    el.className = `alert alert-${type}`;
    el.classList.remove('d-none');

    setTimeout(() => el.classList.add('d-none'), 4000);
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('id_user');
    window.location.href = 'pageConnexion.html';
}

document.getElementById('modal-supprimer').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});

init();