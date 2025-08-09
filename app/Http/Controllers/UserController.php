<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Category;
use App\About;
use App\Article;
use App\Destination;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function home()
    {
        $data = [
            'categories' => Category::all(),
            'about' => About::all()
        ];
        return view('user/home', $data);
    }

    public function blog(Request $request)
    {
        $keyword = $request->get('s') ? $request->get('s') : '';
        $category = $request->get('c') ? $request->get('c') : '';
        
        $query = Article::with('categories')->where('status', 'PUBLISH');
        
        if ($category) {
            $query->whereHas('categories', function($q) use($category) {
                $q->where('name', 'LIKE', "%$category%");
            });
        }
        
        if ($keyword) {
            $query->where('title', 'LIKE', "%$keyword%");
        }
        
        $articles = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $recents = Article::select('title', 'slug')
                         ->where('status', 'PUBLISH')
                         ->orderBy('created_at', 'desc')
                         ->limit(5)
                         ->get();
        
        $data = [
            'articles' => $articles,
            'recents' => $recents
        ];
        
        return view('user/blog', $data);
    }

    public function show_article($slug)
    {
        $articles = Article::where('slug', $slug)
                          ->where('status', 'PUBLISH') // ← Tambahkan filter ini juga
                          ->firstOrFail();
        
        $recents = Article::select('title', 'slug')
                         ->where('status', 'PUBLISH')
                         ->orderBy('created_at', 'desc')
                         ->limit(5)
                         ->get();
        
        $data = [
            'articles' => $articles,
            'recents' => $recents
        ];
        
        return view('user/blog', $data);
    }

    public function destination(Request $request)
    {
        $keyword = $request->get('s') ? $request->get('s') : '';
        
        // ✅ PERBAIKAN: Tambahkan filter status PUBLISH
        $destinations = Destination::where('status', 'PUBLISH') // ← INI YANG KURANG!
                                  ->where('title', 'LIKE', "%$keyword%")
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);
        
        $other_destinations = Destination::select('title', 'slug')
                                       ->where('status', 'PUBLISH')
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)
                                       ->get();
        
        $data = [
            'destinations' => $destinations,
            'other' => $other_destinations
        ];
        
        return view('user/destination', $data);
    }

    public function show_destination($slug)
    {
        // ✅ PERBAIKAN: Tambahkan filter status PUBLISH
        $destinations = Destination::where('slug', $slug)
                                  ->where('status', 'PUBLISH') // ← Tambahkan ini juga
                                  ->firstOrFail();
        
        $other_destinations = Destination::select('title', 'slug')
                                       ->where('status', 'PUBLISH')
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)
                                       ->get();
        
        $data = [
            'destinations' => $destinations,
            'other' => $other_destinations
        ];
        
        return view('user/destination', $data);
    }

    public function contact()
    {
        return view('user/contact');
    }
}
