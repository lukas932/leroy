<?php

namespace App\Http\Controllers\Api;

use Excel;
use App\Product;
use App\Jobs\ProcessProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Products extends Controller
{
    /**
     * @SWG\Info(title="Leroy Merlin - Atualização de produtos via planilha", version="0.0.1")
     */

    /**
     * @SWG\Get(
     *   path="/api/products",
     *   summary="List products",
     *   @SWG\Response(
     *      response=200,
     *      description="A list with products or empty array[]"
     *   ),
     *   @SWG\Response(
     *      response=500,
     *      description="Intenal server error"
     *   )
     * )
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * @SWG\Post(
     *   path="/api/products",
     *   summary="Create a product",
     *   @SWG\Parameter(
	 * 		name="file",
	 * 		in="formData",
	 * 		required=true,
	 * 		type="file",
	 * 		description="excel file with products",
     *      @SWG\Schema(type="file")
	 * 	 ),
     *   @SWG\Response(
     *      response=200,
     *      description="Products created"
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Invalid file"
     *   )
     * )
     */
    public function store(Request $request)
    {
        $plan = $request->file('file');
        $ext = ['xls', 'xlsx', 'xlt'];
        if ($plan->isValid() and in_array($plan->extension(), $ext)) {
            $data = Excel::load($plan->path())->get();
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $product = Product::where('lm', '=', $value->lm)->first();
                    if (!$product) {
                        $product = new Product();
                    }
                    $product->lm = $value->lm;
                    $product->name = $value->name;
                    $product->free_shipping = $value->free_shipping;
                    $product->description = $value->description;
                    $product->price = $value->price;
                    $product->save();
                }
                return response()->json([
                    'success' => 'Products created'
                ], 200);
            }
        }
        return response()->json([
            'error' => 'Invalid file'
        ], 400);
    }

    /**
     * @SWG\Get(
     *   path="/api/products/{lm}",
     *   summary="Show a product",
     *   @SWG\Parameter(
	 * 		name="lm",
	 * 		in="path",
	 * 		required=true,
	 * 		type="integer",
	 * 		description="product lm",
	 * 		),
     *   @SWG\Response(
     *      response=200,
     *      description="Product"
     *   ),
     *   @SWG\Response(
     *      response=404,
     *      description="Product not found"
     *   )
     * )
     */
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

    /**
     * @SWG\Put(
     *   path="/api/products/{lm}",
     *   summary="Update a product",
     *   @SWG\Parameter(
	 * 		name="lm",
	 * 		in="path",
	 * 		required=true,
	 * 		type="integer",
	 * 		description="product lm",
	 * 	 ),
     *   @SWG\Parameter(
	 * 		name="product",
	 * 		in="body",
	 * 		required=true,
	 * 		description="Product object",
     *      @SWG\Schema(type="object")
	 * 	 ),
     *   @SWG\Response(
     *      response=200,
     *      description="Product"
     *   ),
     *   @SWG\Response(
     *      response=404,
     *      description="Product not found"
     *   )
     * )
     */
    public function update(Request $request, $lm)
    {
        $product = Product::where('lm', '=', $lm)->first();

        if ($product) {
            $product->lm = $request->input('lm') ?? $product->lm;
            $product->name = $request->input('name') ?? $product->name;
            $product->free_shipping = $request->input('free_shipping') ?? $product->free_shipping;
            $product->description = $request->input('description') ?? $product->description;
            $product->price = $request->input('price') ?? $product->price;
            $product->save();
            return response()->json($product, 200);
        }

        return response()->json([
            'error' => 'Product not found'
        ], 404);
    }

    /**
     * @SWG\Delete(
     *   path="/api/products/{lm}",
     *   summary="Delete a product",
     *   @SWG\Parameter(
	 * 	    name="lm",
	 * 		in="path",
	 * 		required=true,
	 * 		type="integer",
	 * 		description="product lm",
     *    ),
     *    @SWG\Response(
     *       response=200,
     *       description="Product deleted"
     *     ),
     *     @SWG\Response(
     *        response=404,
     *        description="Product not found"
     *     ),
     * )
     */
    public function destroy($lm)
    {
        $product = Product::where('lm', '=', $lm)->first();
        
        if ($product) {
            $product->delete();
            return response()->json([
                'success' => 'Product deleted'
            ], 200);
        }

        return response()->json([
            'error' => 'Product not found'
        ], 404);
    }

}


