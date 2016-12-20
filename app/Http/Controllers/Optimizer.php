<?php

namespace App\Http\Controllers;

use Approached\LaravelImageOptimizer\ImageOptimizer;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Optimizer extends Controller
{
    public function down(Request $request, ImageOptimizer $imageOptimizer)
    {
        $pic = $request->file('picture');
        //first resize to max limit
        $imageOptimizer->optimizeUploadedImageFile($pic);
        $newName = str_random(20) . '.' . $pic->getClientOriginalExtension();
        Storage::put('compressed/' . $newName, File::get($pic));
        $img = Image::make(storage_path('app/compressed/') . $newName);
        $img->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save(storage_path('app/resized/') . $newName);
    }
}
