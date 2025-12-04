<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradeIn;
use App\Models\Customer;
use App\Models\Product;

class TradeInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = TradeIn::with(['customer', 'newProduct']);

        // Search by product name, old phone, or old IMEI
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($subQuery) use ($q) {
                $subQuery->where('old_phone', 'like', "%{$q}%")
                         ->orWhere('old_imei', 'like', "%{$q}%")
                         ->orWhereHas('newProduct', function($prodQuery) use ($q) {
                             $prodQuery->where('name', 'like', "%{$q}%")
                                      ->orWhere('imei', 'like', "%{$q}%");
                         });
            });
        }

        $tradeins = $query->latest()->paginate(10);

        // Calculate statistics
        $totalTradeIns = TradeIn::count();
        $totalProfit = TradeIn::with('newProduct')->get()->sum(function($tradein) {
            $newProductPrice = $tradein->newProduct->sell_price ?? 0;
            return $newProductPrice - $tradein->old_value;
        });

        return view('tradeins.index', compact('tradeins', 'totalTradeIns', 'totalProfit'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('tradeins.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'old_phone' => 'required|string|max:255',
            'old_imei' => 'nullable|string|max:255',
            'old_value' => 'required|numeric|min:0',
            'new_product_id' => 'required|exists:products,id',
            'date' => 'required|date',
        ]);

        TradeIn::create($data);
        return redirect()->route('tradein.index')->with('success', 'Trade-in created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tradein = TradeIn::with(['customer', 'newProduct'])->findOrFail($id);
        return view('tradeins.show', compact('tradein'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tradein = TradeIn::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('tradeins.edit', compact('tradein', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tradein = TradeIn::findOrFail($id);
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'old_phone' => 'required|string|max:255',
            'old_imei' => 'nullable|string|max:255',
            'old_value' => 'required|numeric|min:0',
            'new_product_id' => 'required|exists:products,id',
            'date' => 'required|date',
        ]);

        $tradein->update($data);
        return redirect()->route('tradein.index')->with('success', 'Trade-in updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tradein = TradeIn::findOrFail($id);
        $tradein->delete();
        return redirect()->route('tradein.index')->with('success', 'Trade-in deleted');
    }
}
