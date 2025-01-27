<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index(){
        return view('product');
    }

    public function getData()
    {
        $categories = Category::with('products')->get();

        return response()->json(['categories' => $categories]);
    }

    public function getProduct($category_id)
    {
        $selectedProducts = DB::table('products')->where('category_id', '=', $category_id)->select('products.*')->get();

        return response()->json($selectedProducts);
    }

    public function createProduct(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'stock_quantity' => 'required',
        ]);

        Product::create($data);

        return redirect(route('product.view'));
    }

    public function editProduct(Request $request, $product_id)
    {

        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update($request->only(['product_name', 'description', 'price']));

        return response()->json(['message' => 'Updated Successfully']);
    }
}
