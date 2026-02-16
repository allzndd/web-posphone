<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = AppVersion::query();

        // Search filter
        if ($search = request('platform')) {
            $query->where('platform', 'like', "%{$search}%");
        }

        // Pagination
        $perPage = request('per_page', 15);
        $appVersions = $query->paginate($perPage);

        return view('app_versions.index', compact('appVersions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app_versions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50|unique:app_versions,platform',
            'latest_version' => 'required|string|max:20',
            'minimum_version' => 'required|string|max:20',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'store_url' => 'nullable|url|max:255',
        ]);

        $validated['maintenance_mode'] = $request->has('maintenance_mode');

        AppVersion::create($validated);

        return redirect()->route('app-version.index')
            ->with('success', 'App Version berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppVersion $appVersion)
    {
        return view('app_versions.show', compact('appVersion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppVersion $appVersion)
    {
        return view('app_versions.edit', compact('appVersion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppVersion $appVersion)
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:50', Rule::unique('app_versions')->ignore($appVersion->id)],
            'latest_version' => 'required|string|max:20',
            'minimum_version' => 'required|string|max:20',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'store_url' => 'nullable|url|max:255',
        ]);

        $validated['maintenance_mode'] = $request->has('maintenance_mode');

        $appVersion->update($validated);

        return redirect()->route('app-version.index')
            ->with('success', 'App Version berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppVersion $appVersion)
    {
        $appVersion->delete();

        return redirect()->route('app-version.index')
            ->with('success', 'App Version berhasil dihapus!');
    }

    /**
     * Bulk delete resources.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        // Handle JSON array format
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        if (empty($ids)) {
            return redirect()->route('app-version.index')
                ->with('error', 'Pilih minimal satu item untuk dihapus!');
        }

        AppVersion::whereIn('id', $ids)->delete();

        return redirect()->route('app-version.index')
            ->with('success', 'App Version berhasil dihapus!');
    }
}
