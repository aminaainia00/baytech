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
        'Midan',            // الميدان
        'Qanawat',          // القنوات
        'Rukn_Al_Din',      // ركن الدين
        'Shaghour',         // الشاغور
        'Dummar',           // دمر
    ],

    'Rif_Damascus' => [// ريف دمشق
        'Al_Nabek',         //النبك 
        'Douma',            // دوما
        'Harasta',          // حرستا
        'Qudsaya',          // قدسيا
        'Jaramana',         // جرمانا
        'Zabadani',         // الزبداني
        'Darayya',          // داريا
        'Al_Tall',          // التل
        'Yabroud',          // يبرود
        'Al_Qutayfah',      // القطيفة
    ],

    'Aleppo' => [ // حلب
        'Aziziyeh',         // العزيزية
        'Slemani',          // السليمانية
        'Al_Shaar',         // الشعار
        'Bab_Al_Faraj',     // باب الفرج
        'New_Aleppo',       // حلب الجديدة
        'Al_Hamdaniyah',    // الحمدانية
        'Al_Sukkari',       // السكري
        'Al_Midan',         // الميدان
        'Al_Ansari',        // الأنصاري
    ],

    'Homs' => [ // حمص
        'Al_Hamidiya',      // الحميدية
        'Al_Inshaat',       // الإنشاءات
        'Bab_Sbaa',         // باب السباع
        'Karam_Al_Zeitoun', // كرم الزيتون
        'Al_Waer',          // الوعر
        'Baba_Amr',         // بابا عمرو
        'Al_Khaldiyeh',     // الخالدية
        'Al_Zahra',         // الزهراء
    ],

    'Hama' => [ // حماة
        'Al_Hader',         // الحاضر
        'Al_Dabagha',       // الدباغة
        'Al_Tawheenah',     // الطواحين
        'Al_Murabit',       // المرابط
        'Al_Arbaeen',       // الأربعين
        'Al_Jalaa',         // الجلاء
        'Al_Hamra',         // الحمراء
    ],

    'Latakia' => [ // اللاذقية
        'Al_Ziraa',         // الزراعة
        'Al_Raml',          // الرمل
        'Al_Sheikh_Daher',  // الشيخ ضاهر
        'Al_Slabeh',        // الصليبة
        'Al_Aziziyeh',      // العزيزية
        'Corniche',         // الكورنيش
    ],

    'Tartus' => [ // طرطوس
        'Al_Mina',          // الميناء
        'Corniche',         // الكورنيش
        'Al_Basl',          // البصّال
        'Al_Arzounah',      // العرصونة
        'Baniyas',          // بانياس
        'Safita',           // صافيتا
        'Duraykish',        // دريكيش
    ],

    'Daraa' => [ // درعا
        'Daraa_Al_Balad',   // درعا البلد
        'Tafileh',          // الطفيلة
        'Izraa',            // إزرع
        'Nawa',             // نوى
        'Jasim',            // جاسم
        'Bosra',            // بصرى
    ],

    'Sweida' => [ // السويداء
        'Shahba',           // شهبا
        'Salkhad',          // صلخد
        'Qanawat',          // قنوات
        'Al_Mazraa',        // المزرعة
        'Ariqa',            // عريقة
    ],

    'Quneitra' => [ // القنيطرة
        'Khan_Arnabeh',     // خان أرنبة
        'Jubata_Al_Khashab',// جباتا الخشب
        'Hader',            // حضر
    ],

    'Idlib' => [ // إدلب
        'Saraqib',          // سراقب
        'Jisr_Al_Shgour',   // جسر الشغور
        'Ariha',            // أريحا
        'Maarrat_Al_Numan', // معرة النعمان
        'Kafr_Takharim',    // كفرتخاريم
    ],

    'Raqqa' => [ // الرقة
        'Al_Mansoura',      // المنصورة
        'Al_Salamiyah',     // السلامية
        'Tabqa',            // الطبقة
        'Al_Rumaila',       // الرميلة
        'Al_Sabkha',        // السبخة
    ],

    'Deir_Ezzor' => [ // دير الزور
        'Al_Joura',         // الجورة
        'Al_Qusour',        // القصور
        'Al_Hamidiya',      // الحميدية
        'Al_Moallemeen',    // المعلمين
        'Mayadin',          // الميادين
    ],

    'Hasakah' => [ // الحسكة
        'Qamishli',         // القامشلي
        'Malikiyah',        // المالكية
        'Ras_Al_Ain',       // رأس العين
        'Al_Shaddadi',      // الشدادي
        'Amuda',            // عامودا
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
