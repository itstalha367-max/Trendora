<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class CmsPage extends Model {
    use HasFactory;
    protected $fillable=['title','slug','eyebrow','excerpt','content','meta_title','meta_description','status','sort_order'];
    protected $casts=['status'=>'boolean'];
    protected static function booted(){static::saving(function($page){if(!$page->slug)$page->slug=Str::slug($page->title);});}
}
