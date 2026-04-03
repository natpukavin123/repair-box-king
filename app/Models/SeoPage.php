<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'featured_image',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image', 'robots', 'schema_type',
        'status', 'sort_order',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'archived');
    }

    public static function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;
        while (self::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $original . '-' . $counter++;
        }
        return $slug;
    }

    public function getEffectiveMetaTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function getEffectiveMetaDescription(): string
    {
        return $this->meta_description ?: Str::limit(strip_tags($this->content), 160);
    }

    public function faqs()
    {
        return Faq::active()->forPage($this->slug)->orderBy('sort_order')->get();
    }
}
