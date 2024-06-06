<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Member;
use Illuminate\Http\Request;

class ProductCountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('countProducts');
    }

    public function countProducts()
    {
        $categories = Category::all();
        
        $category_count = [];
        $total_products = 0;

        foreach ($categories as $category) {
            $category_count[$category->nama_kategori] = 0;
        }

        foreach ($categories as $category) {
            $productCount = Product::where('id_kategori', $category->id)->count();
            $category_count[$category->nama_kategori] = $productCount;
            $total_products += $productCount;
        }

        // Menghitung total member
        $total_members = Member::count();

        $category_count['total_products'] = $total_products;
        $category_count['total_members'] = $total_members;

        return response()->json($category_count);
    }
}
