<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NavigationItem extends Model { protected $fillable=['navigation_menu_id','parent_id','label','url','target','sort_order','status']; protected $casts=['status'=>'boolean']; public function menu(){return $this->belongsTo(NavigationMenu::class,'navigation_menu_id');} public function parent(){return $this->belongsTo(self::class,'parent_id');} public function children(){return $this->hasMany(self::class,'parent_id')->orderBy('sort_order');} }
