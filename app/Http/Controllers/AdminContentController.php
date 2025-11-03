<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $imageUrl = null;
        
        // Handle file upload
        if ($request->hasFile('image')) {
            $imageUrl = $this->uploadImageFile($request->file('image'));
        }

        // Get or create admin record for the current user
        $adminId = $this->getOrCreateAdminId(Auth::id());

        HomePageContent::create([
            'section_name' => $request->input('section_name'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image_url' => $imageUrl,
            'metadata' => $request->input('metadata', []),
            'sort_order' => $request->input('sort_order'),
            'is_active' => $request->boolean('is_active', true),
            'admin_id' => $adminId
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $imageUrl = $content->image_url; // Keep existing image if no new one uploaded
        
        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($content->image_url && Storage::disk('public')->exists(str_replace('/storage/', '', $content->image_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $content->image_url));
            }
            $imageUrl = $this->uploadImageFile($request->file('image'));
        }

        $content->update([
            'section_name' => $request->input('section_name'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image_url' => $imageUrl,
            'metadata' => $request->input('metadata', []),
            'sort_order' => $request->input('sort_order'),
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

    public function uploadImage(Request $request)
    {
        $this->checkAdminAccess();
        
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $imageUrl = $this->uploadImageFile($request->file('image'));
            
            return response()->json([
                'success' => true,
                'url' => $imageUrl,
                'message' => 'Image uploaded successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    private function uploadImageFile($file, $folder = 'home-page-images')
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        Log::info('Uploading file', [
            'original_name' => $file->getClientOriginalName(),
            'generated_filename' => $filename,
            'folder' => $folder,
            'size' => $file->getSize()
        ]);
        
        $path = $file->storeAs($folder, $filename, 'public');
        Log::info('File stored', ['path' => $path]);
        
        $fullUrl = '/storage/' . $path;
        Log::info('Generated URL', ['url' => $fullUrl]);
        
        return $fullUrl;
    }

    private function getOrCreateAdminId($userId)
    {
        // First, try to find an existing admin record for this user
        $admin = \App\Models\Admin::where('email', Auth::user()->email)->first();
        
        if ($admin) {
            return $admin->id;
        }
        
        // If no admin record exists, create one
        $admin = \App\Models\Admin::create([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'password' => Auth::user()->password, // Use the same password
            'role' => 'admin',
            'is_active' => true
        ]);
        
        return $admin->id;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $imageUrl = null;
        
        // Handle file upload
        if ($request->hasFile('image')) {
            Log::info('Announcement image upload started', [
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_size' => $request->file('image')->getSize(),
                'mime_type' => $request->file('image')->getMimeType()
            ]);
            $imageUrl = $this->uploadImageFile($request->file('image'), 'announcements');
            Log::info('Announcement image upload completed', ['image_url' => $imageUrl]);
        }

        // Get or create admin record for the current user
        $adminId = $this->getOrCreateAdminId(Auth::id());

        PublicAnnouncement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category' => $request->input('category'),
            'priority' => $request->input('priority'),
            'image_url' => $imageUrl,
            'published_at' => $request->input('published_at') ?? now(),
            'expires_at' => $request->input('expires_at'),
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $request->boolean('is_active', true),
            'admin_id' => $adminId
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $imageUrl = $announcement->image_url; // Keep existing image if no new one uploaded
        
        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_url && Storage::disk('public')->exists(str_replace('/storage/', '', $announcement->image_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $announcement->image_url));
            }
            $imageUrl = $this->uploadImageFile($request->file('image'), 'announcements');
        }

        $announcement->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category' => $request->input('category'),
            'priority' => $request->input('priority'),
            'image_url' => $imageUrl,
            'published_at' => $request->input('published_at') ?? $announcement->published_at,
            'expires_at' => $request->input('expires_at'),
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
