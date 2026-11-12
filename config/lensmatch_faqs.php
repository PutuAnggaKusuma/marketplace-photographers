<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FAQ Bawaan Sistem LensMatch (Hardcoded Terpusat — Opsi A)
    |--------------------------------------------------------------------------
    | Daftar ketentuan platform yang dikelola langsung di kode. Digunakan untuk
    | halaman Pusat Bantuan (/faq) serta ditampilkan otomatis pada bagian
    | Ketentuan Sistem LensMatch di Tab Tanya Jawab profil publik fotografer.
    */

    'profile_system_faqs' => [
        [
            'id' => 'sys_booking_flow',
            'pertanyaan' => 'Bagaimana alur pemesanan sesi foto di LensMatch?',
            'jawaban' => 'Tekan tombol Reservasi pada profil fotografer, pilih paket layanan, tanggal, jam tersedia, serta lokasi pemotretan. Setelah invoice terbit, unggah bukti transfer agar jadwal diproses dan dikonfirmasi.',
        ],
        [
            'id' => 'sys_payment_safety',
            'pertanyaan' => 'Apakah tersedia opsi uang muka (DP) dan bagaimana sistem pembayarannya?',
            'jawaban' => 'Saat memesan sesi, Anda dapat memilih Pembayaran Penuh atau Uang Muka (DP 30%) melalui transfer bank ke rekening resmi PT LensMatch Indonesia tanpa biaya layanan platform bagi klien. Dana ditahan sistem dan diteruskan ke fotografer setelah hasil foto Anda konfirmasi.',
        ],
        [
            'id' => 'sys_cancel_reschedule',
            'pertanyaan' => 'Bagaimana ketentuan pembatalan atau perubahan jadwal sesi?',
            'jawaban' => 'Pembatalan mandiri dapat diajukan maksimal 24 jam sebelum jadwal sesi melalui menu Reservasi Saya. Klien maupun fotografer juga dapat mengajukan jadwal ulang ke slot tersedia, yang berlaku setelah disetujui kedua pihak.',
        ],
        [
            'id' => 'sys_photo_delivery',
            'pertanyaan' => 'Bagaimana cara menerima hasil foto setelah sesi selesai?',
            'jawaban' => 'Setelah fotografer mengunggah tautan galeri foto, Anda dapat membuka dan mengunduh hasilnya melalui menu Galeri Saya lalu menekan konfirmasi penerimaan.',
        ],
    ],

    'help_categories' => [
        'booking' => [
            'title' => 'Reservasi Sesi',
            'items' => [
                [
                    'q' => 'Bagaimana cara melakukan reservasi sesi foto di LensMatch?',
                    'a' => 'Buka profil fotografer di Katalog Fotografer, tekan tombol Reservasi, pilih paket layanan, tentukan tanggal dan jam tersedia, lalu isi lokasi pemotretan. Setelah invoice diterbitkan, unggah bukti transfer agar pesanan segera diproses.',
                ],
                [
                    'q' => 'Apakah jadwal booking yang sudah diajukan dapat dibatalkan?',
                    'a' => 'Pembatalan mandiri dapat diajukan maksimal 24 jam sebelum waktu sesi melalui menu Reservasi Saya. Pengembalian dana atas pembayaran yang sudah masuk akan diproses melalui peninjauan Admin.',
                ],
                [
                    'q' => 'Bagaimana jika ingin mengubah tanggal atau jam pemotretan?',
                    'a' => 'Klien maupun fotografer dapat mengajukan jadwal ulang melalui menu Reservasi Saya ke tanggal dan jam lain yang tersedia. Jadwal baru berlaku setelah disetujui pihak terkait.',
                ],
                [
                    'q' => 'Bagaimana cara mengunduh hasil foto dari fotografer?',
                    'a' => 'Setelah fotografer mengunggah tautan galeri foto, Anda dapat mengunduh dan mengonfirmasi penerimaan hasil melalui menu Galeri Saya.',
                ],
            ],
        ],
        'payment' => [
            'title' => 'Pembayaran',
            'items' => [
                [
                    'q' => 'Bagaimana mekanisme penahanan dana di LensMatch?',
                    'a' => 'Dana pembayaran klien ditahan sementara di rekening resmi PT LensMatch Indonesia selama sesi berlangsung dan baru diteruskan ke fotografer setelah pemotretan selesai serta hasil foto dikonfirmasi klien.',
                ],
                [
                    'q' => 'Skema dan metode pembayaran apa yang tersedia?',
                    'a' => 'Klien dapat memilih Pembayaran Penuh atau Uang Muka (DP 30%) saat reservasi. Pembayaran dilakukan melalui transfer bank ke rekening resmi PT LensMatch Indonesia yang tertera di halaman invoice.',
                ],
                [
                    'q' => 'Apakah ada biaya layanan platform bagi klien?',
                    'a' => 'Tidak ada biaya layanan platform bagi klien. Nominal tagihan pada invoice merupakan total tarif paket layanan dan fitur tambahan yang Anda pilih.',
                ],
            ],
        ],
        'verification' => [
            'title' => 'Verifikasi Fotografer',
            'items' => [
                [
                    'q' => 'Bagaimana cara fotografer mendapatkan status Terverifikasi?',
                    'a' => 'Fotografer perlu melengkapi 100% data profil (kontak, domisili, kategori keahlian, bio, foto profil, foto sampul, dan rekening bank) lalu menunggu verifikasi Admin. Setelah terverifikasi, fotografer melengkapi paket layanan, portofolio, dan jadwal kerja untuk mengajukan tayang di katalog.',
                ],
                [
                    'q' => 'Bagaimana standar penilaian performa fotografer dihitung?',
                    'a' => 'Penilaian dihitung dari data transaksi nyata: Rating dan Ulasan berasal dari klien yang telah menyelesaikan sesi, jumlah sesi selesai dihitung dari kontrak tuntas, dan indikator Respon Cepat aktif jika fotografer aktif dalam 24 jam terakhir.',
                ],
                [
                    'q' => 'Apakah satu akun bisa digunakan sebagai Klien sekaligus Fotografer?',
                    'a' => 'Setiap akun hanya berlaku untuk satu peran (Klien atau Fotografer). Gunakan akun terpisah jika ingin memesan sesi sekaligus menawarkan jasa foto.',
                ],
            ],
        ],
        'contests' => [
            'title' => 'Lomba Foto',
            'items' => [
                [
                    'q' => 'Siapa yang dapat mengikuti lomba foto di LensMatch?',
                    'a' => 'Pengguna terdaftar di LensMatch dapat mendaftar dan mengirimkan karya foto sesuai ketentuan lomba yang sedang dibuka.',
                ],
                [
                    'q' => 'Bagaimana proses penjurian dan pengumuman pemenang lomba?',
                    'a' => 'Admin meninjau seluruh karya peserta sesuai tema dan kriteria penilaian. Daftar pemenang ditampilkan langsung pada halaman detail lomba terkait.',
                ],
            ],
        ],
    ],
];
