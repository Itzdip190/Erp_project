<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\GalleryPost;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display the Gallery / Post An Event page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $school = $user ? $user->school : null;
        $schoolId = $user->school_id ?? ($school->id ?? null);

        // Fetch Academic Sessions for dropdowns
        $academicSessions = $schoolId ? AcademicSession::where('school_id', $schoolId)->get() : collect();
        $currentSession = $schoolId ? AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() : null;

        // Query posts safely in case table or connection has issues
        try {
            $query = GalleryPost::query();
            if ($schoolId) {
                $query->where('school_id', $schoolId);
            }
            $query->orderBy('created_at', 'desc');

            $previousPosts = (clone $query)->where('type', 'event')->where('is_scheduled', false)->paginate(10, ['*'], 'prev_page');
            $scheduledPosts = (clone $query)->where('type', 'event')->where('is_scheduled', true)->get();
            $achievements = (clone $query)->where('type', 'achievement')->get();
            $totalPreviousCount = (clone $query)->where('type', 'event')->where('is_scheduled', false)->count();
        } catch (\Throwable $e) {
            $previousPosts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url(), 'pageName' => 'prev_page']);
            $scheduledPosts = collect();
            $achievements = collect();
            $totalPreviousCount = 0;
        }

        // Social media settings from institute info safely
        $socialMedia = [];
        if ($school) {
            $udiseData = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);
            $socialMedia = is_array($udiseData) && isset($udiseData['social_media']) ? $udiseData['social_media'] : [];
        }

        return view('school.gallery.post_event', compact(
            'school',
            'academicSessions',
            'currentSession',
            'previousPosts',
            'scheduledPosts',
            'achievements',
            'totalPreviousCount',
            'socialMedia'
        ));
    }

    /**
     * Store a new Event post.
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            'academic_year' => 'nullable|string|max:100',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'recipients'    => 'required|string',
            'photos.*'      => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('gallery/events', 'public');
                $attachments[] = $path;
            }
        }

        $user = Auth::user();
        $userName = $user->name ?? 'Admin';
        $schoolId = $user->school_id ?? ($user->school->id ?? 1);

        GalleryPost::create([
            'school_id'     => $schoolId,
            'posted_by'     => $userName,
            'type'          => 'event',
            'academic_year' => $request->academic_year,
            'title'         => $request->title,
            'description'   => $request->description,
            'attachments'   => $attachments,
            'recipients'    => $request->recipients,
            'is_scheduled'  => $request->has('schedule_for_later') && $request->schedule_for_later == '1',
            'scheduled_at'  => $request->has('schedule_for_later') ? now()->addDays(1) : null,
        ]);

        return back()->with('success', 'Event posted successfully!');
    }

    /**
     * Store a new Achievement post.
     */
    public function storeAchievement(Request $request)
    {
        $request->validate([
            'academic_year' => 'nullable|string|max:100',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'recipients'    => 'required|string',
            'photos.*'      => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('gallery/achievements', 'public');
                $attachments[] = $path;
            }
        }

        $user = Auth::user();
        $userName = $user->name ?? 'Admin';
        $schoolId = $user->school_id ?? ($user->school->id ?? 1);

        GalleryPost::create([
            'school_id'     => $schoolId,
            'posted_by'     => $userName,
            'type'          => 'achievement',
            'academic_year' => $request->academic_year,
            'title'         => $request->title,
            'description'   => $request->description,
            'attachments'   => $attachments,
            'recipients'    => $request->recipients,
            'show_popup'    => $request->has('show_popup') && $request->show_popup == '1',
        ]);

        return back()->with('success', 'Achievement posted successfully!');
    }

    /**
     * Delete a post.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? ($user->school->id ?? 1);
        $post = GalleryPost::where('school_id', $schoolId)->findOrFail($id);

        if (!empty($post->attachments)) {
            foreach ($post->attachments as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        $post->delete();

        return back()->with('success', 'Post deleted successfully!');
    }
}
