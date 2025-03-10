<?php
// app/Services/WhatsAppService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    // Fungsi untuk mengirimkan pesan WhatsApp
    public function sendWhatsAppMessage($target, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => 'E1MNX4umGjXe4QW55tBq', // Ganti dengan API key yang sesuai
        ])->post('https://api.fonnte.com/send', [
            'target' => $target, // Nomor tujuan
            'message' => $message, // Pesan yang akan dikirim
        ]);

        return json_decode($response, true); // Mengembalikan response dalam format array
    }
}
