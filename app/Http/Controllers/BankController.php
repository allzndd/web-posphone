<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the banks.
     */
    public function index()
    {
        $banks = Bank::orderBy('created_at', 'desc')->paginate(15);
        return view('pages.bank.index', compact('banks'));
    }

    /**
     * Show the form for creating a new bank.
     */
    public function create()
    {
        return view('pages.bank.create');
    }

    /**
     * Store a newly created bank in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bank' => 'required|string|max:100',
            'nama_rekening' => 'required|string|max:150',
            'nomor_rekening' => 'required|string|max:50',
        ], [
            'nama_bank.required' => 'Nama bank harus diisi',
            'nama_bank.max' => 'Nama bank tidak boleh lebih dari 100 karakter',
            'nama_rekening.required' => 'Nama rekening harus diisi',
            'nama_rekening.max' => 'Nama rekening tidak boleh lebih dari 150 karakter',
            'nomor_rekening.required' => 'Nomor rekening harus diisi',
            'nomor_rekening.max' => 'Nomor rekening tidak boleh lebih dari 50 karakter',
        ]);

        Bank::create($validated);

        return redirect()->route('bank.index')
            ->with('success', 'Rekening berhasil ditambahkan');
    }

    /**
     * Display the specified bank.
     */
    public function show(Bank $bank)
    {
        return view('pages.bank.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified bank.
     */
    public function edit(Bank $bank)
    {
        return view('pages.bank.edit', compact('bank'));
    }

    /**
     * Update the specified bank in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'nama_bank' => 'required|string|max:100',
            'nama_rekening' => 'required|string|max:150',
            'nomor_rekening' => 'required|string|max:50',
        ], [
            'nama_bank.required' => 'Nama bank harus diisi',
            'nama_bank.max' => 'Nama bank tidak boleh lebih dari 100 karakter',
            'nama_rekening.required' => 'Nama rekening harus diisi',
            'nama_rekening.max' => 'Nama rekening tidak boleh lebih dari 150 karakter',
            'nomor_rekening.required' => 'Nomor rekening harus diisi',
            'nomor_rekening.max' => 'Nomor rekening tidak boleh lebih dari 50 karakter',
        ]);

        $bank->update($validated);

        return redirect()->route('bank.index')
            ->with('success', 'Rekening berhasil diperbarui');
    }

    /**
     * Remove the specified bank from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('bank.index')
            ->with('success', 'Rekening berhasil dihapus');
    }
}
