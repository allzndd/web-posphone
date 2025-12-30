<?php

namespace App\Http\Controllers;

class ManageProfilController extends Controller
{
    public function contactAdmin()
    {
        $contacts = [
            [
                'label' => 'Primary WhatsApp',
                'value' => '+62 812-3456-7890',
                'note' => 'Available Monday to Friday, 09:00-18:00 WIB.',
            ],
            [
                'label' => 'Backup Phone',
                'value' => '+62 813-9876-5432',
                'note' => 'Use this number if the primary line is busy.',
            ],
            [
                'label' => 'Email Support',
                'value' => 'support@posphone.id',
                'note' => 'We reply within one business day.',
            ],
        ];

        return view('manage-profil.contact-admin.index', compact('contacts'));
    }
}
