<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\CmsPage;
class CmsPageController extends Controller { public function show(string $slug){$page=CmsPage::where('slug',$slug)->where('status',true)->firstOrFail();return view('frontend.pages.cms',compact('page'));} }
