<?php
namespace App\Controllers\Classroom;

use App\Controllers\BaseController;
use App\Models\Classroom\AttachmentModel;

class AttachmentController extends BaseController
{
    protected AttachmentModel $attachmentModel;

    public function __construct()
    {
        $this->attachmentModel = new AttachmentModel();
    }

    public function download($id)
    {
        $att = $this->attachmentModel->find($id);
        if (!$att) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lampiran tidak ditemukan');
        }
        // Basic permission: must be logged in. For draft lessons/assignments, rely on original resource restrictions (simplified here)
        if (!session('isLoggedIn')) return redirect()->to('/login');
        $path = WRITEPATH.'uploads/classroom/'.$att['stored_name'];
        if (!is_file($path)) {
            return redirect()->back()->with('error','File hilang');
        }
        return $this->response->download($path, null)->setFileName($att['original_name']);
    }
}
?>