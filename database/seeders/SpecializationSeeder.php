<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['name_en' => 'Business Strategy', 'name_ar' => 'استراتيجية الأعمال'],
            ['name_en' => 'Financial Advisory', 'name_ar' => 'الاستشارات المالية'],
            ['name_en' => 'Marketing & Growth', 'name_ar' => 'التسويق والنمو'],
            ['name_en' => 'Technology & Innovation', 'name_ar' => 'التكنولوجيا والابتكار'],
            ['name_en' => 'Legal & Compliance', 'name_ar' => 'القانون والامتثال'],
            ['name_en' => 'Human Resources', 'name_ar' => 'الموارد البشرية'],
            ['name_en' => 'Operations & Supply Chain', 'name_ar' => 'العمليات وسلاسل الإمداد'],
            ['name_en' => 'Real Estate & PropTech', 'name_ar' => 'العقارات والتقنية العقارية'],
            ['name_en' => 'Venture Capital & Fundraising', 'name_ar' => 'رأس المال الجريء والتمويل'],
            ['name_en' => 'Product Management', 'name_ar' => 'إدارة المنتجات'],
        ];

        foreach ($specializations as $index => $specialization) {
            Specialization::updateOrCreate(
                ['slug' => Str::slug($specialization['name_en'])],
                [
                    ...$specialization,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }
}
