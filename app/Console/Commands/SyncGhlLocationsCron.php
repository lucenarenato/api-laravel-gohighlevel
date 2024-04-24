<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\Setting;
use Exception;

class SyncGhlLocationsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ghl-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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
        
        $ghl_integration = Setting::where('name', 'ghl_integration')->first();

        $token = $ghl_integration['value'];
        $companyId = $ghl_integration['companyId'];
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
                    // DB::beginTransaction();
                    // $user = User::where('email', $location['email'])->first();
                    // if(!$user) {
                    //     $user = User::create([
                    //         'name' => isset($location['name']) ? $location['name']: $location['id'],
                    //         'email' => $location['email'],
                    //         'password' => bcrypt(str_replace(' ', '', $location['id']).'@2024!'),
                    //         'email_verified_at' => now()
                    //     ]);
                    //     $user->assignRole(\App\Models\User::VENDOR);
                    //     $vendor = Vendor::create([
                    //         'user_id' => $user->id,
                    //         'company_name' => isset($location['name']) ? $location['name'] : $location['id'],
                    //         'company_email' => $location['email'],
                    //         'company_website' => isset( $location['website']) ?  $location['website'] : '',
                    //         'company_address' => isset($location['address']) ? $location['address'] : ''
                    //     ]);
                    // } else {
                    //     $vendor = $user->vendor;
                    // }
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
                        // 'vendor_id' => $vendor->id,
                        'meta_data' => $location
                    ]);
                    // DB::commit();
                } catch(Exception $e){
                    Log::info('=== After Getting Location ===');
                    Log::info(json_encode($location));
                    Log::info($e->getMessage());
                    // DB::rollBack();
                }
            }
            $offset = $offset + count($allLocations);  
        } while(count($allLocations) > 0);
    }
}
