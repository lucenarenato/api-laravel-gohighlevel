<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'user_id' => 1,
                'company_name' => 'Company One',
                'company_email' => 'companyOne@monto.com',
                'company_website' => 'companyone.com',
                'company_address' => 'South Wales south plot 1-2DA'
            ],
            [
                'user_id' => 2,
                'company_name' => 'Company Two',
                'company_email' => 'companTwo@monto.com',
                'company_website' => 'companytwo.com',
                'company_address' => 'South Wales south plot 4-2DA'
            ],
            [
                'user_id' => 3,
                'company_name' => 'Company Three',
                'company_email' => 'companThree@monto.com',
                'company_website' => 'companythree.com',
                'company_address' => 'South Wales south plot 3-2DA'
            ],
            [
                'user_id' => 4,
                'company_name' => 'Company Two',
                'company_email' => 'companFour@monto.com',
                'company_website' => 'companyfour@company.com',
                'company_address' => 'South Wales south plot 2-2DA'
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::insert($vendor);
        }
    }
}
