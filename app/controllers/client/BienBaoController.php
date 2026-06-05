<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Models\TrafficSign;
use App\Models\TrafficSignGroup;

class BienBaoController extends Controller
{
    public function index(): void
    {
        $signModel = new TrafficSign();
        $groupModel = new TrafficSignGroup();

        $groupId = $this->input('group', '');
        $keyword = trim($this->input('q', ''));

        $groups = $groupModel->getAllSorted();

        if ($keyword !== '') {
            $signs = $signModel->search($keyword);
            // Group search results
            $signsByGroup = [];
            foreach ($signs as $sign) {
                $groupName = $sign['group_name'] ?? 'Other';
                $signsByGroup[$groupName][] = $sign;
            }
        } elseif ($groupId !== '') {
            $signs = $signModel->findByGroup((int) $groupId);
            $signsByGroup = ['' => $signs];
        } else {
            $allSigns = $signModel->getWithGroup();
            $signsByGroup = [];
            foreach ($allSigns as $sign) {
                $groupName = $sign['group_name'] ?? 'Other';
                $signsByGroup[$groupName][] = $sign;
            }
        }

        $this->view('client/bienbao/index', [
            'title' => 'Traffic Sign Lookup',
            'signsByGroup' => $signsByGroup,
            'groups' => $groups,
            'currentGroup' => $groupId,
            'keyword' => $keyword,
        ]);
    }

    public function detail(int $id): void
    {
        $signModel = new TrafficSign();
        $groupModel = new TrafficSignGroup();

        $sign = $signModel->find($id);
        if (!$sign) {
            Session::setFlash('error', 'Traffic sign does not exist.');
            $this->redirect('/bien-bao');
            return;
        }

        $group = $groupModel->find($sign['group_id'] ?? 0);
        $related = $signModel->findByGroup($sign['group_id'] ?? 0);

        $this->view('client/bienbao/detail', [
            'title' => 'Traffic Sign ' . htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8'),
            'sign' => $sign,
            'group' => $group,
            'related' => $related,
        ]);
    }
}
