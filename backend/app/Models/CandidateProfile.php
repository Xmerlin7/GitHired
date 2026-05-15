<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
    public function user()
{
    // الملف الشخصي "ينتمي إلى" مستخدم واحد
    return $this->belongsTo(User::class);
}

}
