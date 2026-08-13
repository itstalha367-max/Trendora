<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NavigationMenu extends Model { protected $fillable=['name','location','status']; protected $casts=['status'=>'boolean']; public function items(){return $this->hasMany(NavigationItem::class)->whereNull('parent_id')->orderBy('sort_order');} public function allItems(){return $this->hasMany(NavigationItem::class)->orderBy('sort_order');} }
