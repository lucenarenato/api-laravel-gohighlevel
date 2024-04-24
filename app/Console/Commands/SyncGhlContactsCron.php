<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\Program;
use App\Models\ProgramLevel;
use App\Models\Setting;
use App\Http\Controllers\Api\Vendor\MemberController;

class SyncGhlContactsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ghl-contacts';

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
        $ghl_integration = Setting::where('name', 'ghl_integration')->first();

        $token = $ghl_integration['value'];
        $companyId = $ghl_integration['meta_data']['companyId'];
        $locations = Location::all();
        // $locations = Location::where('go_high_level_location_id', 'kxsCgZcTUell5zwFkTUc')->get();
        foreach($locations as $location) {
            $reqCustomField = null;
            try {
                $tokenObj = Http::withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28'                
                ])->asForm()->post('https://services.leadconnectorhq.com/oauth/locationToken', [
                    'companyId' => $companyId,
                    'locationId' => $location->go_high_level_location_id,
                ]);
        
                if ($tokenObj->failed()) {
                    Log::info('=== SyncGhlContactsCron === (Location Token) =>'.json_encode($tokenObj->json()));
                }
                
                $tokenObj = $tokenObj->json();
              
                do {
                    $responseCustomField = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer '.$tokenObj['access_token'],
                        'Version' => '2021-07-28'
                    ])->get('https://services.leadconnectorhq.com/locations/'.$location->go_high_level_location_id.'/customFields');
        
                    if ($responseCustomField->failed()) {
                        $responseCustomField->throw();    
                    }
                    $responseCustomField = $responseCustomField->json();
        
                    foreach($responseCustomField['customFields'] as $customField) {
                        if($customField['name'] == 'Program Level') {
                            $reqCustomField = $customField;
                        }
                    }

                    if($reqCustomField) {
                        $url = 'https://services.leadconnectorhq.com/contacts/?locationId='.$location->go_high_level_location_id.'&limit=100';
                        $response = Http::withHeaders([
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '.$tokenObj['access_token'],
                            'Version' => '2021-07-28'
                        ])->get($url);
            
                        if ($response->failed()) {
                            $response->throw();    
                        }
                        $response = $response->json();
                        $contacts = $response['contacts'];
                        $url = null;
                        if(isset($response['meta'])) {
                            if(isset($response['meta']['nextPageUrl'])) {
                                $url = $response['meta']['nextPageUrl'];
                                $url = str_replace('http://', 'https://', $url);
                            }
                        }
                        foreach($contacts as $contact) {
                            $programLevelId = null;

                            $custom_field_index = array_search($reqCustomField['id'], array_column($contact['customFields'], 'id'));

                            if($custom_field_index !== false) {
                                if (strpos($contact['customFields'][$custom_field_index]['value'], '_') === false) {
                                    continue;
                                }
                                $parts = explode('_', $contact['customFields'][$custom_field_index]['value']);
                                if(count($parts) != 2) {
                                    continue;
                                }

                                $programName = $parts[0];
                                $programLevelName = $parts[1];

                                $program = Program::where('name', $programName)->where('location_id', $location->id)->first();
                                if($program) {
                                    $programLevel = ProgramLevel::where('name', $programLevelName)->where('program_id', $program->id)->first();
                                    if($programLevel) {
                                        MemberController::createMemberFromGHL($contact, $location ,$programLevel->id);
                                    }
                                }                              
                            }
                        }
                    }
                    sleep(5);
                } while($url);
            } catch(\Exception $error) {
                Log::info($error->getMessage());
            }
        }
    }
}
