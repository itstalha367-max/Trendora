<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Campaign extends Model { use HasFactory; protected $fillable=['name','type','status','budget','spent','impressions','clicks','conversions','starts_at','ends_at','notes']; protected $casts=['budget'=>'decimal:2','spent'=>'decimal:2','starts_at'=>'datetime','ends_at'=>'datetime']; public function getCtrAttribute(){return $this->impressions?round(($this->clicks/$this->impressions)*100,2):0;} public function getConversionRateAttribute(){return $this->clicks?round(($this->conversions/$this->clicks)*100,2):0;} }
