<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TaxRate extends Model { use HasFactory; protected $fillable=['name','country','state','rate','compound','shipping_taxable','priority','status']; protected $casts=['rate'=>'decimal:4','compound'=>'boolean','shipping_taxable'=>'boolean','status'=>'boolean']; }
