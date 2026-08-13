<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Permission extends Model { use HasFactory; protected $fillable=['name','key','group']; public function roles(){return $this->belongsToMany(AdminRole::class,'admin_role_permission');} }
