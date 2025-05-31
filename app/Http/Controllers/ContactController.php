<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:4',
            'email' => 'required|email',
            'subject' => 'required|min:4',
            'message' => 'required',
        ]);

        // Kirim email atau simpan ke database (contoh sederhana kirim email)
        Mail::raw("Pesan dari: {$validated['name']} ({$validated['email']})\n\n{$validated['message']}", function ($message) use ($validated) {
            $message->to('tripujiantoro12@gmail.com')
                    ->subject($validated['subject'])
                    ->from($validated['email'], $validated['name']);
        });

        return back()->with('success', 'Pesan berhasil dikirim!');
    }
}
