<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Images insérées dans le contenu d'un article depuis l'éditeur (avant même que l'article existe)
class UploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120']]);

        $path = $request->file('image')->store('editor-images', 'public');

        return response()->json(['url' => Storage::disk('public')->url($path)], 201);
    }
}
