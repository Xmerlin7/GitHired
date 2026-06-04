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
     * GET /api/jobs (مفتوح للكل مع الفلاتر)
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
     * POST /api/jobs (Employers Only)
     */
    public function store(Request $request): JsonResponse
    {
        // تقفيل الـ Validation بناءً على الـ ERD والـ Requirements
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'experience'  => 'required|string',
            'salary_min'  => 'nullable|integer',
            'salary_max'  => 'nullable|integer',
            'work_type'   => 'required|string', // مثلاً: remote, on-site, hybrid
            'deadline'    => 'required|date|after:today',
        ]);

        // تصليح الباصينج: بنباصي البيانات المفلترة + الـ ID بتاع اليوزر اللي عامل Login
        $job = $this->jobService->createJob($validated, $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الوظيفة بنجاح، بانتظار موافقة الإدارة.',
            'data' => $job
        ], 201);
    }

    /**
     * GET /api/jobs/{id}
     */
    public function show(string $id): JsonResponse
    {
        $job = $this->jobService->getJobById((int)$id);

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * PUT /api/jobs/{id} (Employers Only)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'experience'  => 'nullable|string',
            'salary_min'  => 'nullable|integer',
            'salary_max'  => 'nullable|integer',
            'work_type'   => 'nullable|string',
            'deadline'    => 'nullable|date|after:today',
        ]);

        $job = $this->jobService->updateJob((int)$id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الوظيفة بنجاح.',
            'data' => $job
        ]);
    }

    /**
     * DELETE /api/jobs/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $this->jobService->deleteJob((int)$id);

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully'
        ]);
    }

    /**
     * خاص بالـ Admin: الموافقة أو الرفض
     */
    public function changeStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // هنضيف دالة الـ updateJobStatus دي في السيرفيس حالا تحت
        $job = $this->jobService->updateJobStatus((int)$id, $validated['status']);

        return response()->json([
            'success' => true,
            'message' => "تم تغيير حالة الوظيفة إلى {$validated['status']}",
            'data' => $job
        ]);
    }
}
