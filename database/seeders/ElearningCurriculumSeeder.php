<?php

namespace Database\Seeders;

use App\Models\Elearning;
use App\Models\ElearningModule;
use App\Models\ElearningLesson;
use Illuminate\Database\Seeder;

class ElearningCurriculumSeeder extends Seeder
{
    /**
     * Seed realistic curriculum topics & lessons for Elearning courses.
     */
    public function run(): void
    {
        $courses = Elearning::all();

        foreach ($courses as $course) {
            // Set default harga & premium untuk demo
            $course->update([
                'harga' => 149000,
                'is_premium' => true,
            ]);

            // Jika modul belum ada, kita buat modul & pelajarannya
            if (!$course->modules()->exists()) {
                if ($course->slug === 'strategi-manajemen-bisnis-studio-escrow-marketplace') {
                    // MODUL 1: Dasar & Pondasi Bisnis Studio Fotografi
                    $modul1 = ElearningModule::create([
                        'id_elearning' => $course->id,
                        'judul_modul' => 'Dasar & Pondasi Bisnis Studio Fotografi',
                        'deskripsi' => 'Mempelajari model bisnis studio, struktur biaya, dan strategi penentuan harga jasa fotografi yang menguntungkan.',
                        'urutan' => 1,
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modul1->id,
                        'judul' => 'Pengenalan Model Bisnis Studio & Valuasi Pasar',
                        'tipe' => 'video',
                        'konten' => "Pada sesi pembuka ini, kita akan membedah ekosistem industri kreatif fotografi modern dan transformasi studio foto komersial. Anda akan diajak memahami bagaimana merintis studio yang terstruktur rapi, mulai dari identifikasi target audiens bernilai tinggi hingga pembangunan sistem operasional yang berkelanjutan.\n\nKita juga mengulas analisis valuasi pasar jasa visual, strategi diferensiasi portofolio di tengah kompetisi yang ketat, serta pentingnya menjaga cash flow yang stabil agar studio mampu bertahan dan berkembang dalam jangka panjang.",
                        'media_type' => 'youtube',
                        'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'durasi_menit' => 8,
                        'urutan' => 1,
                        'is_free' => true, // Free preview
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modul1->id,
                        'judul' => 'Penyusunan Price List & Perhitungan HPP Jasa',
                        'tipe' => 'reading',
                        'konten' => "Banyak fotografer pemula terjebak dalam perang harga karena tidak menghitung Harga Pokok Penjualan (HPP) jasa secara terperinci. Struktur biaya studio wajib mencakup depresiasi alat (kamera, lensa, lighting), alokasi upah jam kerja fotografer dan editor, biaya operasional cloud storage, asuransi, serta lisensi perangkat lunak.\n\nDengan menerapkan formula HPP yang tepat ditambah target profit margin sehat minimal 35% hingga 50%, Anda dapat menyusun paket harga yang kompetitif tanpa merugikan keberlangsungan finansial studio Anda di masa mendatang.",
                        'durasi_menit' => 12,
                        'urutan' => 2,
                        'is_free' => true, // Free preview
                    ]);

                    // MODUL 2: Sistem Escrow & Manajemen Keuangan
                    $modul2 = ElearningModule::create([
                        'id_elearning' => $course->id,
                        'judul_modul' => 'Sistem Transaksi Aman Escrow & Perlindungan Kontrak',
                        'deskripsi' => 'Strategi mengamankan dana DP pembayaran melalui sistem escrow LensMatch serta klausul kontrak legal perlindungan fotografer.',
                        'urutan' => 2,
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modul2->id,
                        'judul' => 'Podcast Audio: Mengapa Sistem Rekening Bersama Escrow Melindungi Studio?',
                        'tipe' => 'audio',
                        'konten' => "Dengarkan diskusi eksklusif bersama praktisi hukum bisnis kreatif dan konsultan keuangan mengenai peran krusial sistem rekening bersama penampung (escrow). Rekaman ini menjelaskan bagaimana pembayaran berskema escrow memberikan kepastian pencairan dana bagi fotografer sekaligus membangun rasa aman bagi klien.\n\nPelajari langkah-langkah mitigasi risiko gagal bayar, penanganan pembatalan jadwal sepihak, dan cara memanfaatkan invoice digital LensMatch sebagai bukti sah penerimaan order pekerjaan fotografi komersial.",
                        'media_type' => 'external',
                        'media_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                        'durasi_menit' => 15,
                        'urutan' => 3,
                        'is_free' => false, // Paid / Locked
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modul2->id,
                        'judul' => 'Bedah Format Kontrak Perjanjian Layanan & Invoice Digital',
                        'tipe' => 'foto',
                        'konten' => "Halaman ini menyajikan struktur dokumen kontrak kerja sama dan invoice digital yang wajib dimiliki setiap fotografer profesional. Format kontrak ini memuat klausul perlindungan hak cipta non-komersial, batas toleransi revisi editing, serta ketentuan kompensasi bila terjadi kendala tak terduga (force majeure) di lapangan.\n\nPelajari setiap poin pasal perjanjian secara saksama agar Anda memiliki landasan hukum yang kuat sebelum memulai pekerjaan, sehingga hubungan kemitraan dengan klien senantiasa berjalan transparan dan profesional.",
                        'media_type' => 'external',
                        'media_url' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=1200&q=80',
                        'durasi_menit' => 10,
                        'urutan' => 4,
                        'is_free' => false, // Paid / Locked
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modul2->id,
                        'judul' => 'Video Masterclass: Menutup Negosiasi dengan Klien Korporat B2B',
                        'tipe' => 'video',
                        'konten' => "Pelajari teknik dan psikologi negosiasi proyek komersial bernilai puluhan juta rupiah bersama klien korporat berskala besar. Sesi video ini mengajarkan cara mempresentasikan proposal kerja yang berbobot, menyusun ruang lingkup deliverables yang terukur, dan menjawab keberatan anggaran secara elegan.\n\nAnda juga akan dibekali strategi menyepakati skema termin pembayaran uang muka dan pelunasan melalui platform escrow secara transparan, sehingga proses kerja sama terlindungi dari awal hingga tahap penyerahan aset akhir.",
                        'media_type' => 'youtube',
                        'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'durasi_menit' => 22,
                        'urutan' => 5,
                        'is_free' => false, // Paid / Locked
                    ]);

                } else {
                    $judulModulA = match ($course->slug) {
                        'teknik-lighting-portrait-outdoor-golden-hour' => 'Konsep Fundamental & Teori Pencahayaan',
                        'mastering-color-grading-lightroom-untuk-prewedding' => 'Prinsip Color Grading & Manipulasi Tone HSL',
                        'panduan-komposisi-foto-arsitektur-interior-studio' => 'Kaidah Geometri & Perspektif Ruang Arsitektur',
                        'teknik-foto-event-malam-hari-concert-stage' => 'Teknik Low-Light & Autofocus Panggung Konser',
                        'strategi-promosi-portofolio-negosiasi-klien' => 'Kurasi Portofolio & Branding Penjualan Jasa',
                        default => 'Konsep Fundamental & Teori',
                    };

                    $modulA = ElearningModule::create([
                        'id_elearning' => $course->id,
                        'judul_modul' => $judulModulA,
                        'deskripsi' => 'Pengenalan prinsip utama, persiapan peralatan, dan pemahaman teknis dasar.',
                        'urutan' => 1,
                    ]);

                    $kontenVideo = match ($course->slug) {
                        'teknik-lighting-portrait-outdoor-golden-hour' => "Pada sesi pengantar ini, kita akan mempelajari prinsip dasar pemanfaatan cahaya alami matahari saat momen golden hour untuk menghasilkan foto portrait yang dramatis dan berkarakter. Anda akan memahami bagaimana arah datangnya cahaya—mulai dari backlight, side light, hingga front light—dapat memberikan dimensi kedalaman dan tone warna emas yang lembut pada subjek foto.\n\nSelain memahami arah cahaya, kita juga akan membahas pemilihan titik waktu terbaik di sore hari serta antisipasi perubahan intensitas cahaya yang berlangsung sangat cepat. Simak seluruh penjelasan teknis mengenai pengaturan segitiga exposure dan penggunaan perlengkapan penunjang sebelum melangkah ke sesi pemotretan langsung di lapangan.",
                        'mastering-color-grading-lightroom-untuk-prewedding' => "Selamat datang di modul masterclass color grading Adobe Lightroom Classic khusus foto prewedding. Pada sesi ini, kita akan membahas alur kerja pembentukan tone warna khas sinematik dan romantis yang konsisten dari awal hingga akhir sesi pemotretan.\n\nAnda akan mempelajari logika dasar percampuran warna primer dan sekunder, pemilihan profil warna kamera yang tepat, serta strategi menjaga keaslian warna kulit (skin tone) agar tetap cerah dan bercahaya alami meskipun latar belakang diberi grading dramatis.",
                        'panduan-komposisi-foto-arsitektur-interior-studio' => "Memotret arsitektur dan interior studio membutuhkan pemahaman mendalam tentang perspektif ruang, garis pandang (leading lines), dan penataan bidang geometri. Pada sesi video pengantar ini, kita akan mempelajari kaidah dasar pembingkaian ruang agar ruangan terlihat luas, rapi, dan memiliki proporsi yang akurat.\n\nSimak teknik penempatan ketinggian kamera serta pemilihan focal length lensa ideal untuk menghindari distorsi perspektif yang sering membuat dinding atau pilar bangunan tampak miring.",
                        'teknik-foto-event-malam-hari-concert-stage' => "Pemotretan panggung konser dan event malam hari menyajikan dinamika visual yang sangat cepat dan kondisi pencahayaan yang menantang. Di modul pengantar ini, kita akan mempelajari cara membaca ritme panggung, mengantisipasi pergerakan performer, dan memanfaatkan lampu sorot panggung sebagai elemen visual yang memukau.\n\nAnda akan dibimbing memilih focal length lensa cepat (f/2.8 atau lebih besar) dan memahami strategi navigasi di area pit penonton untuk mendapatkan momen aksi panggung terbaik.",
                        'strategi-promosi-portofolio-negosiasi-klien' => "Keahlian fotografi yang luar biasa harus diimbangi dengan strategi pemasaran portofolio yang terarah. Pada video pengantar ini, kita akan membahas cara memilah karya terbaik (curation), menentukan niche fotografi yang paling dicari pasar, dan menyajikan portofolio yang memikat calon klien dalam hitungan detik.\n\nAnda akan mempelajari bagaimana menyusun narasi visual di balik setiap karya, memilih platform presentasi digital yang tepat, dan menarik klien potensial dengan positioning brand yang elegan.",
                        default => "Selamat datang di modul pembelajaran ini. Pada materi pengantar, kita akan mengulas fondasi utama, tujuan pembelajaran, dan kompetensi kunci yang akan Anda kuasai setelah menyelesaikan seluruh tahapan materi secara tuntas.\n\nSimak setiap penjelasan konsep dan demonstrasi visual yang disajikan agar Anda memiliki gambaran utuh sebelum mempraktikkannya langsung pada studi kasus dan latihan terapan di lapangan.",
                    };

                    $kontenReading = match ($course->slug) {
                        'teknik-lighting-portrait-outdoor-golden-hour' => "Pengaturan segitiga exposure (ISO, Aperture, Shutter Speed) memegang peranan krusial dalam pemotretan portrait outdoor dengan pencahayaan alami. Penggunaan aperture lebar seperti f/1.4 hingga f/2.8 sangat disarankan untuk mengisolasi subjek dari latar belakang sehingga menghasilkan efek depth of field dangkal (bokeh) yang halus dan memukau.\n\nUntuk mengimbangi backlight matahari yang kuat dan menghindari bayangan gelap pada area wajah subjek, manfaatkan reflector portabel bernuansa perak atau emas lembut sebagai sumber fill light. Pelajari pula teknik mengunci fokus pada mata model (Eye AF) serta pemilihan white balance bertemperatur hangat guna mempertahankan saturasi warna alami kulit.",
                        'mastering-color-grading-lightroom-untuk-prewedding' => "Warna foto adalah identitas utama dari sebuah studio prewedding. Modul edisi masterclass ini membedah teknik manipulasi HSL (Hue, Saturation, Luminance), Color Grading Wheels pada Adobe Lightroom Classic, serta teknik masking AI khusus area kulit untuk menghasilkan tone visual khas dokumenter berkelas internasional.\n\nKuasai cara mengontrol tone curves secara presisi untuk menciptakan kontras bayangan yang lembut (faded shadows) dan highlights yang hangat. Terapkan preset racikan sendiri secara efisien pada ratusan file foto RAW sekaligus tanpa mengorbankan konsistensi warna antar lokasi foto.",
                        'panduan-komposisi-foto-arsitektur-interior-studio' => "Foto interior dan bangunan membutuhkan ketelitian tinggi pada kesejajaran garis vertikal dan horizontal. Pelajari cara memanfaatkan lensa wide-angle dan tilt-shift, pemilihan ketinggian tripod (eye-level vs chest-level), serta penggabungan multiple exposure HDR untuk mengimbangi terang jendela dan sudut ruangan yang gelap.\n\nKuasai pula teknik staging interior seperti merapikan kabel, menata furnitur simetris, dan mematikan atau menyalakan lampu ambien ruangan guna membangun atmosfer visual yang hangat dan mewah bagi klien properti.",
                        'teknik-foto-event-malam-hari-concert-stage' => "Memotret di kondisi cahaya minim dengan lampu strobo panggung yang berganti warna cepat merupakan tantangan terbesar fotografer event. Modul ini mengajarkan teknik metering Spot, batas toleransi ISO aman pada sensor kamera modern, dan penggunaan shutter speed tinggi untuk membekukan aksi panggung yang dinamis.\n\nPelajari pula metode continuous autofocus (AF-C / AI Servo) dengan tracking titik fokus dinamis agar kamera selalu mengunci wajah vokalis atau musisi di tengah kerumunan tata lampu yang berkedip.",
                        'strategi-promosi-portofolio-negosiasi-klien' => "Bagaimana cara meyakinkan klien bahwa jasa foto Anda bernilai tinggi? Pelajari psikologi presentasi portofolio galeri karya, penyusunan Proposal Penawaran komersial yang rapi, dan teknik komunikasi responsif yang membangun kepercayaan klien sejak kontak awal.\n\nModul ini juga membedah cara menyusun paket layanan fleksibel (tiered pricing), memberikan penawaran nilai tambah tanpa menurunkan harga pokok, dan teknik follow-up calon klien yang sopan dan profesional.",
                        default => $course->konten ?: "Pembahasan teoritis mendalam mengenai teknik dan kaidah esensial yang harus dipahami oleh setiap praktisi fotografi profesional. Anda akan mempelajari parameter teknis kamera, komposisi visual, dan penyesuaian perangkat penunjang sesuai tema pemotretan.\n\nPastikan Anda memahami setiap prinsip fundamental ini sebagai dasar acuan sebelum melanjutkan ke tahap sesi produksi dan uji coba praktik mandiri.",
                    };

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modulA->id,
                        'judul' => 'Video Pengantar: ' . $course->judul,
                        'tipe' => 'video',
                        'konten' => $kontenVideo,
                        'media_type' => 'youtube',
                        'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'durasi_menit' => 7,
                        'urutan' => 1,
                        'is_free' => true,
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modulA->id,
                        'judul' => 'Panduan Tertulis & Teori Kunci',
                        'tipe' => 'reading',
                        'konten' => $kontenReading,
                        'durasi_menit' => 10,
                        'urutan' => 2,
                        'is_free' => true,
                    ]);

                    $judulModulB = match ($course->slug) {
                        'teknik-lighting-portrait-outdoor-golden-hour' => 'Sesi Praktik Terapan & Studi Kasus Lapangan',
                        'mastering-color-grading-lightroom-untuk-prewedding' => 'Workflow Editing Massal & Studi Kasus Prewedding',
                        'panduan-komposisi-foto-arsitektur-interior-studio' => 'Staging Interior & Praktik Foto Komersial',
                        'teknik-foto-event-malam-hari-concert-stage' => 'Manajemen Pit Panggung & Curation File Cepat',
                        'strategi-promosi-portofolio-negosiasi-klien' => 'Psikologi Negosiasi & Penutupan Kontrak Klien',
                        default => 'Sesi Praktik Terapan & Studi Kasus',
                    };

                    $modulB = ElearningModule::create([
                        'id_elearning' => $course->id,
                        'judul_modul' => $judulModulB,
                        'deskripsi' => 'Implementasi langsung di lapangan, panduan troubleshooting, dan tips efisiensi kerja.',
                        'urutan' => 2,
                    ]);

                    $kontenAudio = match ($course->slug) {
                        'teknik-lighting-portrait-outdoor-golden-hour' => "Dengarkan rekaman audio panduan lapangan yang membahas ragam studi kasus dan tantangan nyata saat memotret subjek portrait di lokasi terbuka. Sesi ini mengulas cara cepat beradaptasi terhadap perubahan cuaca mendadak, mengatasi flare lensa yang berlebihan, dan menjaga kenyamanan model selama sesi berlangsung.\n\nKami juga membagikan tips komunikasi efektif dalam mengarahkan pose subjek secara natural tanpa terkesan kaku. Putar rekaman ini sebagai referensi praktis yang dapat Anda dengarkan kapan saja saat bersiap menuju lokasi pemotretan.",
                        'mastering-color-grading-lightroom-untuk-prewedding' => "Dengarkan bedah kasus alur kerja post-processing foto prewedding dalam skala komersial berkecepatan tinggi. Rekaman audio ini membahas trik manajemen katalog Lightroom, pengaturan smart preview untuk laptop spek standar, dan alur batch export bebas hambatan.\n\nSelain itu, dibahas pula tips komunikasi menghadapi permintaan revisi tone warna dari klien prewedding serta cara membuat moodboard visual awal sebagai acuan kesepakatan warna sebelum hari H.",
                        'panduan-komposisi-foto-arsitektur-interior-studio' => "Dengarkan sesi diskusi praktis mengenai kiat mengelola sesi foto interior komersial di lokasi kerja yang aktif. Pembahasan mencakup persiapan daftar periksa peralatan wajib, komunikasi dengan tim desainer interior, hingga manajemen waktu saat memotret ruangan dengan pergantian cahaya luar.\n\nSimak pula trik cepat menyamarkan pantulan silau pada cermin dan permukaan kaca ruangan menggunakan filter circular polarizer (CPL) secara efektif.",
                        'teknik-foto-event-malam-hari-concert-stage' => "Simak panduan audio eksklusif seputar etika fotografer konser di dalam pit panggung, keselamatan peralatan di tengah kerumunan penonton, dan protokol kerja sama dengan tim promotor acara.\n\nRekaman ini juga mengulas tips membawa perlengkapan ringkas, penggunaan tali kamera pengaman ganda, serta strategi cepat memilih dan menyortir ratusan file foto aksi panggung untuk kebutuhan media sosial promotor di malam yang sama.",
                        'strategi-promosi-portofolio-negosiasi-klien' => "Dengarkan rekaman audio studi kasus negosiasi nyata dalam menghadapi berbagai tipe klien, mulai dari klien hemat anggaran hingga klien korporat dengan birokrasi penawaran yang ketat.\n\nPelajari kalimat-kalimat sakti untuk mempertahankan harga penawaran Anda, cara menjelaskan alasan di balik penetapan biaya jasa, dan trik mengubah penolakan awal menjadi kesepakatan kerja sama jangka panjang.",
                        default => "Dengarkan panduan audio terarah yang merangkum tips praktis dan pengalaman nyata dalam mengatasi berbagai kendala teknis saat sesi pemotretan berlangsung di lapangan.\n\nRekaman ini dirancang ringkas dan aplikatif sehingga dapat menjadi teman belajar yang fleksibel untuk didengarkan kapan saja dan di mana saja saat Anda mempersiapkan sesi foto berikutnya.",
                    };

                    $kontenFoto = match ($course->slug) {
                        'teknik-lighting-portrait-outdoor-golden-hour' => "Melalui galeri studi kasus visual ini, Anda dapat membedah langsung contoh karya foto portrait beresolusi tinggi yang diambil pada berbagai kondisi pencahayaan golden hour. Setiap visual dilengkapi dengan breakdown metadata exposure, komposisi framing rule of thirds, dan sudut elevasi kamera terhadap posisi matahari.\n\nPerhatikan detail gradasi transisi cahaya dari highlight ke shadow pada area wajah model, serta bagaimana kontras latar belakang tetap terjaga tanpa mengalami overexposure. Gunakan contoh-contoh visual ini sebagai standar acuan kualitas untuk sesi praktik mandiri Anda.",
                        'mastering-color-grading-lightroom-untuk-prewedding' => "Galeri ini menampilkan perbandingan langsung Before & After dari file RAW mentah hingga hasil ekspor akhir setelah melalui proses color grading mendalam. Perhatikan bagaimana pengolahan warna mengubah mood keseluruhan foto secara dramatis.\n\nAnalisis kurva warna, histogram exposure, dan pengaturan split toning yang disertakan pada setiap foto untuk memahami keputusan kreatif di balik setiap saturasi dan kontras yang dihasilkan.",
                        'panduan-komposisi-foto-arsitektur-interior-studio' => "Galeri studi kasus visual ini memuat bedah foto arsitektur interior berstandar majalah desain internasional. Analisis bagaimana penataan komposisi one-point perspective dan two-point perspective diterapkan pada ruangan berukuran kompak.\n\nSetiap foto dilengkapi panduan teknis aperture optimal (f/8 - f/11) untuk memastikan ketajaman merata dari sudut terdekat hingga latar belakang terjauh ruangan.",
                        'teknik-foto-event-malam-hari-concert-stage' => "Galeri foto resolusi tinggi ini menyajikan karya visual konser musik dengan efek lighting panggung yang dramatis, mulai dari siluet dramatis hingga freeze-motion percikan keringat performer.\n\nPelajari pengaturan ISO tinggi tanpa noise berlebih, manajemen saturasi warna merah dan biru pada lampu LED panggung, serta framing dinamis sudut rendah (low-angle).",
                        'strategi-promosi-portofolio-negosiasi-klien' => "Galeri panduan visual ini menyajikan contoh layout proposal jasa fotografi, lembar invoice komersial profesional, dan moodboard presentasi portofolio yang terbukti memenangkan proyek bernilai tinggi.\n\nPerhatikan bagaimana penataan tipografi, pemilihan foto hero utama, dan kejelasan ringkasan paket layanan dapat meningkatkan kredibilitas merek fotografi Anda di mata klien premium.",
                        default => "Bedah karya foto beresolusi tinggi dengan penjelasan detail setting exposure (ISO, Shutter Speed, Aperture), pencahayaan, dan komposisi framing yang digunakan pada karya visual profesional.\n\nPelajari analisis perbandingan teknik dan hasil akhir foto guna mengasah kepekaan estetika visual serta memperkaya referensi karya portofolio Anda.",
                    };

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modulB->id,
                        'judul' => 'Audio Podcast: Diskusi & Tips Lapangan',
                        'tipe' => 'audio',
                        'konten' => $kontenAudio,
                        'media_type' => 'external',
                        'media_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
                        'durasi_menit' => 12,
                        'urutan' => 3,
                        'is_free' => false,
                    ]);

                    ElearningLesson::create([
                        'id_elearning' => $course->id,
                        'id_module' => $modulB->id,
                        'judul' => 'Galeri Studi Kasus Hasil Visual',
                        'tipe' => 'reading',
                        'konten' => $kontenFoto,
                        'media_type' => 'external',
                        'media_url' => $course->thumbnail_url ?: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=1200&q=80',
                        'durasi_menit' => 8,
                        'urutan' => 4,
                        'is_free' => false,
                    ]);
                }
            }
        }
    }
}
