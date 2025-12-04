<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorageController extends Controller
{
    public function index()
    {
        $storages = Storage::orderBy('name')->paginate(10);
        return view('pages.storage.index', compact('storages'));
    }

    public function create()
    {
        return view('pages.storage.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:storages,name',
        ]);

        $slug = Str::slug($validated['name']);

        Storage::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('storages.index')
            ->with('success', 'Storage berhasil ditambahkan');
    }

    public function edit(Storage $storage)
    {
        return view('pages.storage.edit', compact('storage'));
    }

    public function update(Request $request, Storage $storage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:storages,name,' . $storage->id,
        ]);

        $slug = Str::slug($validated['name']);

        $storage->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('storages.index')
            ->with('success', 'Storage berhasil diperbarui');
    }

    public function destroy(Storage $storage)
    {
        $storage->delete();

        return redirect()->route('storages.index')
            ->with('success', 'Storage berhasil dihapus');
    }
}
