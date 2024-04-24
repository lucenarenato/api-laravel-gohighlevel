<?php

namespace App\Http\Controllers\Api\Vendor;


use App\Models\Program;
use App\Models\ProgramLevel;
use App\Models\Session;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ProgramLevelRequest;
use App\Http\Requests\ProgramLevelUpdateRequest;
use App\Http\Requests\ProgramStoreRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Http\Resources\Member\CheckInResource;
use App\Http\Resources\Vendor\ProgramLevelResource;
use App\Http\Resources\Vendor\ProgramResource;
use App\Models\AchievementActions;
use App\Models\Action;
use App\Models\CheckIn;
use App\Models\Member;
use App\Models\MemberAchievement;
use App\Models\MemberRewardClaim;
use App\Models\Reservation;
use App\Models\Reward;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ProgramController extends BaseController
{
    public function getProgramsByLocation(){
        $location = request()->location;
        //Code commented out below becuase auth guard is not applied anymore.
        // if($location->vendor_id != auth()->user()->vendor->id) {
        //     return $this->sendError('Vendor not authorize, Please contact admin', [], 403);
        // }
        $programs = Program::where('location_id', $location->id);
        if(isset(request()->type)) {
            if(request()->type == 0) {
                $programs = $programs->whereNotNull('deleted_at')->withTrashed();
            }
        }
        $programs = $programs->paginate(25);
        $data = [
            'programs' => ProgramResource::collection($programs),
            'pagination' => [
                'current_page' => $programs->currentPage(),
                'per_page' => $programs->perPage(),
                'total' => $programs->total(),
                'prev_page_url' => $programs->previousPageUrl(),
                'next_page_url' => $programs->nextPageUrl(),
                'first_page_url' => $programs->url(1),
                'last_page_url' => $programs->url($programs->lastPage()),
            ],
        ];
        return $this->sendResponse($data, 'Get programs related to specific location');
    }


    public function lastTenAttendance($member_id, $program_id){
        try{
            $attendances = CheckIn::whereHas('reservation', function($query) use ($member_id){
                $query->where('member_id', $member_id);
            })
            ->whereHas('reservation.session', function($query) use ($program_id){
                $query->where('program_id', $program_id);
            })
            ->with(['reservation.session'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

            return $this->sendResponse(CheckInResource::collection($attendances), 'Get attendances related to member and program.');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }
    
    public function getProgramById($id){
        try{
            $location = request()->location;
            $program = Program::with(['programLevels','programLevels.sessions'])->where('id',$id)->where('location_id', $location->id)->first();

            if(!$program){
                return $this->sendError("Program doesnot exist", [], 400);
            }
            //Code commented out below becuase auth guard is not applied anymore.
            // $location = $program->location;
            // if($location->vendor_id != auth()->user()->vendor->id) {
            //     return $this->sendError('Vendor not authorize, Please contact admin', [], 403);
            // }
            return $this->sendResponse(new ProgramResource($program), 'Get programs related to specific location');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getArchiveProgramLevelsWithSession($programId){
        try{
            $location = request()->location;
            $program = Program::with(['programLevels' => function ($query) {
                $query->withTrashed();
                $query->where('deleted_at', '!=', NULL);
            }])->where('id', $programId)->first();

            return $this->sendResponse(new ProgramResource($program), 'Getting archived levels related to program');
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function addProgram(ProgramStoreRequest $request){
        $location = request()->location;
        try{
            //Code commented out below becuase auth guard is not applied anymore.
            // $location = Location::find($request->location_id);
            // if($location->vendor_id != auth()->user()->vendor->id) {
            //     return $this->sendError('Vendor not authorize, Please contact admin', [], 403);
            // }
            DB::beginTransaction();
            $program = Program::create([
                'location_id' => $location->id,
                'name' => $request->program_name,
                'description' => $request->description,
                'avatar' => $request->avatar ?? null,
            ]);

            $parent_id = null;
            foreach($request->sessions as $session){
                $program_level = ProgramLevel::create([
                    'name' => $session['program_level_name'],
                    'program_id' => $program->id,
                    'parent_id' => $parent_id,
                    'capacity' => $session['capacity'],
                    'min_age' => $session['min_age'],
                    'max_age' => $session['max_age'],
                ]);
                
                $program_level->save();
                
                $parent_id = $program_level->id;

                $now = Carbon::now();
                $formattedNow = $now->format('Y-m-d');

                $twoYearsLater = $now->addYears(2);
                $twoYearsLaterformattedDate = $twoYearsLater->format('Y-m-d');

                $session = Session::create([
                    'program_id' => $program->id,
                    'program_level_id' => $program_level->id,
                    'duration_time' => $session['duration_time'],
                    'start_date' => $formattedNow,
                    'end_date' => $twoYearsLaterformattedDate,
                    'monday' => $session['monday'] ?? null,
                    'tuesday' => $session['tuesday'] ?? null,
                    'wednesday' => $session['wednesday'] ?? null,
                    'thursday' => $session['thursday'] ?? null,
                    'friday' => $session['friday'] ?? null,
                    'saturday' => $session['saturday'] ?? null,
                    'sunday' => $session['sunday'] ?? null,
                    'status' => \App\Models\Session::ACTIVE
                ]);
            }
            DB::commit();
            
            $program = Program::with('programLevels.sessions')->where('id', $program->id)->first();
            return $this->sendResponse(new ProgramResource($program), 'Program created successfully.');
        }catch(Exception $e){
            DB::rollBack();
            Log::info('===== ProgramController - addProgram() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function addProgramLevel(ProgramLevelRequest $request){
        try{
            DB::beginTransaction();
            $parent_id = null;
            $programId = $request->program_id;
            $oldProgramLevelId = ProgramLevel::where('program_id', $programId)->latest()->first();
            
            if($oldProgramLevelId){
                $parent_id = $oldProgramLevelId->id;
            }

            $program_level = ProgramLevel::create([
                'name' => $request->name,
                'program_id' => $programId,
                'parent_id' => $parent_id,
                'capacity' => $request->capacity,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
            ]);

            $program_level->save();

            foreach($request->sessions as $session){
                $now = Carbon::now();
                $formattedNow = $now->format('Y-m-d');

                $twoYearsLater = $now->addYears(2);
                $twoYearsLaterformattedDate = $twoYearsLater->format('Y-m-d');

                $session = Session::create([
                    'program_id' => $programId,
                    'program_level_id' => $program_level->id,
                    'duration_time' => $session['duration_time'],
                    'start_date' => $formattedNow,
                    'end_date' => $twoYearsLaterformattedDate,
                    'monday' => $session['monday'] ?? null,
                    'tuesday' => $session['tuesday'] ?? null,
                    'wednesday' => $session['wednesday'] ?? null,
                    'thursday' => $session['thursday'] ?? null,
                    'friday' => $session['friday'] ?? null,
                    'saturday' => $session['saturday'] ?? null,
                    'sunday' => $session['sunday'] ?? null,
                    'status' => \App\Models\Session::ACTIVE
                ]);
            }
            DB::commit();
            
            return $this->sendResponse(new ProgramLevelResource($program_level), 'Program Level created successfully.');
        }catch(Exception $e){
            DB::rollBack();
            Log::info('===== ProgramController - addProgramLevel() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function getProgramDetails($programId){
        try{
            $location = request()->location;
            $program = Program::with('members')->where('id', $programId)->where('location_id', $location->id)->first();

            if(!$program){
                return $this->sendError('Program does not exist.', [], 400);
            }

            $data = [
                'totalStudents' => $program->totalStudents(),
                'totalActiveStudents' => $program->totalActiveStudents(),
                'monthlyRetentionRate' => $program->monthlyRetentionRate()
            ];

            return $this->sendResponse($data, 'Program related information related to program and location.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function programLevelActiveSessions($programLevelId){
        try{
            $programLevel = ProgramLevel::where('id', $programLevelId)->first();
            $formated_sessions = $programLevel->getFormattedSessions();
            return $formated_sessions;
        }catch(Exception $e){
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function update(ProgramUpdateRequest $request, Program $program){
        try{
            $program->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            $program = Program::with('programLevels')->where('id',$program->id)->first();

            return $this->sendResponse(new ProgramResource($program), 'Program updated successfully.');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info('===== ProgramController - programUpdate() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function programImageUpdate(Request $request, Program $program){
        try{
            $avatar = $request->file('avatar');
            $imgPath = 'program-images/';
            $imageUrl = null;

            if($avatar){
                $fileName = $program->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path($imgPath), $fileName);
                $imageUrl = getenv('APP_URL') .$imgPath. $fileName;
            }

            $program->update([
                'avatar' => isset($imageUrl) ? $imageUrl : $program->avatar,
            ]);

            return $this->sendResponse(new ProgramResource($program), 'Program image updated successfully.');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info('===== ProgramController - programImageUpdate() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function programLevelUpdate(ProgramLevelUpdateRequest $request, ProgramLevel $programLevel){
        try{
            DB::beginTransaction();

            $programLevel->update([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age
            ]);
            
            foreach ($request->sessions as $session) {
                $sessionId = isset($session['id']) ? $session['id'] : null;
                if(isset($session['duration_time'])){
                    Session::updateOrCreate(
                        ['id' => $sessionId],
                        [
                            'duration_time' => $session['duration_time'],
                            'monday' => isset($session['monday']) ? $session['monday'] : null,
                            'tuesday' => isset($session['tuesday']) ? $session['tuesday'] : null,
                            'wednesday' => isset($session['wednesday']) ? $session['wednesday'] : null,
                            'thursday' => isset($session['thursday']) ? $session['thursday'] : null,
                            'friday' => isset($session['friday']) ? $session['friday'] : null,
                            'saturday' => isset($session['saturday']) ? $session['saturday'] : null,
                            'sunday' => isset($session['sunday']) ? $session['sunday'] : null,
                        ]
                    );
                }
            }
            DB::commit();
            $programLevel = ProgramLevel::with('sessions')->where('id', $programLevel->id)->first();
            return $this->sendResponse(new ProgramLevelResource($programLevel), 'Program level updated successfully.');
        }catch (Exception $e) {
            DB::rollBack();
            Log::info('===== ProgramController - programLevelUpdate() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function oldUpdate(ProgramUpdateRequest $request, Program $program){
        try {
            DB::beginTransaction();
            $program->update([
                'name' => $request->program_name,
                'description' => $request->description,
                'avatar' => $request->avatar ?? $program->avatar,
            ]);

            foreach ($request->sessions as $session) {
                $programLevelId = isset($session['program_level_id']) ? $session['program_level_id'] : null;
                $programLevel = ProgramLevel::updateOrCreate(
                    ['id' => $programLevelId],
                    [
                        'program_id' => $program->id,
                        'name' => $session['program_level_name'],
                        'capacity' => $session['capacity'],
                        'min_age' => $session['min_age'],
                        'max_age' => $session['max_age']
                    ]
                );

                $programLevel->save();

                $sessionId = isset($session['id']) ? $session['id'] : null;
                if(isset($session['duration_time']) && isset($session['start_date']) && isset($session['end_date'])){
                    Session::updateOrCreate(
                        ['id' => $sessionId],
                        [
                            'program_level_id' => $programLevel->id,
                            'program_id' => $program->id,
                            'duration_time' => $session['duration_time'],
                            'start_date' => $session['start_date'],
                            'end_date' => $session['end_date'],
                            'monday' => isset($session['monday']) ? $session['monday'] : null,
                            'tuesday' => isset($session['tuesday']) ? $session['tuesday'] : null,
                            'wednesday' => isset($session['wednesday']) ? $session['wednesday'] : null,
                            'thursday' => isset($session['thursday']) ? $session['thursday'] : null,
                            'friday' => isset($session['friday']) ? $session['friday'] : null,
                            'saturday' => isset($session['saturday']) ? $session['saturday'] : null,
                            'sunday' => isset($session['sunday']) ? $session['sunday'] : null,
                        ]
                    );
                }
            }

            DB::commit();
            $program = Program::with('programLevels.sessions')->where('id', $program->id)->first();
            return $this->sendResponse(new ProgramResource($program), 'Program updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::info('===== ProgramController - updateProgram() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function assignProgramLevelToMember($programLevelId, $memberId){
        try{
            // Finding Program Level
            $programLevelCheck = ProgramLevel::find($programLevelId);

            if(!$programLevelCheck){
                return $this->sendError('Program level does not exist. Cannot assign a level to member', [], 400);
            }

            // Getting current reservations for the members
            $reservations = Reservation::with('session')->whereHas('session', function ($query) {
                $query->where('status', Session::ACTIVE);
            })->where('member_id', $memberId)->where('status', Reservation::ACTIVE)->get();

            //Checking for reservations
            if(count($reservations) > 0){
                // Getting program level id
                $currentProgramLevelId = $reservations[0]->session->programLevel->id;
                
                // Checking if member is currently enrolled in this program level or not
                if($currentProgramLevelId == $programLevelId){
                    // If member is already enrolled return back with a message
                    return $this->sendError('Member already assigned to this level.', [], 400);
                }elseif($currentProgramLevelId < $programLevelId){ // Checking if program level is bigger than the current enrolled program or not
                    DB::beginTransaction();
                    //Getting next level sessions
                    $nextLevelSession = Session::where('program_level_id', $programLevelId)->where('status', Session::ACTIVE)->first();
                    if($nextLevelSession) {
                        // Create reservation if not present or update if already present
                        Reservation::updateOrCreate([
                            'session_id' => $nextLevelSession->id,
                            'member_id' =>  $memberId
                        ],[
                            'session_id' => $nextLevelSession->id,
                            'member_id' =>  $memberId,
                            'status' => Reservation::ACTIVE,
                            'start_date' => Carbon::today()->format('Y-m-d'),
                            'end_date' => $nextLevelSession->end_date
                        ]);
                        
                    }
                    
                    $currentSession = Session::where('program_level_id', $currentProgramLevelId)->where('status', Session::ACTIVE)->first();
                    if($currentSession) {
                        // Updating current reservations sessions status to completed
                        Reservation::where('session_id', $currentSession->id)->where('member_id', $memberId)->update(['status' => Reservation::COMPLETED]);
                    }
                    DB::commit();
                    $this->levelCompletionReward($memberId);
                    return $this->sendResponse("Success", "Member assigned to next level successfully");
                }else{
                    DB::beginTransaction();
                    // Getting old level sessions
                    $oldSession = Session::where('program_level_id', $programLevelId)->where('status', Session::ACTIVE)->first();
                    if($oldSession) {
                        // Updating old reservations status to active
                        Reservation::updateOrCreate([
                            'session_id' => $oldSession->id,
                            'member_id' =>  $memberId
                        ],[
                            'session_id' => $oldSession->id,
                            'member_id' =>  $memberId,
                            'status' => Reservation::ACTIVE,
                            'start_date' => Carbon::today()->format('Y-m-d'),
                            'end_date' => $oldSession->end_date
                        ]);
                    }
                    
                    // Getting current sessions 
                    $currentSession = Session::where('program_level_id', $currentProgramLevelId)->where('status', Session::ACTIVE)->first();
                    if($currentSession) {
                        // Updating current reservations sessions status to inactive
                        $reservation = Reservation::where('session_id', $currentSession->id)->where('member_id', $memberId)->first();
                        $reservation->status = Reservation::INACTIVE;
                        $reservation->save();
                    }
                    DB::commit();
                    $this->levelCompletionReward($memberId);
                    return $this->sendResponse("Success", "Member assigned to previous level successfully");
                }
            }else{
                $programLevel = ProgramLevel::find($programLevelId);

                if(!$programLevel){
                    return $this->sendError('Program level does not exist. Cannot assign a level to member', [], 400);
                }

                $session = $programLevel->sessions[0];

                Reservation::updateOrCreate([
                    'session_id' => $session->id,
                    'member_id' =>  $memberId
                ],[
                    'session_id' => $session->id,
                    'member_id' =>  $memberId,
                    'status' => Reservation::ACTIVE,
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'end_date' => $session->end_date
                ]);
                
                $this->levelCompletionReward($memberId);
                return $this->sendResponse("Success", "Member assigned to program level and reservation created successfully");
            }
        }catch (Exception $e) {
            DB::rollBack();
            Log::info('===== ProgramController - assignProgramLevelToMember() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function assignProgramToMember($programId, $memberId){
        try{
            // Finding Program Level
            $program = Program::with('programLevels')->where('id', $programId)->first();

            if(!$program){
                return $this->sendError('Program level does not exist. Cannot assign a level to member', [], 400);
            }
            // Getting current reservations for the members
            $reservations = Reservation::whereHas('session.programLevel.program', function ($query) use ($program) {
                $query->where('id', $program->id);
            })->where('member_id', $memberId)->get();

            //Checking for reservations
            if(count($reservations) > 0) {
                return $this->sendError('Program already assigned', [], 400);
            }else{
                $programLevel = $program->programLevels->whereNull('parent_id')->first();
                $session = Session::where('program_level_id', $programLevel->id)->where('status', Session::ACTIVE)->first();
                Reservation::updateOrCreate([
                    'session_id' => $session->id,
                    'member_id' =>  $memberId
                ],[
                    'session_id' => $session->id,
                    'member_id' =>  $memberId,
                    'status' => Reservation::ACTIVE,
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'end_date' => $session->end_date
                ]);
                return $this->sendResponse("Success", "Member assigned to program and reserved successfully");
            }
        }catch (Exception $e) {
            Log::info('===== ProgramController - assignProgramLevelToMember() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function unAssignMemberFromProgram($programId, $memberId){
        try{
            $sessionIds = Session::where('program_id', $programId)
                ->pluck('id')->toArray();
    
            $reservations = Reservation::whereIn('session_id', $sessionIds)
                ->where('member_id', $memberId)
                ->delete();
    
            return $this->sendResponse("Success", "The member has been removed from the program successfully");
        } catch (Exception $e) {
            Log::info('===== ProgramController - unAssignProgramLevel() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
    
    public function programCompletion($programId, $memberId){
        try{
            $sessionIds = Session::where('program_id', $programId)
                ->where('status', Session::ACTIVE)
                ->pluck('id')
                ->toArray();

            Reservation::whereIn('session_id', $sessionIds)
                ->where('member_id', $memberId)
                ->where('status', Reservation::ACTIVE)
                ->update([
                    'status' => Reservation::COMPLETED
                ]);

            return $this->sendResponse("Success", "The program for the member has been completed successfully");
        } catch (Exception $e) {
            Log::info('===== ProgramController - programCompletion() - error =====');
            Log::info($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
    
    public function levelCompletionReward($member_id){
        // Finding a achivement action related to defined no of level cleared
        $action = Action::where('name', Action::LEVEL_ACHIEVED)->first();
        $member = Member::find($member_id);

        // Getting achievement action
        $achievement_action = AchievementActions::with(['achievement'])->whereHas('achievement' , function ($q) use ($member){
            $q->where('program_id', $member->reservations[0]->session->program->id);
        })->where('action_id', $action->id)->first();

        // If achievement action present
        if($achievement_action){
            // Checking criteria count
            $eligibilityCriteria = $achievement_action->count;
            $completedProgramLevels = Reservation::where('member_id', $member_id)->where('status', Reservation::COMPLETED)
            ->with('session.programLevel')->get()->pluck('session.programLevel') // Extract programLevel from reservations
            ->unique('id') // Ensure unique programLevel instances
            ->count();

            // Checking for eligibility
            if($completedProgramLevels >= $eligibilityCriteria){
                $existingMemberAchievement = MemberAchievement::where('achievement_id', $achievement_action->achievement->id)
                    ->where('member_id', $member_id)->first();

                // Check for if member achievment is already exist
                if(!$existingMemberAchievement){
                    //Creating a new achievment for member
                    $member_achievement = MemberAchievement::create([
                        'achievement_id' => $achievement_action->achievement->id, 
                        'member_id' => $member_id, 
                        'status' => 1, 
                        'note' => 'Achievement accomplished on number of levels completion', 
                        'date_achieved' => now()
                    ]);

                    // Fidning member in order to get the current member achieved points
                    $member = Member::find($member_id);
                    $currentPoints = $member->current_points;

                    if($member_achievement){
                        // Updating mmeber overall points
                        $currentPoints = $currentPoints + $achievement_action->achievement->reward_points;
                    }

                    $member->current_points = $currentPoints;
                    $member->save();
                }
            }
        }
    }

    public function delete($programId){
        try{
            $program = Program::with('programLevels.sessions')->find($programId);
            
            if(!$program){
                return $this->sendError("Program not found", [], 400);
            }

            foreach ($program->programLevels as $programLevel) {
                foreach ($programLevel->sessions as $sessions) {
                    foreach ($sessions->reservations as $reservation) {
                        if ($reservation->status === \App\Models\Reservation::ACTIVE) {
                            return $this->sendError("Cannot delete program. Active reservations exist in the program sessions.", [], 400);
                        }
                    }
                }
            }

            $program->delete();
            return $this->sendResponse("Success", "Program deleted successfully");
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function deleteProgramLevel($programLevelId){
        try{
            $programLevel = ProgramLevel::with('sessions.reservations')->find($programLevelId);
            
            if(!$programLevel){
                return $this->sendError("Program Level not exist", [], 400);
            }

            foreach ($programLevel->sessions as $sessions) {
                foreach ($sessions->reservations as $reservation) {
                    if ($reservation->status === \App\Models\Reservation::ACTIVE) {
                        return $this->sendError("Cannot delete program level. Active reservations exist in the program level sessions.", [], 400);
                    }
                }
            }

            $programLevel->delete();
            return $this->sendResponse("Success", "Program Level deleted successfully");
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function getProgramLevels($programId){
        try{
            $programLevels = ProgramLevel::where('program_id', $programId)->get();

            if(count($programLevels) > 0){
                return $this->sendResponse(ProgramLevelResource::collection($programLevels), 'Program levels related to specific program');
            }else{
                return $this->sendError('No program levels found for this program', [], 400);
            }
        }catch(Exception $error){
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}
