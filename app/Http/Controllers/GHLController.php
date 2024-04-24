<?php

namespace App\Http\Controllers;

use App\Services\GHLService;
use Illuminate\Http\Request;

class GHLController extends Controller
{
    protected $ghlService;

    public function __construct(GHLService $ghlService){
        $this->ghlService = $ghlService;
    }

    public function getUserWithTypeAndRole($email,$type,$role){
        return $this->ghlService->getUserWithTypeAndRole($email,$type,$role);
    }

    public function getLocation($ghl_location_id){
        return $this->ghlService->getGhlLocation($ghl_location_id);
    }

    public function updateUser($user_id, $body){
        return $this->ghlService->updateUser($user_id, $body);
    }

    public function updateContact($user_id, $body){
        return $this->ghlService->updateContact($user_id, $body);
    }

    public function createContact($data){
        return $this->ghlService->createContact($data);
    }

    public function upsertContact($data){
        return $this->ghlService->upsertContact($data);
    }

    public function createTask($contact, $ticket){
        return $this->ghlService->createTask($contact, $ticket);
    }

    public function getContactById($id, $locationId){
        return $this->ghlService->getContactById($id, $locationId);
    }
}