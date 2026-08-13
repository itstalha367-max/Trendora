<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Integration extends Model { protected $fillable=['name','slug','category','enabled','encrypted_config','health_status','last_synced_at']; protected $casts=['enabled'=>'boolean','last_synced_at'=>'datetime']; public function getConfigAttribute(){try{return $this->encrypted_config?json_decode(decrypt($this->encrypted_config),true)??[]:[];}catch(\Throwable $e){return [];} } public function setConfigAttribute($value){$this->attributes['encrypted_config']=encrypt(json_encode($value??[]));} }
