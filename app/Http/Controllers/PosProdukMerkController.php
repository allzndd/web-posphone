<?php

namespace App\Http\Controllers;

use App\Models\PosProdukMerk;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PosProdukMerkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hasAccessRead = PermissionService::check('pos-produk-merk.read');
        $canDelete = PermissionService::check('pos-produk-merk.delete');
        $hasActions = $hasAccessRead || $canDelete;
        
        $query = PosProdukMerk::with('owner');
        
        // If superadmin, only show global items
        if (auth()->user()->role_id === 1) {
            $query->where('is_global', 1);
        }
        // If owner, filter by their ID or global items
        elseif (auth()->user()->role_id === 2) {
            $query->where(function($q) {
                $q->where('owner_id', auth()->id())
                  ->orWhere('is_global', 1);
            });
        } elseif (auth()->user()->role_id === 3) { // role_id 3 = admin
            // Admin can only see global items
            $query->where('is_global', 1);
        }
        
        // Fuzzy search - search across all database data before pagination
        if (request('nama')) {
            $searchTerm = request('nama');
            // Split search term into words for better fuzzy matching
            $words = explode(' ', trim($searchTerm));
            
            $query->where(function($q) use ($words, $searchTerm) {
                // First, try exact partial match on the full search term
                $q->where('merk', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama', 'LIKE', '%' . $searchTerm . '%');
                
                // Also match if all individual words are found
                foreach ($words as $word) {
                    $q->orWhere('merk', 'LIKE', '%' . $word . '%')
                      ->orWhere('nama', 'LIKE', '%' . $word . '%');
                }
            });
        }
        
        $perPage = request('per_page', 10);
        $merks = $query->paginate($perPage);
        return view('pages.pos-produk-merk.index', compact('merks', 'hasAccessRead', 'canDelete', 'hasActions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!PermissionService::check('pos-produk-merk.create')) {
            return redirect('/')->with('error', 'You do not have permission to create product brands');
        }
        // Get distinct merk values - show all for superadmin, filtered for owner/admin
        if (auth()->user()->role_id === 1) {
            // Superadmin: show all merks
            $merks = PosProdukMerk::where('is_global', 1)
                ->distinct()
                ->pluck('merk')
                ->filter(function($value) {
                    return !is_null($value) && $value !== '';
                })
                ->sort()
                ->values();
        } else {
            // Owner/Admin: show global merks and their own merks
            $merks = PosProdukMerk::where(function($q) {
                $q->where('is_global', 1)
                  ->orWhere('owner_id', auth()->id());
            })
                ->distinct()
                ->pluck('merk')
                ->filter(function($value) {
                    return !is_null($value) && $value !== '';
                })
                ->sort()
                ->values();
        }
        
        return view('pages.pos-produk-merk.create', compact('merks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!PermissionService::check('pos-produk-merk.create')) {
            return redirect('/')->with('error', 'You do not have permission to create product brands');
        }

        // Validate based on product type
        $productType = $request->input('product_type', 'electronic');
        
        if ($productType === 'service') {
            $validated = $request->validate([
                'product_type' => 'required|in:service',
                'service_name' => 'required|string|max:255',
                'service_duration' => 'nullable|numeric|min:0',
                'service_period' => 'nullable|in:days,weeks,months,years',
                'service_description' => 'nullable|string|max:1000',
            ]);
            
            $validated['merk'] = 'Service';
            $validated['nama'] = $validated['service_name'];
        } else {
            // Electronic or Accessories
            $validated = $request->validate([
                'product_type' => 'nullable|in:electronic,accessories',
                'merk' => 'required|string|max:255',
                'nama' => 'required|string|max:255',
            ]);
            
            if (empty($validated['product_type'])) {
                $validated['product_type'] = 'electronic';
            }
        }

        // For superadmin, set owner_id to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['owner_id'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set owner_id to their ID and is_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }
        // For admin, set is_global to 1
        elseif (auth()->user()->role_id === 3) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }

        PosProdukMerk::create($validated);

        return redirect()->route('pos-produk-merk.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PosProdukMerk $posProdukMerk)
    {
        // Allow edit if user has read permission
        if (!PermissionService::check('pos-produk-merk.read')) {
            return redirect('/')->with('error', 'You do not have permission to access product brands');
        }
        // Check authorization for owner - only can edit their own items or global items
        if (auth()->user()->role_id === 2 && $posProdukMerk->owner_id !== null && $posProdukMerk->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get distinct merk values - show all for superadmin, filtered for owner/admin
        if (auth()->user()->role_id === 1) {
            // Superadmin: show all merks
            $merks = PosProdukMerk::where('is_global', 1)
                ->distinct()
                ->pluck('merk')
                ->filter(function($value) {
                    return !is_null($value) && $value !== '';
                })
                ->sort()
                ->values();
        } else {
            // Owner/Admin: show global merks and their own merks
            $merks = PosProdukMerk::where(function($q) {
                $q->where('is_global', 1)
                  ->orWhere('owner_id', auth()->id());
            })
                ->distinct()
                ->pluck('merk')
                ->filter(function($value) {
                    return !is_null($value) && $value !== '';
                })
                ->sort()
                ->values();
        }
        
        $merk = $posProdukMerk;
        return view('pages.pos-produk-merk.edit', compact('merk', 'merks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  PosProdukMerk  $posProdukMerk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosProdukMerk $posProdukMerk)
    {
        // Allow update if user has read permission
        if (!PermissionService::check('pos-produk-merk.read')) {
            return redirect('/')->with('error', 'You do not have permission to access product brands');
        }
        // Check authorization for owner - only can update their own items or global items
        if (auth()->user()->role_id === 2 && $posProdukMerk->owner_id !== null && $posProdukMerk->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'merk' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
        ]);

        // For superadmin, set owner_id to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['owner_id'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set owner_id to their ID and is_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }
        // For admin, set is_global to 0
        elseif (auth()->user()->role_id === 3) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }

        $posProdukMerk->update($validated);

        return redirect()->route('pos-produk-merk.index')
            ->with('success', 'Produk Merk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosProdukMerk $posProdukMerk)
    {
        if (!PermissionService::check('pos-produk-merk.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete product brands');
        }
        // Check authorization for owner - only can delete their own items, not global items
        if (auth()->user()->role_id === 2 && $posProdukMerk->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $posProdukMerk->delete();

        return redirect()->route('pos-produk-merk.index')
            ->with('success', 'Produk Merk berhasil dihapus');
    }

    /**
     * Bulk delete product brands
     */
    public function bulkDestroy(Request $request)
    {
        if (!PermissionService::check('pos-produk-merk.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete product brands');
        }
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-produk-merk.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        $query = PosProdukMerk::whereIn('id', $ids);
        
        // If owner, only allow deleting their own items
        if (auth()->user()->role_id === 2) {
            $query->where('owner_id', auth()->id());
        }
        
        $query->delete();

        return redirect()->route('pos-produk-merk.index')
            ->with('success', 'Produk Merk berhasil dihapus');
    }

    /**
     * Quick store product name from AJAX modal (for product forms)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickStore(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            // For superadmin, set owner_id to null and is_global to 1
            if (auth()->user()->role_id === 1) {
                $merk = PosProdukMerk::create([
                    'owner_id' => null,
                    'nama' => $validatedData['nama'],
                    'is_global' => 1,
                ]);
            }
            // For owner, set owner_id to their ID and is_global to 0
            elseif (auth()->user()->role_id === 2) {
                $merk = PosProdukMerk::create([
                    'owner_id' => auth()->id(),
                    'nama' => $validatedData['nama'],
                    'is_global' => 0,
                ]);
            }
            // For admin, is_global should be 1
            elseif (auth()->user()->role_id === 3) {
                $merk = PosProdukMerk::create([
                    'owner_id' => null,
                    'nama' => $validatedData['nama'],
                    'is_global' => 1,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product Name created successfully!',
                'data' => $merk
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product name: ' . $e->getMessage()
            ], 500);
        }
    }
}
