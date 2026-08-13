<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;use App\Models\{Product,ProductQuestion};use Illuminate\Http\Request;
class ProductQuestionController extends Controller{public function store(Request $r,Product $product){$d=$r->validate(['name'=>'nullable|string|max:120','email'=>'nullable|email|max:255','question'=>'required|string|min:5|max:2000']);ProductQuestion::create(['product_id'=>$product->id,'user_id'=>auth()->id(),'name'=>auth()->user()?->name??$d['name']??null,'email'=>auth()->user()?->email??$d['email']??null,'question'=>$d['question'],'status'=>'pending']);return back()->with('success','Your question was submitted for review.');}}
