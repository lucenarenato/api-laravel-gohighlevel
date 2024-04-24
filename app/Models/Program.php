<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'name',
        'description',
        'avatar',
        'last_sync_at'
    ];

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function programLevels(){
        return $this->hasMany(ProgramLevel::class);
    }

    public function achievements(){
        return $this->hasMany(Achievement::class);
    }

    public function members(){
        return $this->belongsToMany(Location::class, 'member_programs', 'program_id', 'member_id');
    }

    public function sessions(){
        return $this->hasMany(Session::class);
    }

    public function activeSessions()
    {
        return $this->sessions()->whereHas('programLevel')->with(['reservations', 'reservations.member', 'programLevel'])->where('status', Session::ACTIVE)->latest()->get();
    }

    public function monthlyRetentionRate()
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();

        $newStudents = $this->sessions()->where('status', Session::ACTIVE)
        ->whereHas('reservations', function ($query) use ($currentMonth){
            $query->where('status', Reservation::ACTIVE)->where('start_date', '>=', $currentMonth);
        })
        ->withCount('reservations')->get()
        ->sum('reservations_count');

        $retainedStudents = $this->sessions()->where('status', Session::ACTIVE)
        ->whereHas('reservations', function ($query) use ($previousMonth, $currentMonth) {
            $query->where('status', Reservation::ACTIVE)
            ->where('start_date', '>=', $previousMonth)->where('start_date', '<', $currentMonth);;
        })
        ->withCount('reservations')->get()
        ->sum('reservations_count');

        if ($newStudents > 0) {
            $retentionRate = $retainedStudents ? ($retainedStudents / $newStudents) * 100 : 100;
        } else {
            $retentionRate = 0;
        }

        return $retentionRate;
    }

    public function totalActiveStudents()
    {
        $activeReservationsCount = $this->sessions()
        ->whereHas('programLevel', function ($query) {
            $query->whereNull('deleted_at');
        })
        ->where('status', Session::ACTIVE)
        ->withCount(['reservations' => function ($query) {
            $query->where('status', Reservation::ACTIVE)
                ->select(DB::raw('COUNT(DISTINCT member_id)'));
        }])
        ->get()
        ->sum('reservations_count');
        return $activeReservationsCount;
    }

    public function totalStudents()
    {
        // Need to fix this afterwards
        $activeReservationsCount = $this->sessions()
        ->with('reservations')
        ->get()
        ->pluck('reservations.*.member_id') // Extracting all member_ids
        ->flatten() // Flattening the nested arrays
        ->unique(); // Getting unique member_ids
    
        return $activeReservationsCount->count();
    }
}