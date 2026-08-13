<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\TaxRate;use Illuminate\Http\Request;
class TaxRateController extends Controller {
 public function index(){ $taxes=TaxRate::orderBy('priority')->paginate(20);return view('admin.taxes.index',compact('taxes'));}
 public function store(Request $r){$d=$this->valid($r);$d['compound']=$r->boolean('compound');$d['shipping_taxable']=$r->boolean('shipping_taxable');$d['status']=$r->boolean('status');TaxRate::create($d);return back()->with('success','Tax rate created.');}
 public function update(Request $r,TaxRate $tax){$d=$this->valid($r);$d['compound']=$r->boolean('compound');$d['shipping_taxable']=$r->boolean('shipping_taxable');$d['status']=$r->boolean('status');$tax->update($d);return back()->with('success','Tax rate updated.');}
 public function destroy(TaxRate $tax){$tax->delete();return back()->with('success','Tax rate deleted.');}
 private function valid(Request $r){return $r->validate(['name'=>'required|string|max:120','country'=>'nullable|string|size:2','state'=>'nullable|string|max:100','rate'=>'required|numeric|min:0|max:100','priority'=>'required|integer|min:1']);}
}
