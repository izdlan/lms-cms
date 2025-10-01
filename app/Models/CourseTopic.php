<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTopic extends Model
{
    protected $fillable = [
        'subject_code',
        'clo_code',
        'topic_title',
        'order'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    public function clo()
    {
        return $this->belongsTo(CourseClo::class, 'subject_code', 'subject_code')
                    ->where('clo_code', $this->clo_code);
    }
}

