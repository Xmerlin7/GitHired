<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'applications';

    protected $fillable = ['user_id', 'job_id', 'notes', 'status'];

    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jobListing()
    {
        return $this->belongsTo(JobListing::class, 'job_id');
    }
}
