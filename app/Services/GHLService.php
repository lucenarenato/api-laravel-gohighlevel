<?php

namespace App\Services;

use App\Enums\TicketStatus;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class GHLService
{

    protected $ghlIntegration;

    public function __construct(){
        $ghlIntegration = Setting::where('name', 'ghl_integration')->first();
        $this->ghlIntegration = $ghlIntegration;
    }

    public function getUserWithTypeAndRole($email,$type,$role){
        $companyId = $this->ghlIntegration['meta_data']['companyId'];
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->ghlIntegration['value'],
            'Version' => config('services.ghl.api_version'),
        ])->get(config('services.ghl.api_url') . "users/search?companyId={$companyId}&role={$role}&type={$type}&query={$email}");
        return $response->json();
    }

    
    public function getGhlLocation($location_id){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->ghlIntegration['value'],
            'Version' => config('services.ghl.api_version'),
        ])->get(config('services.ghl.api_url') ."locations/{$location_id}");

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - getGhlLocation() =====');
            Log::info(json_encode($response->json()));
            return null;
        }
    }


    public function updateUser($user_id, $body){
        $body['companyId'] = $this->ghlIntegration['meta_data']['companyId'];
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->ghlIntegration['value'],
            'Version' => config('services.ghl.api_version'),
        ])->put(config('services.ghl.api_url') ."users/{$user_id}", $body);
        
        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - updateUser() =====');
            Log::info(json_encode($response->json()));
            return null;
        }
    }


    public function createContact($data){
        $locationObj = $this->generateLocationLevelKey($data['locationId']);
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $locationObj['access_token'],
            'Version' => config('services.ghl.api_version'),
        ])->post(config('services.ghl.api_url') .'contacts/', $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - createContact() =====');
            Log::info($response->body());
            return null;
        }
    }

    public function upsertContact($data){
        $locationObj = $this->generateLocationLevelKey($data['locationId']);
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $locationObj['access_token'],
            'Version' => config('services.ghl.api_version'),
        ])->post(config('services.ghl.api_url') .'contacts/upsert', $data);
        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - createContact() =====');
            Log::info($response->body());
            return null;
        }
    }

    public function createTask($contact, $ticket){
        $response =  Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->ghlIntegration['value'],
            'Version' => config('services.ghl.api_version'),
        ])->post(config('services.ghl.api_url') ."contacts/", [
            'name' => $contact['name'],
            'email' => $contact['email'],
            'customField' => [
                'ticket_number' => $ticket['id'],
                'account' => $ticket['accountId'],
                'subject' => $ticket['subject'],
                'issue' => $ticket['issue'],
                'video' => $ticket['video'] ?? null,
                'description' => $ticket['description'] ?? null,
                'status' => TicketStatus::OPEN,
            ],
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - createTask() =====');
            Log::info(json_encode($response->json()));
            return null;
        }
    }

    public function updateContact($id, $data){

        try {
            $locationObj = $this->generateLocationLevelKey($data['locationId']);
            unset($data['locationId']);
            $response = Http::withHeaders([
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $locationObj['access_token'],
                'Version' => config('services.ghl.api_version'),
            ])->put(config('services.ghl.api_url') ."contacts/$id", $data);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::info('==== GHL SERVICE - updateContact() =====');
                Log::info(json_encode($response->json()));
                return null;
            }
        } catch (Exception $error) {
           return $response->throw();
        }
    }

    public function generateLocationLevelKey($location_id) {
        $tokenObj = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->ghlIntegration['value'],
            'Version' => '2021-07-28'                
        ])->asForm()->post('https://services.leadconnectorhq.com/oauth/locationToken', [
            'companyId' => $this->ghlIntegration['meta_data']['companyId'],
            'locationId' => $location_id,
        ]);

        if ($tokenObj->failed()) {
            $tokenObj->throw();
        }
     
        return $tokenObj->json();
    }

    public function getContactById($id, $locationId) {
        $locationObj = $this->generateLocationLevelKey($locationId);
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $locationObj['access_token'],
            'Version' => config('services.ghl.api_version'),
        ])->post(config('services.ghl.api_url') .'contacts/', $id);
        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - Error getContactById() =====');
            Log::info($response->body());
            return null;
        }
    }

    public function getContactsByLocation($locationId){
        $locationObj = $this->generateLocationLevelKey($locationId);
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $locationObj['access_token'],
            'Version' => config('services.ghl.api_version'),
        ])->get(config('services.ghl.api_url') .'contacts/?locationId='.$locationId);
        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - Error getContactsByLocation() =====');
            Log::info($response->body());
            return $response->body();
        }
    }

    public function getContactsByName($locationId, $name){
        $locationObj = $this->generateLocationLevelKey($locationId);
        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $locationObj['access_token'],
            'Version' => config('services.ghl.api_version'),
        ])->get(config('services.ghl.api_url') .'contacts/?locationId='.$locationId.'&query='.$name);
        if ($response->successful()) {
            return $response->json();
        } else {
            Log::info('==== GHL SERVICE - Error getContactsByLocation() =====');
            Log::info($response->body());
            return $response->body();
        }
    }
}