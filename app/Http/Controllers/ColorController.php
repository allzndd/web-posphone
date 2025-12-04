<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('name')->paginate(10);
        return view('pages.color.index', compact('colors'));
    }

    public function create()
    {
        return view('pages.color.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
        ]);

        $slug = Str::slug($validated['name']);

        Color::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('colors.index')
            ->with('success', 'Color berhasil ditambahkan');
    }

    public function edit(Color $color)
    {
        return view('pages.color.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
        ]);

        $slug = Str::slug($validated['name']);

        $color->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('colors.index')
            ->with('success', 'Color berhasil diperbarui');
    }

    public function destroy(Color $color)
    {
        $color->delete();

        return redirect()->route('colors.index')
            ->with('success', 'Color berhasil dihapus');
    }
}
