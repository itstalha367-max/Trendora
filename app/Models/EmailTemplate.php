<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmailTemplate extends Model { protected $fillable=['key','name','subject','content','variables','status']; protected $casts=['variables'=>'array','status'=>'boolean']; }
