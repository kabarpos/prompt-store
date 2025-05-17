<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    public function optimize($image, $width = null, $height = null, $quality = 80)
    {
        $img = Image::make($image);
        
        if ($width || $height) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        return $img->encode('jpg', $quality);
    }

    public function storeOptimized($image, $path, $width = null, $height = null, $quality = 80)
    {
        $optimized = $this->optimize($image, $width, $height, $quality);
        return Storage::put($path, $optimized);
    }

    public function generatePlaceholder($image, $width = 10, $height = 10)
    {
        $img = Image::make($image);
        $img->resize($width, $height);
        return $img->encode('jpg', 10)->encode('data-url');
    }
} 