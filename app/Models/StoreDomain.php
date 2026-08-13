<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StoreDomain extends Model { protected $fillable=['domain','primary','ssl_status','verification_status']; protected $casts=['primary'=>'boolean']; }
