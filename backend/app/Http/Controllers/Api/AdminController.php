<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function users(): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function applications(): JsonResponse
    {
        $applications = JobApplication::query()
            ->with([
                'candidate:id,name,email',
                'jobListing:id,title',
            ])
            ->latest()
            ->get()
            ->map(fn (JobApplication $application) => $this->formatApplication($application));

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => 'required|in:admin,employer,candidate',
        ]);

        $currentRole = $request->user()->role->value ?? $request->user()->role;
        if ($request->user()->is($user) && $validated['role'] !== $currentRole) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own admin role.',
            ], 422);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    public function deleteUser(Request $request, User $user): JsonResponse
    {
        if ($request->user()->is($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function updateApplicationStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $application = JobApplication::query()->findOrFail($id);
        $application->update(['status' => $validated['status']]);
        $application->load([
            'candidate:id,name,email',
            'jobListing:id,title',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => $this->formatApplication($application),
        ]);
    }

    private function formatApplication(JobApplication $application): array
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'created_at' => $application->created_at,
            'user' => $application->candidate,
            'job' => $application->jobListing,
        ];
    }
}
