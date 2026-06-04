<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Validation\ValidationException;

class JobApplicationService
{
    public function apply(int $candidateId, int $jobId, array $data): JobApplication
    {
        $job = JobListing::findOrFail($jobId);

        $exists = JobApplication::where('user_id', $candidateId)
            ->where('job_id', $jobId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'job_id' => ['لقد قمت بالتقديم على هذه الوظيفة بالفعل.'],
            ]);
        }

        return JobApplication::create([
            'user_id' => $candidateId,
            'job_id'  => $jobId,
            'notes'   => $data['notes'] ?? null,
            'status'  => 'pending', // هتبدأ pending أوتوماتيك هنا
        ]);
    }

    public function getCandidateApplications(int $candidateId)
    {
        return JobApplication::where('user_id', $candidateId)
            ->with('jobListing')
            ->latest()
            ->get();
    }

    public function getEmployerApplications(int $employerId)
    {
        return JobApplication::whereHas('jobListing', function ($query) use ($employerId) {
            $query->where('user_id', $employerId);
        })->with(['candidate.candidateProfile', 'jobListing'])->latest()->get();
    }

    public function updateStatus(int $applicationId, string $status, int $employerId): JobApplication
    {
        $application = JobApplication::with('jobListing')->findOrFail($applicationId);

        if ($application->jobListing->user_id !== $employerId) {
            abort(403, 'غير مصرح لك بتعديل حالة هذا الطلب.');
        }

        $application->update(['status' => $status]);
        return $application;
    }
}
