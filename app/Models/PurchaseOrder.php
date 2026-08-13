<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseOrder extends Model { protected $fillable=['po_number','supplier_id','warehouse_id','status','subtotal','total','ordered_at','expected_at','received_at','notes','created_by']; protected $casts=['subtotal'=>'decimal:2','total'=>'decimal:2','ordered_at'=>'date','expected_at'=>'date','received_at'=>'date']; public function supplier(){return $this->belongsTo(Supplier::class);} public function warehouse(){return $this->belongsTo(Warehouse::class);} public function items(){return $this->hasMany(PurchaseOrderItem::class);} public function creator(){return $this->belongsTo(User::class,'created_by');} public static function generateNumber(){return 'PO-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(),-5));} }
