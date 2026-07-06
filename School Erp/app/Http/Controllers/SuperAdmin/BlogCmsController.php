<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BlogCmsController extends Controller
{
    protected string $filePath = 'blog_posts.json';

    /**
     * Get all blog posts from JSON file.
     */
    private function getPosts(): array
    {
        if (Storage::disk('local')->exists($this->filePath)) {
            $posts = json_decode(Storage::disk('local')->get($this->filePath), true);
            return is_array($posts) ? $posts : [];
        }

        // Default seeding with one dummy post
        $defaultPosts = [
            [
                'id' => 1,
                'title' => 'EduCore ERP Version 4.0 Released!',
                'summary' => 'Discover the new features including AI-grounded chatbot support and automated fees notification models.',
                'content' => 'We are excited to launch EduCore ERP 4.0. In this release, we have focused on platform speed, white-label UI upgrades, and fully automated SMS gateway controllers.',
                'author' => 'SuperAdmin Office',
                'status' => 'published',
                'cover_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600',
                'created_at' => Carbon::now()->subDays(5)->format('M d, Y'),
            ]
        ];
        Storage::disk('local')->put($this->filePath, json_encode($defaultPosts, JSON_PRETTY_PRINT));
        return $defaultPosts;
    }

    /**
     * Save posts list to JSON file.
     */
    private function savePosts(array $posts): void
    {
        Storage::disk('local')->put($this->filePath, json_encode(array_values($posts), JSON_PRETTY_PRINT));
    }

    /**
     * Display posts list.
     */
    public function index(): View
    {
        $posts = $this->getPosts();
        return view('superadmin.blog-cms.index', compact('posts'));
    }

    /**
     * Store new post.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'author' => 'required|string|max:100',
            'status' => 'required|in:draft,published',
            'cover_url' => 'nullable|url',
        ]);

        $posts = $this->getPosts();
        
        $newPost = [
            'id' => time(), // Unique timestamp ID
            'title' => $request->title,
            'summary' => $request->summary,
            'content' => $request->content,
            'author' => $request->author,
            'status' => $request->status,
            'cover_url' => $request->cover_url ?? 'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=600',
            'created_at' => Carbon::now()->format('M d, Y'),
        ];

        $posts[] = $newPost;
        $this->savePosts($posts);

        return redirect()->route('superadmin.blog-cms.index')
            ->with('success', 'Article created successfully.');
    }

    /**
     * Update post.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'author' => 'required|string|max:100',
            'status' => 'required|in:draft,published',
            'cover_url' => 'nullable|url',
        ]);

        $posts = $this->getPosts();
        $found = false;

        foreach ($posts as $key => $post) {
            if ($post['id'] == $id) {
                $posts[$key]['title'] = $request->title;
                $posts[$key]['summary'] = $request->summary;
                $posts[$key]['content'] = $request->content;
                $posts[$key]['author'] = $request->author;
                $posts[$key]['status'] = $request->status;
                if ($request->filled('cover_url')) {
                    $posts[$key]['cover_url'] = $request->cover_url;
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            return redirect()->route('superadmin.blog-cms.index')
                ->with('error', 'Article not found.');
        }

        $this->savePosts($posts);

        return redirect()->route('superadmin.blog-cms.index')
            ->with('success', 'Article updated successfully.');
    }

    /**
     * Remove post.
     */
    public function destroy($id): RedirectResponse
    {
        $posts = $this->getPosts();
        $filtered = array_filter($posts, function ($post) use ($id) {
            return $post['id'] != $id;
        });

        $this->savePosts(array_values($filtered));

        return redirect()->route('superadmin.blog-cms.index')
            ->with('success', 'Article deleted successfully.');
    }
}
