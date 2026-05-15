<?php

namespace App\Http\Controllers\Api;

use App\Services\JobListingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobListingController extends Controller
{
    public function __construct(
        protected JobListingService $jobService
    ) {}

    /**
     * GET /api/jobs
     */
    public function index(Request $request): JsonResponse
    {
        $jobs = $this->jobService->getAllJobs($request->all());

        return response()->json([
            'success' => true,
            'data' => $jobs->items(),
            'pagination' => [
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
            ]
        ]);
    }

    /**
     * POST /api/jobs
     */
    public function store(Request $request): JsonResponse
    {
        // TODO: Add validation
        $job = $this->jobService->createJob($request->all());

        return response()->json([
            'success' => true,
            'data' => $job
        ], 201);
    }

    /**
     * GET /api/jobs/{id}
     */
    public function show(string $id): JsonResponse
    {
        $job = $this->jobService->getJobById($id);

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * PUT/PATCH /api/jobs/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // TODO: Add validation
        $job = $this->jobService->updateJob($id, $request->all());

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * DELETE /api/jobs/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $this->jobService->deleteJob($id);

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully'
        ]);
    }
}
