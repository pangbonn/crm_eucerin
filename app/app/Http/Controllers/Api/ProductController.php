<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $imageUrl = null;
                if ($product->image) {
                    $imageUrl = filter_var($product->image, FILTER_VALIDATE_URL)
                        ? $product->image
                        : url(Storage::url($product->image));
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image_url' => $imageUrl,
                ];
            })
            ->values();

        return response()->json($products);
    }
}
