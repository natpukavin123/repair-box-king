<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Get the configured image storage disk name.
     */
    public function disk(): string
    {
        return config('app.image_disk', 'public');
    }

    /**
     * Get the public URL for a stored image path.
     */
    public function url(?string $path): ?string
    {
        if (!$path) return null;

        // Already a full URL (e.g. migrated data or S3 absolute URL)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // CDN override (CloudFront, BunnyCDN, etc.)
        $cdn = config('app.image_cdn_url');
        if ($cdn) {
            return rtrim($cdn, '/') . '/' . ltrim($path, '/');
        }

        return Storage::disk($this->disk())->url($path);
    }

    /**
     * Get the base URL prefix for constructing image URLs in JS.
     */
    public function baseUrl(): string
    {
        $cdn = config('app.image_cdn_url');
        if ($cdn) return rtrim($cdn, '/');

        $disk = $this->disk();
        if ($disk === 'public') {
            return rtrim(config('app.url'), '/') . '/storage';
        }

        // For S3 and other cloud disks, derive from a blank url() call
        return rtrim(Storage::disk($disk)->url(''), '/');
    }

    /**
     * Store an uploaded file to the configured disk.
     */
    public function store(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, $this->disk());
    }

    /**
     * Delete a file from the configured disk.
     */
    public function delete(?string $path): void
    {
        if ($path && !str_starts_with($path, 'http')) {
            Storage::disk($this->disk())->delete($path);
        }
    }

    /**
     * Generate a 200×200 max thumbnail (JPEG, quality 85).
     * Works with both local and cloud disks.
     */
    public function makeThumb(string $storedPath, string $thumbFolder): ?string
    {
        if (!function_exists('imagecreatefromjpeg')) return null;

        $disk    = $this->disk();
        $isLocal = ($disk === 'public');
        $thumbFilename = pathinfo($storedPath, PATHINFO_FILENAME) . '_thumb.jpg';
        $tempFile = null;

        // Get a local file path for GD processing
        if ($isLocal) {
            $localPath = Storage::disk($disk)->path($storedPath);
        } else {
            $tempFile  = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tempFile, Storage::disk($disk)->get($storedPath));
            $localPath = $tempFile;
        }

        try {
            $ext = strtolower(pathinfo($storedPath, PATHINFO_EXTENSION));
            $image = match ($ext) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($localPath),
                'png'         => @imagecreatefrompng($localPath),
                'gif'         => @imagecreatefromgif($localPath),
                'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($localPath) : null,
                default       => null,
            };

            if (!$image) return null;

            [$sw, $sh] = getimagesize($localPath);
            $ratio = min(200 / $sw, 200 / $sh);
            $nw = max(1, (int)($sw * $ratio));
            $nh = max(1, (int)($sh * $ratio));
            $thumb = imagecreatetruecolor($nw, $nh);
            imagecopyresampled($thumb, $image, 0, 0, 0, 0, $nw, $nh, $sw, $sh);

            if ($isLocal) {
                $dir = Storage::disk($disk)->path($thumbFolder);
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                imagejpeg($thumb, $dir . DIRECTORY_SEPARATOR . $thumbFilename, 85);
            } else {
                // Cloud disk: render to temp then upload
                $tempThumb = tempnam(sys_get_temp_dir(), 'thumb_');
                imagejpeg($thumb, $tempThumb, 85);
                Storage::disk($disk)->put(
                    $thumbFolder . '/' . $thumbFilename,
                    file_get_contents($tempThumb),
                    'public'
                );
                unlink($tempThumb);
            }

            imagedestroy($image);
            imagedestroy($thumb);

            return $thumbFolder . '/' . $thumbFilename;
        } finally {
            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Full upload handler: validate, store image + thumbnail, update model.
     * Replaces duplicated uploadImage() logic across all controllers.
     *
     * @param  Request  $request   The HTTP request with 'image' and/or 'thumbnail' files
     * @param  Model    $model     The Eloquent model to update (must have image & thumbnail columns)
     * @param  string   $folder    Storage folder name (e.g. 'products', 'brands', 'vendors')
     * @return array               JSON-ready response array
     */
    public function handleUpload(Request $request, Model $model, string $folder): array
    {
        $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $updates = [];

        if ($request->hasFile('image')) {
            $this->delete($model->image);
            if (!$request->hasFile('thumbnail')) {
                $this->delete($model->thumbnail);
            }

            $path = $this->store($request->file('image'), $folder);
            $updates['image'] = $path;

            // Auto-generate thumbnail if none provided
            if (!$request->hasFile('thumbnail')) {
                $thumbPath = $this->makeThumb($path, $folder . '/thumbs');
                if ($thumbPath) $updates['thumbnail'] = $thumbPath;
            }
        }

        if ($request->hasFile('thumbnail')) {
            $this->delete($model->thumbnail);
            $path = $this->store($request->file('thumbnail'), $folder . '/thumbs');
            $updates['thumbnail'] = $path;
        }

        if ($updates) $model->update($updates);

        $fresh = $model->fresh();
        return [
            'success'   => true,
            'image_url' => $this->url($fresh->image),
            'thumb_url' => $this->url($fresh->thumbnail),
        ];
    }
}
