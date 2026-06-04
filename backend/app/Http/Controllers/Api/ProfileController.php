<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * جلب بيانات البروفايل الحالي للمستخدم (Candidate أو Employer)
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        // بناءً على الـ Role بنعمل تحميل (Load) للعلاقة الخاصة به طبقاً للـ ERD
        if ($user->role === 'candidate' || $user->role->value === 'candidate') {
            $user->load('candidateProfile'); // تأكد من اسم العلاقة في موديل User
        } else {
            $user->load('employerProfile');  // تأكد من اسم العلاقة في موديل User
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * تحديث بيانات البروفايل ديناميكياً حسب الـ Role وحقول الـ ERD
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. لو المستخدم كانديديت (Candidate)
        if ($user->role === 'candidate' || $user->role->value === 'candidate') {
            $validated = $request->validate([
                'resume_url'       => 'nullable|string', // للتبسيط حالياً، وقريباً نخليه يرفع ملف PDF بجد
                'portfolio_link'   => 'nullable|url',
                'years_experience' => 'nullable|integer|min:0',
                'phone_number'     => 'nullable|string|max:20',
            ]);

            // بنحدث جدول الـ candidate_profiles المربوط باليوزر ده
            $user->candidateProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ملفك الشخصي بنجاح كباحث عن عمل.',
                'data' => $user->load('candidateProfile')
            ]);
        }

        // 2. لو المستخدم صاحب عمل (Employer)
        $validated = $request->validate([
            'company_name'     => 'required|string|max:255',
            'company_logo_url' => 'nullable|string',
            'website'          => 'nullable|url',
            'company_bio'      => 'nullable|string',
        ]);

        // بنحدث جدول الـ employer_profiles المربوط باليوزر ده
        $user->employerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الشركة بنجاح.',
            'data' => $user->load('employerProfile')
        ]);
    }
}
