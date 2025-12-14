<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        return Product::where('status', ProductStatus::Active)
            ->with('resident')
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|file|image|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
        }

        $product = Product::create([
            'resident_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'photo_url' => $path ? asset('storage/' . $path) : null,
            'status' => ProductStatus::Active,
        ]);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return $product->load('resident');
    }
}
