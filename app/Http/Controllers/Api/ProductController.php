<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Liste tous les produits.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::with([
                'category',
                'images',
                'sizes',
                'colors',
                'details',
                'about'
            ])->get();

            return response()->json([
                'status' => 'success',
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du chargement des produits'
            ], 500);
        }
    }

    /**
     * Affiche un produit spécifique.
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with([
                'category',
                'images',
                'sizes',
                'colors',
                'details',
                'about'
            ])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => new ProductDetailResource($product)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produit non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du chargement du produit'
            ], 500);
        }
    }
}
