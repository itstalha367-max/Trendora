<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AdminRole extends Model { use HasFactory; protected $fillable=['name','slug','description','is_system']; protected $casts=['is_system'=>'boolean']; public function permissions(){return $this->belongsToMany(Permission::class,'admin_role_permission');} public function users(){return $this->hasMany(User::class);} }
