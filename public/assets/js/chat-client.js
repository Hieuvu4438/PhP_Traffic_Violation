document.addEventListener('DOMContentLoaded', function () {
    const config = window.chatConfig || {};
    const startForm = document.querySelector('#chatStartForm');
    const sendForm = document.querySelector('#chatSendForm');
    const chatBox = document.querySelector('#chatBox');
    const chatEmpty = document.querySelector('#chatEmpty');
    const chatError = document.querySelector('#chatError');
    const chatStatus = document.querySelector('#chatStatus');
    const storageKey = 'traffic_chat_guest_token';
    let conversationId = Number(config.conversationId || 0);
    let guestToken = config.isLoggedIn ? '' : (config.guestToken || localStorage.getItem(storageKey) || '');
    let lastMessageId = 0;
    let pollingStarted = false;

    function showError(message) {
        if (!chatError) return;
        chatError.textContent = message;
        chatError.classList.remove('d-none');
    }

    function clearError() {
        if (!chatError) return;
        chatError.classList.add('d-none');
        chatError.textContent = '';
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function appendMessage(message) {
        if (!chatBox) return;
        if (chatEmpty) chatEmpty.remove();

        const isUser = message.sender_type === 'user';
        const wrapper = document.createElement('div');
        wrapper.className = `d-flex mb-3 ${isUser ? 'justify-content-end' : 'justify-content-start'}`;
        wrapper.innerHTML = `
            <div class="${isUser ? 'bg-primary text-white' : 'bg-light border'} rounded px-3 py-2" style="max-width: 75%;">
                <div class="small ${isUser ? 'text-white-50' : 'text-muted'} mb-1">${isUser ? 'Bạn' : 'Admin'}</div>
                <div>${escapeHtml(message.message).replaceAll('\n', '<br>')}</div>
                <div class="small ${isUser ? 'text-white-50' : 'text-muted'} mt-1">${escapeHtml(message.created_at || '')}</div>
            </div>
        `;
        chatBox.appendChild(wrapper);
        lastMessageId = Math.max(lastMessageId, Number(message.id));
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function rememberConversation(data) {
        conversationId = Number(data.conversation_id || 0);
        guestToken = data.guest_token || guestToken;
        if (!config.isLoggedIn && guestToken) localStorage.setItem(storageKey, guestToken);
    }

    if (config.guestToken) rememberConversation(config);

    function startPolling() {
        if (pollingStarted || !conversationId) return;
        pollingStarted = true;
        loadMessages();
        setInterval(loadMessages, 4000);
    }

    async function restoreConversation() {
        if (conversationId || !guestToken) return false;

        const formData = new FormData();
        formData.append('csrf_token', config.csrfToken || '');
        formData.append('guest_token', guestToken);

        try {
            const resp = await fetch(config.restoreUrl, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });
            const data = await resp.json();
            if (!data.success) return false;

            rememberConversation(data);
            if (startForm) startForm.style.display = 'none';
            if (chatBox) chatBox.style.display = '';
            if (sendForm) sendForm.style.display = '';
            startPolling();
            return true;
        } catch (err) {
            return false;
        }
    }

    async function ensureConversation() {
        if (conversationId) return true;

        const formData = new FormData();
        formData.append('csrf_token', config.csrfToken || '');

        const resp = await fetch(config.startUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        });
        const data = await resp.json();
        if (!data.success) {
            showError(data.message || 'Không thể bắt đầu chat.');
            return false;
        }

        rememberConversation(data);
        if (chatBox) chatBox.style.display = '';
        if (sendForm) sendForm.style.display = '';
        return true;
    }

    async function loadMessages() {
        if (!conversationId) return;

        try {
            const url = `/chat/${conversationId}/messages?after_id=${lastMessageId}&guest_token=${encodeURIComponent(guestToken)}`;
            const resp = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await resp.json();
            if (!data.success) return;

            data.messages.forEach(appendMessage);
            if (data.status === 'closed') {
                if (chatStatus) chatStatus.textContent = 'Đã đóng';
                if (sendForm) sendForm.style.display = 'none';
            }
        } catch (err) {
            showError('Không thể tải tin nhắn mới.');
        }
    }

    if (startForm) {
        startForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearError();

            try {
                const resp = await fetch(config.startUrl, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(startForm)
                });
                const data = await resp.json();
                if (!data.success) {
                    showError(data.message || 'Không thể bắt đầu chat.');
                    return;
                }

                rememberConversation(data);
                startForm.style.display = 'none';
                if (chatBox) chatBox.style.display = '';
                if (sendForm) sendForm.style.display = '';
                startPolling();
            } catch (err) {
                showError('Lỗi kết nối, vui lòng thử lại.');
            }
        });
    }

    if (sendForm) {
        sendForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearError();

            try {
                if (!(await ensureConversation())) return;

                const formData = new FormData(sendForm);
                if (guestToken) formData.append('guest_token', guestToken);

                const resp = await fetch(`/chat/${conversationId}/send`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                });
                const data = await resp.json();
                if (!data.success) {
                    showError(data.message || 'Không thể gửi tin nhắn.');
                    return;
                }

                sendForm.reset();
                loadMessages();
            } catch (err) {
                showError('Lỗi kết nối, vui lòng thử lại.');
            }
        });
    }

    if (guestToken && !conversationId) {
        restoreConversation();
    }
    if (conversationId) {
        startPolling();
    }
});
