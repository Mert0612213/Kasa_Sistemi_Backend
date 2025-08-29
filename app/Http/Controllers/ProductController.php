<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * GET /products -> list all products
     */
    public function index()
    {
        $products = Product::query()->orderBy('id', 'desc')->get();
        return response()->json($products);
    }

    /**
     * GET /products/{barcode} -> get product by barcode
     */
    public function show(string $barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    /**
     * POST /products -> create product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:255', 'unique:products,barcode'],
            'name'    => ['required', 'string', 'max:255'],
            'price'   => ['required', 'numeric', 'min:0'],
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * PUT /products/{barcode} -> update product by barcode
     */
    public function update(Request $request, string $barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'barcode' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('products', 'barcode')->ignore($product->id),
            ],
            'name'    => ['sometimes', 'string', 'max:255'],
            'price'   => ['sometimes', 'numeric', 'min:0'],
        ]);

        $product->fill($validated);
        $product->save();

        return response()->json($product);
    }
}
