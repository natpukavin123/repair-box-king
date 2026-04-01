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
     * Handles both full URLs (new R2 uploads) and relative paths (legacy data).
     */
    public function url(?string $path): ?string
    {
        if (!$path) return null;

        // Already a full URL (R2, S3, or migrated data)
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
     * Extract relative storage path from a full URL for disk operations.
     */
    public function relativePath(string $url): string
    {
        // S3/R2: strip the AWS_URL prefix
        $awsUrl = config('filesystems.disks.s3.url');
        if ($awsUrl && str_starts_with($url, rtrim($awsUrl, '/'))) {
            return ltrim(substr($url, strlen(rtrim($awsUrl, '/'))), '/');
        }

        // CDN URL
        $cdn = config('app.image_cdn_url');
        if ($cdn && str_starts_with($url, rtrim($cdn, '/'))) {
            return ltrim(substr($url, strlen(rtrim($cdn, '/'))), '/');
        }

        // Local: strip APP_URL/storage prefix
        $localPrefix = rtrim(config('app.url'), '/') . '/storage';
        if (str_starts_with($url, $localPrefix)) {
            return ltrim(substr($url, strlen($localPrefix)), '/');
        }

        return $url;
    }

    /**
     * Store an uploaded file and return its full public URL.
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $path = $file->store($folder, $this->disk());
        return Storage::disk($this->disk())->url($path);
    }

    /**
     * Delete a file from the configured disk. Handles both full URLs and relative paths.
     */
    public function delete(?string $path): void
    {
        if (!$path) return;

        // Convert full URL to relative path for disk deletion
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $path = $this->relativePath($path);
        }

        Storage::disk($this->disk())->delete($path);
    }

    /**
     * Generate a 200×200 max thumbnail (JPEG, quality 85).
     * Works with both local and cloud disks.
     * Accepts and returns full URLs.
     */
    public function makeThumb(string $storedPath, string $thumbFolder): ?string
    {
        if (!function_exists('imagecreatefromjpeg')) return null;

        $disk    = $this->disk();
        $isLocal = ($disk === 'public');

        // Convert full URL to relative path for disk operations
        $relativePath = $storedPath;
        if (str_starts_with($storedPath, 'http://') || str_starts_with($storedPath, 'https://')) {
            $relativePath = $this->relativePath($storedPath);
        }

        $thumbFilename = pathinfo($relativePath, PATHINFO_FILENAME) . '_thumb.jpg';
        $tempFile = null;

        // Get a local file path for GD processing
        if ($isLocal) {
            $localPath = Storage::disk($disk)->path($relativePath);
        } else {
            $tempFile  = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tempFile, Storage::disk($disk)->get($relativePath));
            $localPath = $tempFile;
        }

        try {
            $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
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

            $thumbRelativePath = $thumbFolder . '/' . $thumbFilename;

            if ($isLocal) {
                $dir = Storage::disk($disk)->path($thumbFolder);
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                imagejpeg($thumb, $dir . DIRECTORY_SEPARATOR . $thumbFilename, 85);
            } else {
                // Cloud disk: render to temp then upload
                $tempThumb = tempnam(sys_get_temp_dir(), 'thumb_');
                imagejpeg($thumb, $tempThumb, 85);
                Storage::disk($disk)->put(
                    $thumbRelativePath,
                    file_get_contents($tempThumb),
                    'public'
                );
                unlink($tempThumb);
            }

            imagedestroy($image);
            imagedestroy($thumb);

            // Return full URL
            return Storage::disk($disk)->url($thumbRelativePath);
        } finally {
            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Full upload handler: validate, store image + thumbnail, update model.
     * Stores full URLs in the database.
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

            $url = $this->store($request->file('image'), $folder);
            $updates['image'] = $url;

            // Auto-generate thumbnail if none provided
            if (!$request->hasFile('thumbnail')) {
                $thumbUrl = $this->makeThumb($url, $folder . '/thumbs');
                if ($thumbUrl) $updates['thumbnail'] = $thumbUrl;
            }
        }

        if ($request->hasFile('thumbnail')) {
            $this->delete($model->thumbnail);
            $url = $this->store($request->file('thumbnail'), $folder . '/thumbs');
            $updates['thumbnail'] = $url;
        }

        if ($updates) $model->update($updates);

        $fresh = $model->fresh();
        return [
            'success'   => true,
            'image_url' => $fresh->image,
            'thumb_url' => $fresh->thumbnail,
        ];
    }
}
