<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Location;
use Exception;

class SycnGHLLocations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ignore_locations = [
            'Monstro',
            'monstro Outreach',
            'Support Montro',
            'Boom Banging Media',
            'Brandon Hogan Account',
            'Career Co-Pilot',
            'Choicelend mortgage',
            'Convoy Realtor Partner',
            'Convoy Home loans',
            'Demo Playground',
            'Docs Karate San Diego',
            'Gracie Humaita Redlands',
            'Gymnastics Snapshots',
            'Ideal Martial Arts',
            'InvestorLoanHUb.com',
            'Loanforsuccess.com',
            'MA Single Snapshot',
            'Major Academy Martial Arts',
            'Maxim Hair Transplant',
            'Maynard Breese\'s Account',
            'Men\'s Health',
            'Mission Mom Care',
            'Muteki Dojo',
            'My Loan Pipeline',
            'New York Homebuyer porgrams',
            'One World Karate',
            'Peace Keeper Martial Arts',
            'Pinncacle Martial Arts',
            'Purchase snapshot',
            'Root Health',
            'Simply Grow Online',
            'Team Scherillio',
            'Tim Hope\'s Account',
            'Team Brian Diez',
            'US Credit Advocate',
            'We Admit Prep',
            'Will\'s Condo Account',
            'Won\'s Taekwondo Education',
        ];
        // $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJDb21wYW55IiwiYXV0aENsYXNzSWQiOiJHOXVDeTk3bVB2NGl2RFF4OHoyMCIsInNvdXJjZSI6IklOVEVHUkFUSU9OIiwic291cmNlSWQiOiI2NGQyMjhmY2VhOTA0YjFkODQwMTFlNDctbG41MG54MWUiLCJjaGFubmVsIjoiT0FVVEgiLCJwcmltYXJ5QXV0aENsYXNzSWQiOiJHOXVDeTk3bVB2NGl2RFF4OHoyMCIsIm9hdXRoTWV0YSI6eyJzY29wZXMiOlsiYnVzaW5lc3Nlcy5yZWFkb25seSIsImNhbGVuZGFycy5yZWFkb25seSIsImNhbGVuZGFycy53cml0ZSIsImNhbXBhaWducy5yZWFkb25seSIsImNvbnZlcnNhdGlvbnMucmVhZG9ubHkiLCJjb250YWN0cy5yZWFkb25seSIsImNvbnRhY3RzLndyaXRlIiwibG9jYXRpb25zLnJlYWRvbmx5IiwibG9jYXRpb25zL2N1c3RvbVZhbHVlcy5yZWFkb25seSIsImxvY2F0aW9ucy9jdXN0b21GaWVsZHMucmVhZG9ubHkiLCJsb2NhdGlvbnMvdGFza3MucmVhZG9ubHkiLCJsb2NhdGlvbnMvdGFncy5yZWFkb25seSIsIm9wcG9ydHVuaXRpZXMucmVhZG9ubHkiLCJvcHBvcnR1bml0aWVzLndyaXRlIiwidXNlcnMucmVhZG9ubHkiLCJjYWxlbmRhcnMvZXZlbnRzLnJlYWRvbmx5IiwiY29udmVyc2F0aW9ucy9tZXNzYWdlLnJlYWRvbmx5Iiwib2F1dGgud3JpdGUiLCJvYXV0aC5yZWFkb25seSJdLCJjbGllbnQiOiI2NGQyMjhmY2VhOTA0YjFkODQwMTFlNDciLCJjbGllbnRLZXkiOiI2NGQyMjhmY2VhOTA0YjFkODQwMTFlNDctbG41MG54MWUifSwiaWF0IjoxNzAyMzc5MjMxLjgyMSwiZXhwIjoxNzAyNDY1NjMxLjgyMX0.6LBdbzuVJAciXIq2xBY1TtNnWtpY1xKOg2KgPQO63lw';
        // $companyId = 'G9uCy97mPv4ivDQx8z20';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJDb21wYW55IiwiYXV0aENsYXNzSWQiOiJaNDl3bWY4ZzM4UXN4ZWRvQmRpRyIsInNvdXJjZSI6IklOVEVHUkFUSU9OIiwic291cmNlSWQiOiI2NTc4Mzk0NTA4MWEzZTEyZjZlMGI0MTEtbHEyOThxa2YiLCJjaGFubmVsIjoiT0FVVEgiLCJwcmltYXJ5QXV0aENsYXNzSWQiOiJaNDl3bWY4ZzM4UXN4ZWRvQmRpRyIsIm9hdXRoTWV0YSI6eyJzY29wZXMiOlsiYnVzaW5lc3Nlcy5yZWFkb25seSIsImJ1c2luZXNzZXMud3JpdGUiLCJjb250YWN0cy5yZWFkb25seSIsImNvbnRhY3RzLndyaXRlIiwibG9jYXRpb25zLndyaXRlIiwibG9jYXRpb25zLnJlYWRvbmx5IiwibG9jYXRpb25zL2N1c3RvbVZhbHVlcy53cml0ZSIsImxvY2F0aW9ucy9jdXN0b21WYWx1ZXMucmVhZG9ubHkiLCJsb2NhdGlvbnMvY3VzdG9tRmllbGRzLnJlYWRvbmx5IiwibG9jYXRpb25zL2N1c3RvbUZpZWxkcy53cml0ZSIsImxvY2F0aW9ucy90YWdzLnJlYWRvbmx5IiwibG9jYXRpb25zL3RhZ3Mud3JpdGUiLCJvcHBvcnR1bml0aWVzLnJlYWRvbmx5Iiwib3Bwb3J0dW5pdGllcy53cml0ZSIsIm9hdXRoLnJlYWRvbmx5Iiwib2F1dGgud3JpdGUiXSwiY2xpZW50IjoiNjU3ODM5NDUwODFhM2UxMmY2ZTBiNDExIiwiY2xpZW50S2V5IjoiNjU3ODM5NDUwODFhM2UxMmY2ZTBiNDExLWxxMjk4cWtmIn0sImlhdCI6MTcwMjM5MDg0My45ODEsImV4cCI6MTcwMjQ3NzI0My45ODF9.g091fh2ivrXGwuoxjp7kiT7PlUWUT82SjLmS5LriJhU';
        $companyId = 'Z49wmf8g38QsxedoBdiG';
        $offset = 0;
        do {
            $response =  Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Version' => '2021-07-28',
            ])->get('https://services.leadconnectorhq.com/locations/search?limit=100&skip='.$offset.'&companyId='.$companyId);

            if($response->failed()) {
                $response->throw();
            }

            $response_json = json_decode($response->body(),true);

            if(!isset($response_json['locations'])) {
                return;
            }
            
            $allLocations = $response_json['locations'];   
            foreach($allLocations as $index => $location){
                try {
                    
                    if (in_array($location['name'], $ignore_locations)) {
                        continue;
                    }
                    $checkIfLocationExist = Location::where('go_high_level_location_id', $location['id'])->first();
                    if($checkIfLocationExist) {
                        continue;
                    }
                    if(!isset($location['email'])) {
                        Log::info(json_encode($location));
                        continue;
                    }
                    DB::beginTransaction();
                    $user = User::where('email', $location['email'])->first();
                    if(!$user) {
                        $user = User::create([
                            'name' => isset($location['name']) ? $location['name']: $location['id'],
                            'email' => $location['email'],
                            'password' => bcrypt(str_replace(' ', '', $location['lastName']).'@2024!'),
                            'email_verified_at' => now()
                        ]);
                        $user->assignRole(\App\Models\User::VENDOR);
                        $vendor = Vendor::create([
                            'user_id' => $user->id,
                            'company_name' => isset($location['name']) ? $location['name'] : $location['id'],
                            'company_email' => $location['email'],
                            'company_website' => isset( $location['website']) ?  $location['website'] : '',
                            'company_address' => isset($location['address']) ? $location['address'] : ''
                        ]);
                    }
                    $vendor = $user->vendor;
                    $location = Location::create([
                        'go_high_level_location_id' => $location['id'],
                        'name' => isset($location['name']) ? $location['name'] : $location['id'],
                        'address' => isset($location['address']) ? $location['address'] : '',
                        'city' => isset($location['city']) ? $location['city'] : '',
                        'state' => isset($location['state']) ? $location['state'] : '',
                        'logo_url' => null,
                        'country' => isset($location['country']) ? $location['country'] : '',
                        'postal_code' => isset($location['postalCode']) ? $location['postalCode'] : '',
                        'website' => isset($location['website']) ? $location['website'] : '',
                        'email' => $location['email'],
                        'phone' => isset($location['phone']) ? $location['phone'] : '',
                        'vendor_id' => $vendor->id,
                        'meta_data' => $location
                    ]);
                    DB::commit();
                } catch(Exception $e){
                    Log::info('=== After Getting Location ===');
                    Log::info(json_encode($location));
                    Log::info($e->getMessage());
                    DB::rollBack();
                }
            }
            $offset = $offset + count($allLocations);  
        } while(count($allLocations) > 0);
       
    }
}
