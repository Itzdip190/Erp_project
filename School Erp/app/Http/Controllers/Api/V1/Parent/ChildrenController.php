<?php

namespace App\Http\Controllers\Api\V1\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChildrenController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $children = Student::where('guardian_email', $user->email)
            ->with(['class', 'section'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $children,
        ]);
    }

    public function profile(Request $request, int $id)
    {
        $user = $request->user();
        
        $student = Student::with(['class', 'section', 'academicSession', 'category', 'house'])
            ->findOrFail($id);

        if ($student->guardian_email !== $user->email && $student->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to child profile.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function documents(Request $request, int $id)
    {
        $user = $request->user();
        
        $student = Student::findOrFail($id);

        if ($student->guardian_email !== $user->email && $student->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to child documents.',
            ], 403);
        }

        $documents = StudentDocument::where('student_id', $id)->get();

        $disk = Storage::disk(config('filesystems.default'));
        
        $data = $documents->map(function ($doc) use ($disk) {
            $url = '';
            try {
                $url = $disk->temporaryUrl($doc->file_path, now()->addMinutes(60));
            } catch (\RuntimeException $e) {
                // Fallback for local filesystems which do not support S3 temporary URLs
                $url = $disk->url($doc->file_path);
            }

            return [
                'id' => $doc->id,
                'document_type' => $doc->document_type,
                'original_name' => $doc->original_name,
                'file_url' => $url,
                'created_at' => $doc->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
