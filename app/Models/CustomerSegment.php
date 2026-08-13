<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class CustomerSegment extends Model { protected $fillable=['name','slug','description','rules','status']; protected $casts=['rules'=>'array','status'=>'boolean']; protected static function booted(){static::saving(function($m){$m->slug=$m->slug?:Str::slug($m->name);});} public function users(){return $this->belongsToMany(User::class,'customer_segment_user')->withTimestamps();} }
