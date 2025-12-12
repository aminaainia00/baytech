<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'Damascus' => [ // دمشق
                'Old_Damascus',     // دمشق القديمة
                'Mazzeh',           // المزة
                'Kafar_Souseh',     // كفرسوسة
                'Baramkeh',         // برامكة
            ],

            'Rif_Damascus' => [ // ريف دمشق
                'Douma',            // دوما
                'Harasta',          // حرستا
                'Qudsaya',          // قدسيا
                'Jaramana',         // جرمانا
            ],

            'Aleppo' => [ // حلب
                'Aziziyeh',         // العزيزية
                'Slemani',          // السليمانية
                'Al_Shaar',         // الشعار
                'Bab_Al_Faraj',     // باب الفرج
            ],

            'Homs' => [ // حمص
                'Al_Hamidiya',      // الحميدية
                'Al_Inshaat',       // الإنشاءات
                'Bab_Sbaa',         // باب السباع
            ],

            'Hama' => [ // حماة
                'Al_Hader',         // الحاضر
                'Al_Dabagha',       // الدباغة
                'Al_Tawheenah',     // الطواحين
            ],

            'Latakia' => [ // اللاذقية
                'Al_Ziraa',         // الزراعة
                'Al_Raml',          // الرمل
                'Tishreen',         // تشرين
            ],

            'Tartus' => [ // طرطوس
                'Al_Mina',          // الميناء
                'Corniche',         // الكورنيش
                'Al_Basl',          // البصّال
            ],

            'Daraa' => [ // درعا
                'Daraa_Al_Balad',   // درعا البلد
                'Tafileh',          // الطفيلة
            ],

            'Sweida' => [ // السويداء
                'Shahba',           // شهبا
                'Salkhad',          // صلخد
            ],

            'Quneitra' => [ // القنيطرة
                'Khan_Arnabeh',     // خان أرنبة
                'Al_Baath',         // مدينة البعث
            ],

            'Idlib' => [ // إدلب
                'Saraqib',          // سراقب
                'Jisr_Al_Shgour',   // جسر الشغور
            ],

            'Raqqa' => [ // الرقة
                'Al_Mansoura',      // المنصورة
                'Al_Salamiyah',     // السلامية
            ],

            'Deir_Ezzor' => [ // دير الزور
                'Al_Joura',         // الجورة
                'Al_Qusour',        // القصور
            ],

            'Hasakah' => [ // الحسكة
                'Qamishli',         // القامشلي
                'Malikiyah',        // المالكية
            ],
        ];

        foreach ($cities as $governorateName => $governorateCities) {

            $governorate = Governorate::where('name', $governorateName)->first();

            foreach ( $governorateCities as $city) {
                City::create([
                    'governorate_id' => $governorate->id,
                    'name'        => $city,
                ]);
            }
    }
}
}
