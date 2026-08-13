<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class CommerceSettingsController extends Controller
{
    public function storeDetails(){ return view('admin.settings.store-details', ['values'=>$this->values(['store_name','store_legal_name','store_email','store_phone','store_address','store_country','store_timezone','currency','currency_symbol','order_prefix','invoice_prefix','social_instagram','social_facebook','social_x','social_youtube'])]); }
    public function checkout(){ return view('admin.settings.checkout', ['values'=>$this->values(['minimum_order_amount','checkout_terms_required','checkout_notes_enabled','checkout_default_country','checkout_cod_enabled','checkout_wallet_enabled'])]); }
    public function shipping(){ return view('admin.settings.shipping', ['values'=>$this->values(['default_shipping_name','default_shipping_cost','default_shipping_min_days','default_shipping_max_days','shipping_fallback_enabled'])]); }
    public function tax(){ return view('admin.settings.tax', ['values'=>$this->values(['tax_prices_include_tax','tax_display_cart'])]); }

    public function updateStore(Request $request){
        $data=$request->validate(['store_name'=>'required|string|max:255','store_legal_name'=>'nullable|string|max:255','store_email'=>'required|email|max:255','store_phone'=>'nullable|string|max:40','store_address'=>'nullable|string|max:2000','store_country'=>'nullable|string|max:100','store_timezone'=>'required|string|max:100','currency'=>'required|string|max:10','currency_symbol'=>'required|string|max:8','order_prefix'=>'required|string|max:12','invoice_prefix'=>'required|string|max:12','social_instagram'=>'nullable|url:https|max:500','social_facebook'=>'nullable|url:https|max:500','social_x'=>'nullable|url:https|max:500','social_youtube'=>'nullable|url:https|max:500']);
        return $this->save($data,'Store details updated.');
    }
    public function updateCheckout(Request $request){
        $data=$request->validate(['minimum_order_amount'=>'required|numeric|min:0','checkout_default_country'=>'required|string|max:100']);
        $data += ['checkout_terms_required'=>$request->boolean('checkout_terms_required')?'on':'off','checkout_notes_enabled'=>$request->boolean('checkout_notes_enabled')?'on':'off','checkout_cod_enabled'=>$request->boolean('checkout_cod_enabled')?'on':'off','checkout_wallet_enabled'=>$request->boolean('checkout_wallet_enabled')?'on':'off'];
        return $this->save($data,'Checkout settings updated.');
    }
    public function updateShipping(Request $request){
        $data=$request->validate(['default_shipping_name'=>'required|string|max:120','default_shipping_cost'=>'required|numeric|min:0','default_shipping_min_days'=>'required|integer|min:0|max:90','default_shipping_max_days'=>'required|integer|min:0|max:120']);
        if((int)$data['default_shipping_max_days'] < (int)$data['default_shipping_min_days']) return back()->withInput()->with('error','Maximum delivery days must be greater than or equal to minimum days.');
        $data['shipping_fallback_enabled']=$request->boolean('shipping_fallback_enabled')?'on':'off';
        return $this->save($data,'Shipping defaults updated.');
    }
    public function updateTax(Request $request){
        return $this->save(['tax_prices_include_tax'=>$request->boolean('tax_prices_include_tax')?'on':'off','tax_display_cart'=>$request->boolean('tax_display_cart')?'on':'off'],'Tax display settings updated.');
    }

    private function values(array $keys): array { $out=[]; foreach($keys as $key)$out[$key]=Setting::get($key); return $out; }
    private function save(array $data,string $message){ foreach($data as $key=>$value) Setting::set($key,$value); return back()->with('success',$message); }
}
