<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    /**
     * Tampilkan Halaman FAQ & Pusat Bantuan.
     */
    public function faq()
    {
        $faqs = [
            'booking' => [
                'title' => 'Reservasi & Sesi Foto',
                'items' => [
                    [
                        'q' => 'Bagaimana cara melakukan booking sesi foto di LensMatch?',
                        'a' => 'Pilih studio fotografer pada halaman Katalog Fotografer, pilih paket layanan yang diinginkan, tentukan tanggal & lokasi pemotretan, lalu klik tombol "Booking Sesi Foto Ini".'
                    ],
                    [
                        'q' => 'Apakah saya bisa membatalkan jadwal booking yang sudah disetujui?',
                        'a' => 'Pembatalan jadwal booking dapat diajukan maksimal 24 jam sebelum tanggal sesi foto melalui dashboard "Reservasi Saya" dengan memilih konfirmasi persetujuan dari pihak studio.'
                    ],
                    [
                        'q' => 'Bagaimana cara mengunduh hasil foto dari fotografer?',
                        'a' => 'Setelah sesi pemotretan selesai dan fotografer mengunggah hasil karya, Anda dapat mengakses tautan galeri resolusi tinggi langsung di menu "Galeri Hasil Foto Saya".'
                    ],
                ]
            ],
            'payment' => [
                'title' => 'Pembayaran & Sistem Escrow',
                'items' => [
                    [
                        'q' => 'Apa itu Sistem Pembayaran Rekening Bersama (Escrow)?',
                        'a' => 'Sistem Escrow LensMatch mengamankan dana pembayaran Anda di rekening bersama platform. Dana baru akan diteruskan ke rekening fotografer setelah sesi pemotretan dan penyerahan foto karya selesai.'
                    ],
                    [
                        'q' => 'Metode pembayaran apa saja yang didukung?',
                        'a' => 'LensMatch mendukung pembayaran serba otomatis via QRIS (BCA, Mandiri, GoPay, OVO, ShopeePay) serta Transfer Bank Rekening Bersama BCA Escrow.'
                    ],
                    [
                        'q' => 'Apakah ada biaya tambahan transaksi bagi Klien?',
                        'a' => 'Tidak ada biaya tersembunyi. Seluruh harga paket layanan yang tertera bersifat transparan dan sudah termasuk biaya perlindungan escrow platform.'
                    ],
                ]
            ],
            'verification' => [
                'title' => 'Akun & Verifikasi Studio',
                'items' => [
                    [
                        'q' => 'Bagaimana cara fotografer mendapatkan badge Verified Studio Centang Biru?',
                        'a' => 'Studio fotografer dapat mengajukan verifikasi profil dengan melengkapi identitas resmi, alamat studio, dan minimal 3 portofolio karya asli. Tim Admin LensMatch akan me-review dan memberikan centang biru.'
                    ],
                    [
                        'q' => 'Apakah saya bisa mendaftar sebagai Klien sekaligus Fotografer?',
                        'a' => 'Setiap akun didaftarkan sesuai role spesifik (Klien atau Fotografer). Jika ingin mendaftar sebagai penyedia jasa foto, Anda dapat mendaftarkan akun baru dengan role Fotografer.'
                    ],
                ]
            ],
            'contests' => [
                'title' => 'Kompetisi Lomba Foto',
                'items' => [
                    [
                        'q' => 'Siapa saja yang dapat mengikuti event Lomba Foto di LensMatch?',
                        'a' => 'Seluruh pengguna terdaftar di platform LensMatch (Klien maupun Fotografer) dapat mendaftarkan karya foto terbaiknya secara gratis pada event lomba foto yang terbuka.'
                    ],
                    [
                        'q' => 'Bagaimana proses penetapan dan pengumuman pemenang lomba?',
                        'a' => 'Tim juri profesional dan Admin me-review seluruh submisi karya foto berdasarkan kriteria estetika dan kesesuaian tema. Pemenang Juara 1, 2, dan 3 akan ditampilkan pada Panggung Kehormatan.'
                    ],
                ]
            ]
        ];

        return view('public.help.faq', compact('faqs'));
    }

    /**
     * Tampilkan Halaman Kebijakan Privasi & Syarat Ketentuan.
     */
    public function privacy()
    {
        return view('public.help.privacy');
    }
}