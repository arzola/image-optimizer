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
        try {
            $pic = $request->file('picture');
            $width = ($request->get('width')) ? $request->get('width') : 640;
            $height = ($request->get('height')) ? $request->get('height') : null;
            $imageOptimizer->optimizeUploadedImageFile($pic);
            $newName = substr_replace($pic->getClientOriginalName(),
                $this->getName($width, $height) . '.' . $pic->getClientOriginalExtension(), self::EXTENSION_DOT_LENGTH);
            Storage::put('compressed/' . $newName, File::get($pic));
            $img = Image::make(storage_path('app/compressed/') . $newName);
            $resize = $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if ($resize) {
                $img->save(storage_path('app/resized/') . $newName);
                unlink(storage_path('app/compressed/') . $newName);
                return $img->response();
            }
        } catch (\Exception $e) {
            return response()->json(['bad_input' => 'The input image is corrupted'], 204);
        }
        return response()->json(['bad_input' => 'The input image is corrupted'], 204);
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
        $resize = $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        if ($resize) {
            return $img->response();
        }
        return response()->json(['bad_input' => 'The input image is corrupted']);
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
