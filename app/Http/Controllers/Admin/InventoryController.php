<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory items.
     */
    public function index()
    {
        $inventories = Inventory::orderBy('name')->get();
        return view('admin.inventory', compact('inventories')); // Use single blade
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        Inventory::create([
            'name' => $request->name,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item added successfully.');
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $inventory->update([
            'name' => $request->name,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Deduct stock when medicine is given to a patient
     */
    public function deductStock($medicineId, $quantity)
    {
        $medicine = Inventory::find($medicineId);

        if (!$medicine) {
            return false;
        }

        // Prevent negative stock
        if ($medicine->quantity < $quantity) {
            return false;
        }

        $medicine->quantity -= $quantity;
        $medicine->save();

        return true;
    }
}