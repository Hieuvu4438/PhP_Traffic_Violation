document.addEventListener('DOMContentLoaded', function () {
    const config = window.adminChatConfig || {};
    const conversationId = Number(config.conversationId || 0);
    const replyForm = document.querySelector('#adminChatReplyForm');
    const chatBox = document.querySelector('#chatBox');
    const chatEmpty = document.querySelector('#chatEmpty');
    const chatError = document.querySelector('#chatError');
    const chatStatus = document.querySelector('#chatStatus');
    let lastMessageId = 0;

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

        const isAdmin = message.sender_type === 'admin';
        const wrapper = document.createElement('div');
        wrapper.className = `d-flex mb-3 ${isAdmin ? 'justify-content-end' : 'justify-content-start'}`;
        wrapper.innerHTML = `
            <div class="${isAdmin ? 'bg-primary text-white' : 'bg-white border'} rounded px-3 py-2" style="max-width: 75%;">
                <div class="small ${isAdmin ? 'text-white-50' : 'text-muted'} mb-1">${isAdmin ? 'Admin' : 'Customer'}</div>
                <div>${escapeHtml(message.message).replaceAll('\n', '<br>')}</div>
                <div class="small ${isAdmin ? 'text-white-50' : 'text-muted'} mt-1">${escapeHtml(message.created_at || '')}</div>
            </div>
        `;
        chatBox.appendChild(wrapper);
        lastMessageId = Math.max(lastMessageId, Number(message.id));
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function loadMessages() {
        if (!conversationId) return;

        try {
            const resp = await fetch(`/admin/chat/${conversationId}/messages?after_id=${lastMessageId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await resp.json();
            if (!data.success) return;

            data.messages.forEach(appendMessage);
            if (data.status === 'closed') {
                if (chatStatus) chatStatus.textContent = 'Closed';
                if (replyForm) replyForm.style.display = 'none';
            }
        } catch (err) {
            showError('Unable to load new messages.');
        }
    }

    if (replyForm) {
        replyForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearError();

            try {
                const resp = await fetch(`/admin/chat/${conversationId}/reply`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(replyForm)
                });
                const data = await resp.json();
                if (!data.success) {
                    showError(data.message || 'Unable to send reply.');
                    return;
                }

                replyForm.reset();
                loadMessages();
            } catch (err) {
                showError('Connection error, please try again.');
            }
        });
    }

    loadMessages();
    setInterval(loadMessages, 4000);
});
