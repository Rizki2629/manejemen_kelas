<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Settings'
        ];
        
        return view('admin/settings/index', $data);
    }

    public function update()
    {
        // General settings update
        $validation = \Config\Services::validation();
        $validation->setRules([
            'setting_key' => 'required',
            'setting_value' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Update general settings logic here
        return redirect()->to('/admin/settings')->with('success', 'Pengaturan berhasil diperbarui');
    }

    public function updateSchoolInfo()
    {
        // School information update
        $validation = \Config\Services::validation();
        $validation->setRules([
            'school_name' => 'required|min_length[3]|max_length[100]',
            'school_address' => 'required|max_length[255]',
            'school_phone' => 'permit_empty|numeric',
            'school_email' => 'permit_empty|valid_email',
            'principal_name' => 'permit_empty|max_length[100]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Update school information logic here
        return redirect()->to('/admin/settings')->with('success', 'Informasi sekolah berhasil diperbarui');
    }

    public function updateSystem()
    {
        // System settings update
        $validation = \Config\Services::validation();
        $validation->setRules([
            'timezone' => 'required',
            'date_format' => 'required',
            'language' => 'required',
            'maintenance_mode' => 'permit_empty|in_list[0,1]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Update system settings logic here
        return redirect()->to('/admin/settings')->with('success', 'Pengaturan sistem berhasil diperbarui');
    }
}
