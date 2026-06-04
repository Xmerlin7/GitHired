<?php

namespace App\Services;

use App\Models\JobListing;
use Illuminate\Pagination\LengthAwarePaginator;

class JobListingService
{
    public function getAllJobs(array $filters = []): LengthAwarePaginator
    {
        $query = JobListing::with(['employer', 'category'])
            ->where('status', 'approved')
            ->latest();

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Work Type Filter
        if (isset($filters['work_type'])) {
            $query->where('work_type', $filters['work_type']);
        }

        // Category Filter
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Search Filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        // Experience Filter
        if (isset($filters['experience'])) {
            $query->where('experience', $filters['experience']);
        }

        // Salary Range Filter
        if (isset($filters['salary_min'])) {
            $query->where('salary_max', '>=', $filters['salary_min']);
        }

        if (isset($filters['salary_max'])) {
            $query->where('salary_min', '<=', $filters['salary_max']);
        }

        //  Location Filter ()
        // if (isset($filters['location'])) {
        //     $query->where('location', 'like', "%{$filters['location']}%");
        // }

        // Employer Filter
        if (isset($filters['employer_id'])) {
            $query->where('employer_id', $filters['employer_id']);
        }

        // Pagination with custom per_page
        $perPage = $filters['per_page'] ?? 10;
        if ($perPage > 100) {
            $perPage = 100;
        }

        return $query->paginate($perPage);
    }

    public function getJobById(int $id): JobListing
    {
        return JobListing::with(['employer', 'category'])
            ->where('status', 'approved')
            ->findOrFail($id);
    }

    // CRUD Methods
    public function createJob(array $data, int $employerId): JobListing
    {
        return JobListing::create(array_merge($data, [
            'employer_id' => $employerId,
            'status' => 'pending',
        ]));
    }

    public function updateJob(int $id, array $data): JobListing
    {
        $job = JobListing::findOrFail($id);
        $job->update($data);

        return $job->fresh(['employer', 'category']);
    }

    public function deleteJob(int $id): bool
    {
        $job = JobListing::findOrFail($id);

        return $job->delete();
    }

    public function updateJobStatus(int $id, string $status): JobListing
    {
        $job = JobListing::findOrFail($id);
        $job->update(['status' => $status]);

        return $job->fresh(['employer', 'category']);
    }
}
