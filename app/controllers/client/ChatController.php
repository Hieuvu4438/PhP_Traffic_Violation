<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function index(): void
    {
        $conversationModel = new ChatConversation();
        $conversation = null;
        if (Session::isLoggedIn()) {
            $conversation = $conversationModel->findOpenByUser((int) Session::get('user_id'));
        } else {
            $conversationId = (int) Session::get('chat_conversation_id', 0);
            $conversation = $conversationId > 0 ? $conversationModel->find($conversationId) : null;
            if ($conversation && $conversation['status'] === 'closed') {
                $conversation = null;
            }
            if ($conversation && empty($conversation['guest_token'])) {
                $conversation['guest_token'] = bin2hex(random_bytes(32));
                $conversationModel->update((int) $conversation['id'], ['guest_token' => $conversation['guest_token']]);
            }
        }

        $this->view('client/chat/index', [
            'title' => 'Support Chat',
            'conversation' => $conversation,
        ]);
    }

    public function start(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Session has expired.'], 419);
        }

        $conversationModel = new ChatConversation();

        if (Session::isLoggedIn()) {
            $conversation = $conversationModel->findOpenByUser((int) Session::get('user_id'));
            if ($conversation) {
                $this->json(['success' => true, 'conversation_id' => (int) $conversation['id'], 'guest_token' => null]);
            }

            $conversationId = $conversationModel->create([
                'user_id' => (int) Session::get('user_id'),
                'guest_name' => Session::get('user_name'),
                'guest_email' => Session::get('user_email'),
                'status' => 'open',
                'last_message_at' => date('Y-m-d H:i:s'),
            ]);

            $this->json(['success' => true, 'conversation_id' => $conversationId, 'guest_token' => null]);
        }

        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $phone = trim($this->input('phone', ''));
        $validator = new Validator();

        if (!$validator->validate(['name' => $name, 'email' => $email], [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
        ])) {
            $this->json(['success' => false, 'message' => $validator->firstError('name') ?? $validator->firstError('email') ?? 'Invalid data.'], 422);
        }

        if ($phone !== '' && !Validator::phone($phone)) {
            $this->json(['success' => false, 'message' => 'Invalid phone number format.'], 422);
        }

        $guestToken = bin2hex(random_bytes(32));
        $conversationId = $conversationModel->create([
            'guest_name' => $name,
            'guest_email' => $email,
            'guest_phone' => $phone ?: null,
            'guest_token' => $guestToken,
            'status' => 'open',
            'last_message_at' => date('Y-m-d H:i:s'),
        ]);
        Session::set('chat_conversation_id', $conversationId);

        $this->json(['success' => true, 'conversation_id' => $conversationId, 'guest_token' => $guestToken]);
    }

    public function restore(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Session has expired.'], 419);
        }

        $token = trim($this->input('guest_token', ''));
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            $this->json(['success' => false, 'message' => 'Chat history not found.'], 404);
        }

        $conversation = (new ChatConversation())->findOpenByGuestToken($token);
        if (!$conversation) {
            $this->json(['success' => false, 'message' => 'Chat history not found.'], 404);
        }

        Session::set('chat_conversation_id', (int) $conversation['id']);
        $this->json(['success' => true, 'conversation_id' => (int) $conversation['id'], 'guest_token' => $token]);
    }

    public function messages(int $id): void
    {
        $conversation = $this->findAllowedConversation($id);
        if (!$conversation) {
            $this->json(['success' => false, 'message' => 'Conversation does not exist.'], 404);
        }

        $afterId = (int) $this->input('after_id', 0);
        $messageModel = new ChatMessage();
        $messageModel->markConversationRead($id, 'admin');

        $this->json([
            'success' => true,
            'messages' => $messageModel->getByConversation($id, $afterId),
            'status' => $conversation['status'],
        ]);
    }

    public function send(int $id): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Session has expired.'], 419);
        }

        $conversation = $this->findAllowedConversation($id);
        if (!$conversation) {
            $this->json(['success' => false, 'message' => 'Conversation does not exist.'], 404);
        }
        if ($conversation['status'] === 'closed') {
            $this->json(['success' => false, 'message' => 'Conversation is closed.'], 422);
        }

        $message = trim($this->input('message', ''));
        $validator = new Validator();
        if (!$validator->validate(['message' => $message], ['message' => 'required|min:1|max:2000'])) {
            $this->json(['success' => false, 'message' => $validator->firstError('message') ?? 'Invalid message.'], 422);
        }

        $messageModel = new ChatMessage();
        $messageId = $messageModel->create([
            'conversation_id' => $id,
            'sender_type' => 'user',
            'sender_id' => Session::isLoggedIn() ? (int) Session::get('user_id') : null,
            'message' => $message,
            'is_read' => 0,
        ]);
        (new ChatConversation())->touchLastMessage($id);

        $this->json(['success' => true, 'message_id' => $messageId]);
    }

    private function findAllowedConversation(int $id): ?array
    {
        $conversation = (new ChatConversation())->find($id);
        if (!$conversation) {
            return null;
        }

        if (Session::isLoggedIn()) {
            return (int) $conversation['user_id'] === (int) Session::get('user_id') ? $conversation : null;
        }

        if ((int) Session::get('chat_conversation_id', 0) === $id) {
            return $conversation;
        }

        $token = trim($this->input('guest_token', ''));
        return $token !== '' && hash_equals((string) ($conversation['guest_token'] ?? ''), $token) ? $conversation : null;
    }
}
