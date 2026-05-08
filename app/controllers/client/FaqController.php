<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index(): void
    {
        $faqModel = new Faq();
        $faqs = $faqModel->getActive();

        $this->view('client/faq/index', [
            'title' => 'Câu hỏi thường gặp',
            'faqs' => $faqs,
        ]);
    }
}
