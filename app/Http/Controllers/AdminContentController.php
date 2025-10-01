<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HomePageContent;
use App\Models\PublicAnnouncement;

class AdminContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkAdminAccess()
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->isStaff())) {
            abort(403, 'Unauthorized access.');
        }
    }

    // Home Page Content Management
    public function homePageIndex()
    {
        $this->checkAdminAccess();
        $contents = HomePageContent::ordered()->get();
        return view('admin.home-page.index', compact('contents'));
    }

    public function homePageCreate()
    {
        $this->checkAdminAccess();
        return view('admin.home-page.create');
    }

    public function homePageStore(Request $request)
    {
        $this->checkAdminAccess();
        $request->validate([
            'section_name' => 'required|string|max:255|unique:home_page_contents,section_name',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image_url' => 'nullable|url',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        HomePageContent::create([
            'section_name' => $request->section_name,
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'metadata' => $request->metadata ?? [],
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true),
            'admin_id' => Auth::id()
        ]);

        return redirect()->route('admin.home-page.index')->with('success', 'Home page content created successfully!');
    }

    public function homePageEdit(HomePageContent $content)
    {
        $this->checkAdminAccess();
        return view('admin.home-page.edit', compact('content'));
    }

    public function homePageUpdate(Request $request, HomePageContent $content)
    {
        $this->checkAdminAccess();
        $request->validate([
            'section_name' => 'required|string|max:255|unique:home_page_contents,section_name,' . $content->id,
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image_url' => 'nullable|url',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $content->update([
            'section_name' => $request->section_name,
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'metadata' => $request->metadata ?? [],
            'sort_order' => $request->sort_order,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.home-page.index')->with('success', 'Home page content updated successfully!');
    }

    public function homePageDestroy(HomePageContent $content)
    {
        $this->checkAdminAccess();
        $content->delete();
        return redirect()->route('admin.home-page.index')->with('success', 'Home page content deleted successfully!');
    }

    // Public Announcements Management
    public function announcementsIndex()
    {
        $this->checkAdminAccess();
        $announcements = PublicAnnouncement::with('admin')->latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function announcementsCreate()
    {
        $this->checkAdminAccess();
        return view('admin.announcements.create');
    }

    public function announcementsStore(Request $request)
    {
        $this->checkAdminAccess();
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'image_url' => 'nullable|url',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        PublicAnnouncement::create([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->priority,
            'image_url' => $request->image_url,
            'published_at' => $request->published_at ?? now(),
            'expires_at' => $request->expires_at,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $request->boolean('is_active', true),
            'admin_id' => Auth::id()
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully!');
    }

    public function announcementsEdit(PublicAnnouncement $announcement)
    {
        $this->checkAdminAccess();
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function announcementsUpdate(Request $request, PublicAnnouncement $announcement)
    {
        $this->checkAdminAccess();
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'image_url' => 'nullable|url',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->priority,
            'image_url' => $request->image_url,
            'published_at' => $request->published_at ?? $announcement->published_at,
            'expires_at' => $request->expires_at,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function announcementsDestroy(PublicAnnouncement $announcement)
    {
        $this->checkAdminAccess();
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully!');
    }
}
