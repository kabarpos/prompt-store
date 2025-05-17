<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::latest()->get();
        return Inertia::render('Dashboard/Pages/Index', [
            'pages' => $pages
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Pages/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required|in:published,draft'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        Page::create($validated);

        return redirect()->route('dashboard.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return Inertia::render('Dashboard/Pages/Edit', [
            'page' => $page
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required|in:published,draft'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $page->update($validated);

        return redirect()->route('dashboard.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('dashboard.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Display the home page.
     */
    public function home()
    {
        return Inertia::render('public/home/Index');
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return Inertia::render('public/about/Index');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return Inertia::render('public/contact/Index');
    }
}
