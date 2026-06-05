<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $page = (int) $this->input('page', 1);
        $data = (new ChatConversation())->paginateWithUnreadCount($page, 10);
        $data['title'] = 'Chat khách hàng';
        $data['baseUrl'] = '/admin/chat';
        $this->view('admin/chat/index', $data, 'admin');
    }

    public function show(int $id): void
    {
        $conversation = (new ChatConversation())->find($id);
        if (!$conversation) {
            Session::setFlash('error', 'Cuộc trò chuyện không tồn tại.');
            $this->redirect('/admin/chat');
        }

        (new ChatMessage())->markConversationRead($id, 'user');
        $this->view('admin/chat/show', [
            'title' => 'Chi tiết chat',
            'conversation' => $conversation,
        ], 'admin');
    }

    public function messages(int $id): void
    {
        $conversation = (new ChatConversation())->find($id);
        if (!$conversation) {
            $this->json(['success' => false, 'message' => 'Cuộc trò chuyện không tồn tại.'], 404);
        }

        $afterId = (int) $this->input('after_id', 0);
        $messageModel = new ChatMessage();
        $messageModel->markConversationRead($id, 'user');

        $this->json([
            'success' => true,
            'messages' => $messageModel->getByConversation($id, $afterId),
            'status' => $conversation['status'],
        ]);
    }

    public function reply(int $id): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Phiên làm việc hết hạn.'], 419);
        }

        $conversation = (new ChatConversation())->find($id);
        if (!$conversation) {
            $this->json(['success' => false, 'message' => 'Cuộc trò chuyện không tồn tại.'], 404);
        }
        if ($conversation['status'] === 'closed') {
            $this->json(['success' => false, 'message' => 'Cuộc trò chuyện đã đóng.'], 422);
        }

        $message = trim($this->input('message', ''));
        $validator = new Validator();
        if (!$validator->validate(['message' => $message], ['message' => 'required|min:1|max:2000'])) {
            $this->json(['success' => false, 'message' => $validator->firstError('message') ?? 'Tin nhắn không hợp lệ.'], 422);
        }

        $messageId = (new ChatMessage())->create([
            'conversation_id' => $id,
            'sender_type' => 'admin',
            'sender_id' => (int) Session::get('user_id'),
            'message' => $message,
            'is_read' => 0,
        ]);
        (new ChatConversation())->touchLastMessage($id);

        $this->json(['success' => true, 'message_id' => $messageId]);
    }

    public function close(int $id): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/admin/chat/' . $id);
        }

        $conversation = new ChatConversation();
        if (!$conversation->find($id)) {
            Session::setFlash('error', 'Cuộc trò chuyện không tồn tại.');
            $this->redirect('/admin/chat');
        }

        $conversation->close($id);
        Session::setFlash('success', 'Đã đóng cuộc trò chuyện.');
        $this->redirect('/admin/chat/' . $id);
    }
}
