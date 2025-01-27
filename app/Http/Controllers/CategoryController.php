<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(){
        return view('category');
    }

    public function viewCategory()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function createCategory(Request $request)
    {
        $data = $request->validate([
            'category_name' => 'required'
        ]);

        Category::create($data);

    }

    public function editCategory() {




    }

    public function deleteCategory() {}

    public function updateCategory() {}
}


