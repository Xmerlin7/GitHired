<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\WorkType;
class JobListing extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    protected $fillable = [
        'employer_id',
        'category_id',
        'title',
        'description',
        'experience',
        'salary_min',
        'salary_max',
        'work_type',
        'status',
        'deadline',
    ];

    /**
     * الوظيفة تنتمي لصاحب عمل (بروفايل الشركة)
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class, 'employer_id');
    }

    /**
     * الوظيفة تنتمي لقسم معين
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * الوظيفة الواحدة لها العديد من طلبات التوظيف
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_listing_id');
    }


    protected function casts(): array
    {
        return [
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'deadline' => 'date',
            'work_type' => WorkType::class,
        ];
    }
}
