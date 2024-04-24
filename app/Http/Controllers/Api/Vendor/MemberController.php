<?php

namespace App\Http\Controllers\Api\Vendor;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Reservation;
use App\Models\Session;
use App\Models\Setting;
use App\Models\Program;
use App\Models\CheckIn;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Vendor\CreateMemberResource;
use App\Http\Resources\Member\ProgramResource;
use App\Http\Resources\Vendor\ReservationResource;
use App\Http\Resources\Vendor\MemberResource;
use App\Http\Resources\Vendor\SessionResource;
use App\Models\ProgramLevel;
use App\Notifications\NewMemberNotification;
use App\Services\GHLService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class MemberController extends BaseController
{
    
    protected $ghlService;

    public function __construct(GHLService $ghlService){
        $this->ghlService = $ghlService;
    }

    public function getMembersByLocation(){
        //Code commented out below becuase auth guard is not applied anymore.
        // $location = Location::find($location_id);
        // if($location->vendor_id != auth()->user()->vendor->id) {
        //     return $this->sendError('Vendor not authenticated', [], 403);
        // }
        $location = request()->location;
        $locationId = $location->id;
        $membersByLocation = Member::whereHas('locations', function ($query) use ($locationId) {
            $query->where('locations.id', $locationId);
        })->whereHas("user.roles", function ($q){
            $q->where('name', \App\Models\User::MEMBER);
        })->paginate(10);

        $data = [
            'members' => MemberResource::collection($membersByLocation),
            'pagination' => [
                'current_page' => $membersByLocation->currentPage(),
                'per_page' => $membersByLocation->perPage(),
                'total' => $membersByLocation->total(),
                'prev_page_url' => $membersByLocation->previousPageUrl(),
                'next_page_url' => $membersByLocation->nextPageUrl(),
                'first_page_url' => $membersByLocation->url(1),
                'last_page_url' => $membersByLocation->url($membersByLocation->lastPage()),
            ],
        ];
        
        return $this->sendResponse($data, 'Members with details for the location.');
    }

    public function profileUpdate(Request $request, $member_id){
        try{
            $member = Member::find($member_id);

            if(!$member){
                return $this->sendError('Member does not exist.', [], 400);
            }
            
            $member->name = $request->name;
            $member->email = $request->email;
            $member->phone = $request->phone;
            $member->save();
            
            return $this->sendResponse(new MemberResource($member), 'Members updated successfully.');            
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getMemberDetails($member_id){
        try{
            $location = request()->location;
            $locationId = $location->id;
            
            $reservations = Reservation::with(['checkIns', 'session', 'session.programLevel','session.programLevel.program'])
            ->whereHas('session.programLevel', function ($query) {
                return $query->whereNull('deleted_at');
            })->whereHas('session.program', function ($query) {
                return $query->whereNull('deleted_at');
            })
            ->where('member_id', $member_id)->get();
            
            $member_details = Member::with(['rewards', 'achievements'])->where('id', $member_id)->first();
            
            $go_high_level_contact_id = DB::table('member_locations')
                ->where('member_id', $member_id)
                ->where('go_high_level_location_id', $location->go_high_level_location_id)
                ->pluck('go_high_level_contact_id')
                ->first();

            $member_details['go_high_level_contact_id'] = $go_high_level_contact_id;

            if(count($reservations)) {
                $reservationIds = $reservations->pluck('id')->toArray();
                $latestCheckInTime = CheckIn::whereIn('reservation_id', $reservationIds)->latest()->first();
                if ($latestCheckInTime) {
                    $carbonInstance = Carbon::parse($latestCheckInTime->check_in_time);
                    $member_details['last_seen'] = $carbonInstance->diffForHumans();
                } else {
                    $member_details['last_seen'] = null;
                }
            } else {
                $member_details['last_seen'] = null;
            }

            $member_details['current_level'] = $this->getCurrentLevel($member_id);

            $data = [
                'memberDetails' => new MemberResource($member_details),
                'reservations' => ReservationResource::collection($reservations)
            ];
            
            return $this->sendResponse($data, 'Member details with session reservations and program');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getCurrentLevel($member_id){
        try{
            $currentLevel = null;
            $member = Member::findOrFail($member_id);
            $member->load(['reservations' => function ($query) {
                $query->where('status', Reservation::ACTIVE);
            }]);
            
            if(count($member->reservations) > 0){
                $currentLevel = $member->reservations[0]->session->programLevel->name;
            }else{
                $currentLevel = "----";
            }
            
            return $currentLevel;
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function createMember(CreateMemberResource $request){
        try{
            $location = request()->location;
            $password = Str::random(8);

            $user = User::where('email', $request->email)->first();

            if(!$user){
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($password),
                    'email_verified_at' => now()
                ]);
    
                $user->assignRole(User::MEMBER);
                $randomNumberMT = mt_rand(100, 999);
    
                $member = Member::create([
                    'name' => $request->name,
                    'email' =>  $request->email,
                    'phone' => $request->phone,
                    'referral_code' => $randomNumberMT.$user->id,
                    'user_id' => $user->id
                ]);

                if(isset($request->programLevelId)){
                    $session = Session::where('program_level_id', $request->programLevelId)->where('status', Session::ACTIVE)->latest()->first();

                    if($session){
                        Reservation::updateOrCreate([
                            'session_id' => $session->id,
                            'member_id' =>  $member->id
                        ],[
                            'session_id' => $session->id,
                            'member_id' =>  $member->id,
                            'status' => Reservation::ACTIVE,
                            'start_date' => Carbon::today()->format('Y-m-d'),
                            'end_date' => $session->end_date
                        ]);
                    }
                }
    
                $member->locations()->sync([$location->id => [
                    'go_high_level_location_id' => $location->go_high_level_location_id,
                    'go_high_level_contact_id' => null
                ]], false);
    
                $data =  [];
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['password'] = $password;
                // Notification::route('mail', $user->email)->notify(new NewMemberNotification($data));
    
                return $this->sendResponse(new MemberResource($member), 'Member created successfully.');
            }else{
                return $this->sendError('Member already registered with this email', [], 400);
            }
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function memberStatusUpdate($member_id){
        try{ 
            $member = Member::find($member_id);

            if(!$member){
                return $this->sendError('Member does not exist.', [], 400);
            }

            $member->status = \App\Models\Member::INACTIVE;
            $member->save();

            return $this->sendResponse(new MemberResource($member), 'Member status updated to inactive successfully.');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public static function createMemberFromGHL($contact, $location, $programLevelId) {
        try {
            DB::beginTransaction();
            $session = Session::where('program_level_id', $programLevelId)->where('status', Session::ACTIVE)->latest()->first();

            $password = 'Monstro2024Welcome';
            $user = User::where('email', $contact['email'])->first();
            if(!$user) {
                $user = User::create([
                    'name' => isset($contact['contactName']) ? $contact['contactName'] : '',
                    'email' => $contact['email'],
                    // 'password' => bcrypt($contact['email'].'@'.Carbon::now()->year.'!!'),
                    'password' => bcrypt($password),
                    'email_verified_at' => now()
                ]);
                $user->assignRole(User::MEMBER);
                $randomNumberMT = mt_rand(100, 999);
                $member = Member::create([
                    'name' => (isset($contact['firstName']) ? $contact['firstName'] : ' ').' ' .(isset($contact['lastName']) ? $contact['lastName'] : ''),
                    'email' =>  $contact['email'],
                    'phone' => isset($contact['phone']) ? $contact['phone'] : '',
                    'referral_code' => $randomNumberMT.$user->id,
                    'user_id' => $user->id
                ]);
            } else {
                $member = $user->member;
            }

            $programLevel = ProgramLevel::with(['program'])->where('id', $programLevelId)->first();

            $alreadyEnrolledInProgramLevel = $member->reservations()->whereHas('session.programLevel', function ($query) use ($programLevelId) {
                $query->where('id', '!=', $programLevelId);
            })->whereHas('session', function ($query) use ($programLevel) {
                $query->where('program_id', '=', $programLevel->program->id);
            })->count();

            if($alreadyEnrolledInProgramLevel > 0){
                return false;
            }else{
                $reservation = Reservation::updateOrCreate([
                    'session_id' => $session->id,
                    'member_id' =>  $member->id
                ],[
                    'session_id' => $session->id,
                    'member_id' =>  $member->id,
                    'status' => Reservation::ACTIVE,
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'end_date' => $session->end_date
                ]);

                $member->locations()->sync([$location->id => [
                    'go_high_level_location_id' => $location->go_high_level_location_id,
                    'go_high_level_contact_id' => $contact['id']
                ]], false);

                DB::commit();
                return true;
            }
           
        } catch(\Exception $error) {
            DB::rollback();
            Log::info('===== Create New Member =====');
            Log::info(json_encode($contact));
            Log::info(json_encode($programLevelId));
            Log::info($error->getMessage());
            return false;
        }
    }

    public static function generateLocationLevelKey($location_id) {
        $ghlIntegration = Setting::where('name', 'ghl_integration')->first();
        $tokenObj = Http::withHeaders([
            'Authorization' => 'Bearer '.$ghlIntegration['value'],
            'Version' => '2021-07-28'                
        ])->asForm()->post('https://services.leadconnectorhq.com/oauth/locationToken', [
            'companyId' => $ghlIntegration['meta_data']['companyId'],
            'locationId' => $location_id,
        ]);

        if ($tokenObj->failed()) {
            $tokenObj->throw();
        }
        
        $url = 'https://services.leadconnectorhq.com/contacts/?locationId='.$location_id.'&limit=100';

        return $tokenObj->json();
    }

    public function getContacts(){
        try{
            $location = request()->location;
            $name = request()->name;
            $locationId = $location->go_high_level_location_id;
            
            $ghl_contacts = $this->ghlService->getContactsByName($locationId, $name);

            $members = $this->getMembers($location->id);
            $members = json_decode($members, true);

            foreach ($members as &$member) {
                $member['contactName'] = $member['name'];
                unset($member['name']);
            }

            $contacts = array_merge_recursive($ghl_contacts, ['contacts' => $members]);

            $uniqueContacts = [];
            foreach ($contacts['contacts'] as $contact) {
                $email = $contact['email'];
                if (!isset($uniqueContacts[$email])) {
                    $uniqueContacts[$email] = $contact;
                }
            }

            $uniqueContacts = array_values($uniqueContacts);
            $contacts['contacts'] = $uniqueContacts;

            return $contacts;
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getMembers($location_id){
        $members = Member::whereHas('locations', function ($query) use ($location_id) {
            $query->where('id', $location_id);
        })->get();

        return $members;
    }

    public function getMembersByProgram($id) {
        $location = request()->location;
        $locationId = $location->id;
        $program = Program::where('id', $id)->where('location_id', $locationId)->first();
        if ($program) {
            
            $activeSessions = Session::with(['reservations' => function($q) {
                $q->where('status', Reservation::ACTIVE);
            }, 'reservations.member','programLevel','program'])->whereHas('reservations', function($q) {
                $q->where('status', Reservation::ACTIVE);
            })->where('program_id', $program->id)->get();

            return $this->sendResponse(SessionResource::collection($activeSessions), 'program active members.');
        } else {
            return $this->sendError('Program not found.', 404);
        }
        
    }

    public function syncMembersByLocation($programId) {
        $delayTimeForEachLocation = 15;
        $reqCustomField = null;
        $location = request()->location;
        $program = Program::with('programLevels')->where('id',$programId)->first();
        $ghl_integration = Setting::where('name', 'ghl_integration')->first();
        $token = $ghl_integration['value'];
        $companyId = $ghl_integration['meta_data']['companyId'];
        if((Carbon::now()->diffInMinutes($program->last_sync_at) >= $delayTimeForEachLocation) || !$program->last_sync_at) {
            try {
                $tokenObj = Http::withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Version' => '2021-07-28'                
                ])->asForm()->post('https://services.leadconnectorhq.com/oauth/locationToken', [
                    'companyId' => $companyId,
                    'locationId' => $location->go_high_level_location_id,
                ]);
        
                if ($tokenObj->failed()) {
                    return $this->sendError('Something went wrong!', json_encode($tokenObj->json()));
                }
    
                $tokenObj = $tokenObj->json();
                
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
                    do {
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

                                $programLevelName = $parts[1];
                                $programName = $parts[0];

                                if($programName == $program->name) {
                                    foreach($program->programLevels as $programLevel) {
                                        if($programLevelName == $programLevel->name) {
                                            $programLevelId = $programLevel->id;
                                            MemberController::createMemberFromGHL($contact, $location ,$programLevelId);
                                        }
                                    }
                                }
                            }
                        }
                    } while($url);
                }
                $program->last_sync_at = now();
                $program->save();
    
            } catch(\Exception $error) {
                return $this->sendError('Something went wrong!', $error->getMessage());
            }
            return $this->sendResponse([], 'Members synced successfully');
        } else {
            return $this->sendError('Resync again in about '. $delayTimeForEachLocation - Carbon::now()->diffInMinutes($program->last_sync_at).' mins', []);
        }
    }

    public function addMemberManually($programLevelId, Request $request){
        try {
            $reqCustomField = null;
            $location = request()->location;
            $tokenObj = $this->generateLocationLevelKey($location->go_high_level_location_id);
            $programLevel = ProgramLevel::with('program')->where('id',$programLevelId)->first();
            $contact = $request->all();
            if(!isset($contact['email'])) {
                return $this->sendError('No email found against the contact!', json_encode($tokenObj));
            } else {
                $addMember = MemberController::createMemberFromGHL($contact, $location ,$programLevelId);
                
                if($addMember == true){
                    return $this->sendResponse('Success', 'Member synced successfully');
                }else{
                    return $this->sendError('Member is already enrolled in another program level', [], 400);
                }
            }
        } catch(Exception $error) {
            return $this->sendError('Something went wrong!', $error->getMessage());
        }
    }

    public function getMemberPrograms($member_id){
        try{
            $reservations = Reservation::where('member_id', $member_id)
                ->with('session.programLevel.program')
                ->get();

            $programs = [];

            foreach ($reservations as $reservation) {
                $session = $reservation->session;
                $programLevel = $session->programLevel;
                
                if($programLevel){
                    $program = $programLevel->program;
                    
                    if($program){
                        if (!isset($programs[$program->id])) {
                            $programs[$program->id] = $program;
                        }
                    }
                }

            }

            return $this->sendResponse(ProgramResource::collection($programs), 'Get programs related to specific member');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getProgramsForMemberNotEnrolled($member_id) {
        try{
            $location = request()->location;
            $reservations = Reservation::where('member_id', $member_id)
                ->with('session.programLevel.program')
                ->get();
            $programs = [];
            foreach ($reservations as $reservation) {
                $session = $reservation->session;
                $programLevel = $session->programLevel;
                
                if($programLevel){
                    $program = $programLevel->program;
                    
                    if($program){
                        $programs[] = $program->id;
                    }
                }
            }
            $programsObj = Program::with(['programLevels'])->whereNotIn('id', $programs)->where('location_id', $location->id)->get();
            return $this->sendResponse(ProgramResource::collection($programsObj), 'Get programs related to specific member');
        } catch(Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function memberPasswordReset($member_id){
        try{
            $member = Member::findOrFail($member_id);

            if(!$member){
                return $this->sendError("Member not found", [], 400);
            }

            $password = bcrypt("Monstro@".Carbon::now()->year);
            $member->user->password = $password;
            $member->user->save();

            return $this->sendResponse("Success", 'Member password updated successfully.');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}
