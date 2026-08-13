<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationTemplate extends Model { protected $fillable=['key','name','title','content','channels','status']; protected $casts=['channels'=>'array','status'=>'boolean']; }
