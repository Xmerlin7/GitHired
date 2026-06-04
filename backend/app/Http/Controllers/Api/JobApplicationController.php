<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobApplicationController extends Controller
{
    protected JobApplicationService $applicationService;

    public function __construct(JobApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function store(Request $request, $jobId): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|min:10',
            'resume_url' => 'nullable|string',
        ]);

        $application = $this->applicationService->apply($request->user()->id, $jobId, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Your application has been submitted',
            'data' => $application
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role->value === 'candidate' || $user->role === 'candidate') {
            $applications = $this->applicationService->getCandidateApplications($user->id);
        } else {
            $applications = $this->applicationService->getEmployerApplications($user->id);
        }

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,pending',
        ]);

        $application = $this->applicationService->updateStatus($id, $validated['status'], $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'data' => $application
        ]);
    }
}
