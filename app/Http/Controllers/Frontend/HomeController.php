<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    { $categories=Category::all();
        $posts = Post::with('categoryName')->where('status','Active')->paginate('9');
        $mostRecommended=Post::with('categoryName')->where('status','Active')->orderBy('created_at','DESC')->where('rentAmount','>',20000)->paginate(9);
        $bannerPosts=Post::where('status','Active')->where('rentAmount','>',30000)->orderBy('created_at','DESC')->paginate('5');
        return view('frontend.layouts.home', compact('posts','categories','mostRecommended','bannerPosts'));
//
    }


}