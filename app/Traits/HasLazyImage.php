<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasLazyImage
{
    public function getLazyImageAttribute($value, $field = 'featured_image')
    {
        if (!$value) {
            return null;
        }

        return [
            'src' => Storage::url($value),
            'placeholder' => $this->generatePlaceholder($value),
            'alt' => $this->getImageAltText($field)
        ];
    }

    protected function generatePlaceholder($imagePath)
    {
        // Generate base64 encoded placeholder
        $path = storage_path('app/public/' . $imagePath);
        if (file_exists($path)) {
            $image = imagecreatefromstring(file_get_contents($path));
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Create a 10x10 pixel version
            $thumb = imagecreatetruecolor(10, 10);
            imagecopyresized($thumb, $image, 0, 0, 0, 0, 10, 10, $width, $height);
            
            ob_start();
            imagejpeg($thumb, null, 10);
            $contents = ob_get_contents();
            ob_end_clean();
            
            return 'data:image/jpeg;base64,' . base64_encode($contents);
        }
        
        return null;
    }

    protected function getImageAltText($field)
    {
        return $this->name ?? $this->title ?? 'Image';
    }
} 