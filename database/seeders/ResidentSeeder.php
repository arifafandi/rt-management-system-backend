<?php

namespace Database\Seeders;

use App\Models\Resident;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure the storage directory exists
        $storagePath = storage_path('app/public/id_cards');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Create sample ID card image (just a colored rectangle)
        $idCardSample = $this->createSampleIdCard();

        // List of Indonesian names for the dummy data
        $names = [
            'Budi Santoso', 'Siti Nurhayati', 'Ahmad Wijaya', 'Dewi Lestari', 'Eko Prasetyo',
            'Fitri Handayani', 'Gunawan Hartono', 'Heni Purnama', 'Irfan Setiawan', 'Joko Susilo',
            'Kartika Sari', 'Lutfi Hakim', 'Maya Rahmawati', 'Nanda Putri', 'Oki Kurniawan',
            'Putri Wulandari', 'Rudi Hermawan', 'Sri Wahyuni', 'Tono Sutanto', 'Udin Pratama',
            'Vina Anggraeni', 'Wawan Hidayat', 'Yanti Permata', 'Zaenal Abidin', 'Ani Maryani'
        ];

        // Create 20 permanent residents
        for ($i = 0; $i < 20; $i++) {
            // Create a new unique file for each resident
            $fileName = 'id_card_' . ($i + 1) . '.png';
            $filePath = 'id_cards/' . $fileName;
            File::put(storage_path('app/public/' . $filePath), $idCardSample);

            Resident::create([
                'name' => $names[$i],
                'id_card_photo' => $filePath,
                'resident_status' => $i < 15 ? 'permanent' : 'contract',
                'phone_number' => '08' . rand(10000000, 99999999),
                'is_married' => rand(0, 1) === 1,
            ]);
        }

        // Create 5 additional contract residents (for historical data)
        for ($i = 20; $i < 25; $i++) {
            // Create a new unique file for each resident
            $fileName = 'id_card_' . ($i + 1) . '.png';
            $filePath = 'id_cards/' . $fileName;
            File::put(storage_path('app/public/' . $filePath), $idCardSample);

            $index = $i % count($names);

            Resident::create([
                'name' => $names[$index] . ' ' . chr(65 + $i - 20), // Add suffix to make names unique
                'id_card_photo' => $filePath,
                'resident_status' => 'contract',
                'phone_number' => '08' . rand(10000000, 99999999),
                'is_married' => rand(0, 1) === 1,
            ]);
        }
    }

    /**
     * Create a simple colored rectangle as a sample ID card image
     */
    private function createSampleIdCard()
    {
        $width = 600;
        $height = 400;

        // Create a blank image
        $image = imagecreatetruecolor($width, $height);

        // Colors
        $bg = imagecolorallocate($image, 220, 220, 220);
        $blue = imagecolorallocate($image, 0, 114, 206);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        // Fill the background
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        // Header
        imagefilledrectangle($image, 0, 0, $width, 60, $blue);

        // Title text
        $text = "KARTU TANDA PENDUDUK";
        $font = 5; // Use built-in font
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = ($width - $textWidth) / 2;
        imagestring($image, $font, $x, 20, $text, $white);

        // Photo placeholder
        imagefilledrectangle($image, 30, 80, 180, 230, $white);
        imagerectangle($image, 30, 80, 180, 230, $black);

        // Text fields
        $y = 100;
        $fields = ['Nama:', 'NIK:', 'Tempat/Tgl Lahir:', 'Jenis Kelamin:', 'Alamat:', 'RT/RW:', 'Kel/Desa:', 'Kecamatan:', 'Agama:', 'Status:'];

        foreach ($fields as $field) {
            imagestring($image, 3, 200, $y, $field, $black);
            $y += 30;
        }

        // Output to a string
        ob_start();
        imagepng($image);
        $imagedata = ob_get_clean();

        // Free memory
        imagedestroy($image);

        return $imagedata;
    }
}
