<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page', 'meta_title', 'meta_description', 'meta_keywords',
        'og_title', 'og_description', 'og_image', 'canonical_url',
        'robots', 'schema_markup'
    ];

    protected $casts = [
        'schema_markup' => 'array',
    ];

    // Get SEO settings for a page
    public static function getForPage($page)
    {
        return self::where('page', $page)->first();
    }

    // Get meta tags array
    public function getMetaTags()
    {
        return [
            'title' => $this->meta_title,
            'description' => $this->meta_description,
            'keywords' => $this->meta_keywords,
            'robots' => $this->robots,
        ];
    }

    // Get Open Graph tags array
    public function getOpenGraphTags()
    {
        return [
            'title' => $this->og_title ?? $this->meta_title,
            'description' => $this->og_description ?? $this->meta_description,
            'image' => $this->og_image ? asset('storage/' . $this->og_image) : null,
        ];
    }
}