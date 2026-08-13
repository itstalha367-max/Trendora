<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Collection;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\TaxRate;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\StorefrontCache;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class TrendoraDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && !filter_var(env('TRENDORA_ALLOW_DEMO_SEED', false), FILTER_VALIDATE_BOOL)) {
            throw new RuntimeException('Demo seeding is blocked in production. Set TRENDORA_ALLOW_DEMO_SEED=true only if you intentionally want demo data.');
        }

        DB::transaction(function () {
            $admin = User::updateOrCreate(['email'=>'admin@trendora.demo'], [
                'name'=>'Trendora Admin','role'=>'admin','email_verified_at'=>now(),'password'=>Hash::make('Trendora@123'),
                'phone'=>'+92 300 1112233','city'=>'Lahore','country'=>'Pakistan',
            ]);
            $customer = User::updateOrCreate(['email'=>'customer@trendora.demo'], [
                'name'=>'Areeba Khan','role'=>'user','email_verified_at'=>now(),'password'=>Hash::make('Trendora@123'),
                'store_credit'=>2500,'phone'=>'+92 300 7654321','address'=>'Gulberg III','city'=>'Lahore','state'=>'Punjab','zip'=>'54660','country'=>'Pakistan',
            ]);

            $categoryNames = ['Smartphones','Laptops','Fashion','Home & Living','Accessories'];
            $categories=[];
            foreach ($categoryNames as $name) {
                $categories[$name]=Category::updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'description'=>'Demo '.$name.' collection for Trendora.','status'=>true]);
            }

            $brandNames=['NovaTech','Aster','Lumina','Northline','Mono'];
            $brands=[];
            foreach($brandNames as $i=>$name){$brands[$name]=Brand::updateOrCreate(['slug'=>Str::slug($name)],['name'=>$name,'description'=>'Trendora demo brand','featured'=>$i<3,'status'=>true,'sort_order'=>$i]);}

            $catalog = [
                ['Nova X Pro','Smartphones','NovaTech',189999,209999,22,true],
                ['Lumina Air 5G','Smartphones','Lumina',129999,145000,35,true],
                ['AsterBook Studio 14','Laptops','Aster',284999,309999,12,true],
                ['Northline WorkBook 15','Laptops','Northline',214999,null,9,false],
                ['Mono Arc Headphones','Accessories','Mono',18999,22999,42,true],
                ['Nova Charge Pro','Accessories','NovaTech',8999,null,70,false],
                ['Aster Minimal Watch','Accessories','Aster',31999,35999,28,true],
                ['Lumina Desk Lamp','Home & Living','Lumina',12499,14999,18,false],
                ['Northline Lounge Throw','Home & Living','Northline',7499,null,26,false],
                ['Mono Everyday Tote','Fashion','Mono',9999,11999,31,true],
                ['Aster Urban Jacket','Fashion','Aster',24999,28999,15,true],
                ['Northline Classic Sneakers','Fashion','Northline',17999,null,24,false],
            ];
            $products=[];
            foreach($catalog as $i=>[$name,$category,$brand,$price,$compare,$stock,$featured]){
                $sku='DEMO-'.str_pad((string)($i+1),3,'0',STR_PAD_LEFT);
                $products[]=Product::updateOrCreate(['sku'=>$sku],[
                    'category_id'=>$categories[$category]->id,'brand_id'=>$brands[$brand]->id,'name'=>$name,'slug'=>Str::slug($name),
                    'description'=>'A polished Trendora demo product created for UI, checkout, inventory and reporting tests.',
                    'price'=>$price,'compare_price'=>$compare,'stock_quantity'=>$stock,'images'=>[],'thumbnail'=>null,'featured'=>$featured,'status'=>true,'views'=>150+($i*37),
                ]);
            }

            $collection=Collection::updateOrCreate(['slug'=>'editor-picks'],['name'=>'Editor Picks','description'=>'A curated demo collection.','featured'=>true,'status'=>true]);
            $collection->products()->sync(collect($products)->take(6)->pluck('id')->mapWithKeys(fn($id,$i)=>[$id=>['sort_order'=>$i]])->all());

            $warehouse=Warehouse::updateOrCreate(['code'=>'LHR-MAIN'],['name'=>'Lahore Main Warehouse','email'=>'warehouse@trendora.test','city'=>'Lahore','state'=>'Punjab','country'=>'Pakistan','postal_code'=>'54660','is_default'=>true,'status'=>true]);
            foreach($products as $i=>$product){Inventory::updateOrCreate(['warehouse_id'=>$warehouse->id,'product_id'=>$product->id,'product_variation_id'=>null],[ 'sku'=>$product->sku,'quantity'=>$product->stock_quantity,'reserved_quantity'=>0,'reorder_level'=>5,'bin_location'=>'A-'.str_pad((string)($i+1),2,'0',STR_PAD_LEFT)]);}

            $zone=ShippingZone::updateOrCreate(['name'=>'Pakistan'],['countries'=>['PK'],'states'=>null,'status'=>true]);
            $standard=ShippingMethod::updateOrCreate(['shipping_zone_id'=>$zone->id,'name'=>'Standard Delivery'],['type'=>'flat_rate','cost'=>250,'free_over'=>10000,'min_days'=>3,'max_days'=>5,'status'=>true]);
            ShippingMethod::updateOrCreate(['shipping_zone_id'=>$zone->id,'name'=>'Express Delivery'],['type'=>'flat_rate','cost'=>550,'free_over'=>30000,'min_days'=>1,'max_days'=>2,'status'=>true]);
            TaxRate::updateOrCreate(['name'=>'Demo Sales Tax'],['country'=>'PK','state'=>null,'rate'=>5,'compound'=>false,'shipping_taxable'=>false,'priority'=>1,'status'=>true]);
            Coupon::updateOrCreate(['code'=>'WELCOME10'],['name'=>'Welcome 10%','type'=>'percentage','value'=>10,'min_order'=>3000,'max_discount'=>5000,'usage_limit'=>500,'used_count'=>0,'per_user_limit'=>1,'start_date'=>today()->subDay(),'end_date'=>today()->addMonths(3),'status'=>true]);
            Promotion::updateOrCreate(['code'=>'PRO15'],['name'=>'Pro Launch 15','type'=>'percentage','value'=>15,'minimum_order'=>15000,'maximum_discount'=>7500,'usage_limit'=>1000,'usage_count'=>0,'rules'=>[],'starts_at'=>now()->subDay(),'ends_at'=>now()->addMonth(),'status'=>true]);
            CmsPage::updateOrCreate(['slug'=>'our-promise'],['title'=>'Our Promise','eyebrow'=>'Trendora Standard','excerpt'=>'Fast support, transparent operations and a polished shopping experience.','content'=>'<h2>Built for confidence</h2><p>This demo CMS page proves that content can be managed without editing Blade templates.</p>','meta_title'=>'Our Promise — Trendora','meta_description'=>'Trendora customer promise.','status'=>true,'sort_order'=>10]);

            foreach ([
                ['DEMO-ORD-001','delivered','paid',0],
                ['DEMO-ORD-002','shipped','paid',1],
                ['DEMO-ORD-003','processing','pending',2],
            ] as $index=>[$number,$status,$payment,$productOffset]) {
                $product=$products[$productOffset]; $subtotal=(float)$product->price; $tax=round($subtotal*.05,2); $shipping=$subtotal>=10000?0:250; $total=$subtotal+$tax+$shipping;
                $order=Order::updateOrCreate(['order_number'=>$number],[
                    'user_id'=>$customer->id,'subtotal'=>$subtotal,'tax'=>$tax,'tax_name'=>'Demo Sales Tax','tax_rate'=>5,'shipping_cost'=>$shipping,'shipping_method_id'=>$standard->id,'shipping_method_name'=>$standard->name,'discount'=>0,'refunded_amount'=>0,'total'=>$total,
                    'shipping_name'=>$customer->name,'shipping_email'=>$customer->email,'shipping_phone'=>$customer->phone,'shipping_address'=>$customer->address,'shipping_city'=>$customer->city,'shipping_state'=>$customer->state,'shipping_zip'=>$customer->zip,'shipping_country'=>'Pakistan',
                    'payment_status'=>$payment,'order_status'=>$status,'payment_gateway'=>$payment==='paid'?'demo':'cod','transaction_id'=>$payment==='paid'?'DEMO-TX-00'.($index+1):null,'tracking_number'=>in_array($status,['shipped','delivered'])?'TRK-DEMO-00'.($index+1):null,
                    'shipped_at'=>in_array($status,['shipped','delivered'])?now()->subDays(2-$index):null,'delivered_at'=>$status==='delivered'?now()->subDay():null,
                ]);
                $order->forceFill(['created_at'=>now()->subDays(10-($index*3)),'updated_at'=>now()])->saveQuietly();
                OrderItem::updateOrCreate(['order_id'=>$order->id,'product_id'=>$product->id],[ 'product_variation_id'=>null,'warehouse_id'=>$warehouse->id,'product_name'=>$product->name,'product_sku'=>$product->sku,'quantity'=>1,'price'=>$subtotal,'total'=>$subtotal]);
                if($payment==='paid') PaymentTransaction::updateOrCreate(['transaction_id'=>'DEMO-TX-00'.($index+1)],[ 'order_id'=>$order->id,'gateway'=>'demo','type'=>'charge','status'=>'succeeded','amount'=>$total,'currency'=>'PKR','payload'=>['demo'=>true],'note'=>'Demo payment transaction']);
            }

            Review::updateOrCreate(['user_id'=>$customer->id,'product_id'=>$products[0]->id],[ 'order_id'=>Order::where('order_number','DEMO-ORD-001')->value('id'),'rating'=>5,'comment'=>'Excellent demo experience — the product page, checkout and tracking flow all look polished.','images'=>[],'status'=>'approved','verified_purchase'=>true]);
        });

        StorefrontCache::clear();
        $this->command?->info('Trendora demo data ready. Admin: admin@trendora.demo / Trendora@123 · Customer: customer@trendora.demo / Trendora@123');
    }
}
