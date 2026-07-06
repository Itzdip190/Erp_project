<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolAiSetting;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\View\View;

class AiController extends Controller
{
    // ─── AI Overview: all schools + their AI status ───────────
    public function index(): View
    {
        $schools = School::leftJoin('school_ai_settings', 'schools.id', '=', 'school_ai_settings.school_id')
            ->select(
                'schools.id',
                'schools.name',
                'schools.code',
                'schools.status',
                'school_ai_settings.enabled as ai_enabled',
                'school_ai_settings.ai_model',
                'school_ai_settings.chatbot_name',
                'school_ai_settings.ai_provider',
                \Illuminate\Support\Facades\DB::raw("CASE WHEN school_ai_settings.api_key IS NOT NULL AND school_ai_settings.api_key != '' THEN 1 ELSE 0 END as has_api_key")
            )
            ->orderBy('schools.created_at', 'desc')
            ->get();

        $totalWithAi     = $schools->where('ai_enabled', 1)->count();
        $totalWithKey    = $schools->where('has_api_key', 1)->count();
        $totalSchools    = $schools->count();

        return view('superadmin.ai.index', compact('schools', 'totalWithAi', 'totalWithKey', 'totalSchools'));
    }

    // ─── Toggle AI enabled for a school ───────────────────────
    public function toggleSchool(Request $request): JsonResponse
    {
        $schoolId = (int) $request->input('school_id');
        $enabled  = (bool) $request->input('enabled');

        $setting = SchoolAiSetting::firstOrCreate(
            ['school_id' => $schoolId],
            ['chatbot_name' => 'AI Assistant', 'ai_model' => 'gemini-1.5-flash', 'ai_provider' => 'gemini', 'max_tokens' => 1024]
        );
        $setting->enabled = $enabled;
        $setting->save();

        return response()->json(['success' => true, 'enabled' => $setting->enabled]);
    }

    // ─── SuperAdmin AI Chat Page ───────────────────────────────
    public function chat(): View
    {
        $globalModel    = config('services.superadmin_ai.model', 'gemini-1.5-flash');
        $globalProvider = config('services.superadmin_ai.provider', 'gemini');
        $hasKey         = (bool) config('services.superadmin_ai.key');
        return view('superadmin.ai.chat', compact('globalModel', 'globalProvider', 'hasKey'));
    }

    // ─── SuperAdmin AI Chat Send ───────────────────────────────
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);
        $message  = trim($request->input('message'));
        $history  = $request->input('history', []);

        $apiKey   = config('services.superadmin_ai.key');
        $model    = config('services.superadmin_ai.model', 'gemini-1.5-flash');
        $provider = config('services.superadmin_ai.provider', 'gemini');

        if (!$apiKey) {
            return response()->json([
                'reply' => '⚠️ No SuperAdmin AI key configured. Add `SUPERADMIN_AI_API_KEY` to your `.env` file.'
            ]);
        }

        $systemPrompt = 'You are an intelligent SuperAdmin AI assistant for SchoolCloud ERP — a multi-tenant SaaS platform for schools. '
            . 'Help the super admin with platform management, school onboarding, subscription issues, data analysis, and system operations. '
            . 'Be concise, technical, and professional.';

        try {
            if ($provider === 'gemini') {
                $contents = [];
                foreach ($history as $h) {
                    $contents[] = ['role' => ($h['role'] === 'user' ? 'user' : 'model'), 'parts' => [['text' => $h['content'] ?? '']]];
                }
                $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

                $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey;
                $response = Http::timeout(30)->post($url, [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents'           => $contents,
                    'generationConfig'   => ['maxOutputTokens' => 1024, 'temperature' => 0.7],
                ]);
                if ($response->failed()) {
                    return response()->json(['reply' => '❌ Gemini error: ' . $response->json('error.message', 'Unknown')], 500);
                }
                return response()->json(['reply' => $response->json('candidates.0.content.parts.0.text', 'No response.')]);
            } else {
                $messages = [['role' => 'system', 'content' => $systemPrompt]];
                foreach ($history as $h) $messages[] = ['role' => $h['role'] ?? 'user', 'content' => $h['content'] ?? ''];
                $messages[] = ['role' => 'user', 'content' => $message];

                $response = Http::withToken($apiKey)->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model, 'messages' => $messages, 'max_tokens' => 1024,
                ]);
                if ($response->failed()) {
                    return response()->json(['reply' => '❌ OpenAI error: ' . $response->json('error.message', 'Unknown')], 500);
                }
                return response()->json(['reply' => $response->json('choices.0.message.content', 'No response.')]);
            }
        } catch (\Exception $e) {
            return response()->json(['reply' => '❌ Error: ' . $e->getMessage()], 500);
        }
    }
}
