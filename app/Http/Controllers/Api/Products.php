<?php

namespace App\Http\Controllers\Api;

use App\Product;
use App\Jobs\ProcessProduct;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Controllers\Controller;

class Products extends Controller
{
    
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->path();
            $this->dispatch(new ProcessProduct($path));
        }
        return response()->json([
            'success' => 'Products in processing'
        ], 200);
    }

    public function show($lm)
    {
        $product = Product::where('lm', '=', $lm)->first();

        if ($product) {
            return response()->json($product, 200);
        }

        return response()->json([
            'error' => 'Product not found'
        ], 404);
    }

    public function update(ProductRequest $request, $lm)
    {
        $product = Product::where('lm', '=', $lm)->first();

        if ($product) {
            if ($request->has('lm')) { $product->lm = $request->input('lm'); }
            if ($request->has('name')) { $product->name = $request->input('name'); }
            if ($request->has('free_shipping')) { $product->free_shipping = $request->input('free_shipping'); }
            if ($request->has('description')) { $product->description = $request->input('description'); }
            if ($request->has('price')) { $product->price = $request->input('price'); }
            $product->save();
            return response()->json($product, 200);
        }

        return response()->json([
            'error' => 'Product not found'
        ], 404);
    }

    public function destroy($lm)
    {
        $product = Product::where('lm', '=', $lm)->first();
        
        if ($product) {
            return response()->json([], 204);
        }

        return response()->json([
            'error' => 'Product can not be deleted'
        ], 500);
    }

}


