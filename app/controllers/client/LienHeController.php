<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ContactMessage;

class LienHeController extends Controller
{
    public function index(): void
    {
        $this->view('client/pages/lien-he', [
            'title' => 'Contact',
        ]);
    }

    public function send(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/lien-he');
            return;
        }

        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $subject = trim($this->input('subject', ''));
        $message = trim($this->input('message', ''));

        $validator = new Validator();
        $data = ['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message];
        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'subject' => 'required|min:3|max:255',
            'message' => 'required|min:10',
        ];

        if (!$validator->validate($data, $rules)) {
            Session::setFlash('error', $validator->firstError('name') ?? $validator->firstError('email') ?? $validator->firstError('subject') ?? $validator->firstError('message') ?? 'Invalid data.');
            $this->redirect('/lien-he');
            return;
        }

        $contactMessage = new ContactMessage();
        $contactMessage->create([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'is_read' => 0,
        ]);

        Session::setFlash('success', 'Thank you for your message. We will respond as soon as possible.');
        $this->redirect('/lien-he');
    }
}
