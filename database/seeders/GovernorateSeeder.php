<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            'Damascus',      // دمشق
            'Rif_Damascus',  // ريف دمشق
            'Aleppo',        // حلب
            'Homs',          // حمص
            'Hama',          // حماة
            'Latakia',       // اللاذقية
            'Tartus',        // طرطوس
            'Daraa',         // درعا
            'Sweida',        // السويداء
            'Quneitra',      // القنيطرة
            'Idlib',         // إدلب
            'Raqqa',         // الرقة
            'Deir_Ezzor',    // دير الزور
            'Hasakah',       // الحسكة
        ];

        foreach ($governorates as $governorate) {
            Governorate::create([
                'name' => $governorate,
            ]);
        }

    }
}
