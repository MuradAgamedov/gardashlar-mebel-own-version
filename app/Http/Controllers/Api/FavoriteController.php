<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Product;
use App\Traits\LocaleFromHeader;

class FavoriteController extends Controller
{
    use LocaleFromHeader;

    /**
     * Favorit əlavə et
     */
    public function addFavorite(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to complete this transaction!"
            ], 401);
        }

        // Favorite əlavə et və ya varsa təkrar etmə
        $favorite = Favorite::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
            ],
            []
        );

        return response()->json([
            'success' => true,
            'message' => 'Product added to favorites successfully!',
            'data' => $favorite
        ]);
    }

    /**
     * Favoritləri gətir
     */
    public function getFavorites(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in!"
            ], 401);
        }

        $favorites = $user->favorites()->with(['product.projectCategory', 'product.images'])->get();

        $favoriteData = $favorites->map(function ($fav) use ($languageId) {
            return [
                'favorite_id' => $fav->id,
                'product' => $fav->product ? $fav->product->apiData($languageId) : null,
                'color_id' => $fav->color_id,
            ];
        });

        return response()->json([
            'success' => true,
            "data" => $favoriteData,
            "message" => true
        ]);
    }

    /**
     * Favoriti sil
     */
    public function removeFavorite(Request $request)
    {
        $user = $request->user();

        $deleted = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Favorite removed successfully!' : 'Favorite not found!'
        ]);
    }
}
