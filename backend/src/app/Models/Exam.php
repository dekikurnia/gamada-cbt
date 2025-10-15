<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration',
        'class_id', 
        'teacher_id',
        'token',
        'token_expired_at'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function userStatuses()
    {
        return $this->hasMany(ExamUserStatus::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 🧠 event: otomatis isi token kalau kosong
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exam) {
            if (empty($exam->token)) {
                $exam->token = strtoupper(Str::random(5)); 
            }

            if (empty($exam->token_expired_at)) {
                $exam->token_expired_at = Carbon::now()->addMinutes(15);
            }
        });
    }
}
