<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    public function index()
    {
        return Image::latest()->get()->map(function ($image) {
            return [
                'id' => $image->id,
                'label' => $image->label,
                'path' => url(Storage::url($image->path))
            ];
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg'],
            'label' => ['nullable', 'string', 'max:255']
        ]);

        $path = $request->file('image')->store('images', 'public');
        $image = Image::create([
            'path' => $path,
            'label' => $request->label
        ]);

        return response( $image, 201 );
    }

    public function destroy(Image $image)
    {
        $image->delete();
        return response(null, 204);
    }
}
