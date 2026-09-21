<?php

namespace Database\Seeders;

use App\Models\PhotoContest;
use Illuminate\Database\Seeder;

class PhotoContestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSyaratKetentuan = "1. Orisinalitas & Hak Cipta: Karya foto harus merupakan hasil karya orisinal peserta sendiri, belum pernah memenangkan kompetisi sejenis sebelumnya, dan tidak melanggar hak cipta pihak mana pun.
2. Ketentuan Teknis File: Format foto yang diizinkan adalah JPG, JPEG, PNG, atau WEBP dengan resolusi tinggi (maksimal ukuran file 10MB). Olah digital diperbolehkan sebatas penyesuaian dasar (cropping, exposure, contrast, color balance) tanpa manipulasi penambahan/penghapusan objek utama.
3. Kriteria Penjurian: Penilaian dewan juri didasarkan pada kekuatan konsep/storytelling (40%), komposisi & keunikan sudut pandang (30%), serta penguasaan teknik pencahayaan & ketajaman (30%).
4. Hadiah & Apresiasi: Pemenang akan mendapatkan hadiah berupa uang tunai, e-sertifikat resmi, serta publikasi kurasi karya di platform dan media sosial utama.
5. Keputusan Juri: Keputusan dewan juri bersifat mutlak, independen, dan tidak dapat diganggu gugat.";

        $contests = PhotoContest::all();

        foreach ($contests as $contest) {
            $startDate = $contest->start_date ?: now()->subDays(10);
            $endDate = $contest->end_date ?: now()->addDays(20);

            $regStart = $startDate->copy();
            $regEnd = $startDate->copy()->addDays(10);
            $subStart = $regEnd->copy()->addDay();
            $subEnd = $subStart->copy()->addDays(12);
            $judgStart = $subEnd->copy()->addDay();
            $judgEnd = $judgStart->copy()->addDays(3);
            $announceDate = $judgEnd->copy()->addDay();

            $contest->update([
                'syarat_ketentuan' => $defaultSyaratKetentuan,
                'max_submissions' => $contest->max_submissions ?: 2,
                'allow_link' => $contest->allow_link ?? true,
                'biaya_pendaftaran' => $contest->biaya_pendaftaran ?? 0,
                'bank_name' => $contest->bank_name ?: 'Bank Central Asia (BCA)',
                'bank_account_number' => $contest->bank_account_number ?: '8271928391',
                'bank_account_holder' => $contest->bank_account_holder ?: 'PT LensMatch Komunika Digital',
                'registration_start' => $regStart,
                'registration_end' => $regEnd,
                'submission_start' => $subStart,
                'submission_end' => $subEnd,
                'judging_start' => $judgStart,
                'judging_end' => $judgEnd,
                'announcement_date' => $announceDate,
            ]);
        }
    }
}
