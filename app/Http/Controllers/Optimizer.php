<?php

namespace App\Http\Controllers;

use Approached\LaravelImageOptimizer\ImageOptimizer;
use File;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Optimizer extends Controller
{

    const EXTENSION_DOT_LENGTH = -4;

    public function resizepost(Request $request, ImageOptimizer $imageOptimizer)
    {
        $pic = $request->file('picture');
        $width = ($request->get('width')) ? $request->get('width') : 640;
        $height = ($request->get('height')) ? $request->get('height') : null;
        $imageOptimizer->optimizeUploadedImageFile($pic);
        $newName = substr_replace($pic->getClientOriginalName(),
            $this->getName($width, $height) . '.' . $pic->getClientOriginalExtension(), self::EXTENSION_DOT_LENGTH);
        Storage::put('compressed/' . $newName, File::get($pic));
        $img = Image::make(storage_path('app/compressed/') . $newName);
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(storage_path('app/resized/') . $newName);
        unlink(storage_path('app/compressed/') . $newName);
        return $img->response();
    }

    public function resizeremote(Request $request, ImageOptimizer $imageOptimizer)
    {
        $pic = $request->get('image');
        $width = ($request->get('width')) ? $request->get('width') : 640;
        $height = ($request->get('height')) ? $request->get('height') : null;
        $file = storage_path('app/remote/') . basename($pic);
        $client = new Client();
        $client->request('GET', $pic, [
            'sink' => storage_path('app/remote/') . basename($pic),
        ]);
        $imageOptimizer->optimizeImage($file);
        $img = Image::make($file);
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        return $img->response();
    }

    private function getName($w, $h)
    {
        $postfix = '_';
        if ($w) {
            $postfix .= 'w' . $w;
        }
        if ($h) {
            $postfix .= 'h' . $h;
        }
        return $postfix;
    }
}
