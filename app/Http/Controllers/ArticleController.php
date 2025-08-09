<?php

namespace App\Http\Controllers;

use App\Category;
use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status     = $request->get('status');
        $keyword    = $request->get('keyword') ? $request->get('keyword') : '';
        $category   = $request->get('c') ? $request->get('c') : '';

        $query = Article::with('categories');

        // Filter by category if provided
        if ($category) {
            $query->whereHas('categories', function($q) use($category) {
                $q->where('name', 'LIKE', "%$category%");
            });
        }

        // Filter by status if provided
        if ($status) {
            $query->where('status', strtoupper($status));
        }

        // Filter by keyword if provided
        if ($keyword) {
            $query->where('title', 'LIKE', "%$keyword%");
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Validator::make($request->all(), [
            'title' => 'required|min:2|max:200|unique:articles,title', // ← Tambah unique
            'content' => 'required|min:10'
        ], [
            'title.unique' => 'Article with this title already exists. Please choose a different title.',
            'title.required' => 'Article title is required.',
            'title.min' => 'Article title must be at least 2 characters.',
            'title.max' => 'Article title cannot exceed 200 characters.',
            'content.required' => 'Article content is required.',
            'content.min' => 'Article content must be at least 10 characters.'
        ])->validate();

        try {
            DB::beginTransaction();

            $new_articles = new \App\Article;
            $new_articles->title = $request->get('title');
            $new_articles->slug = \Str::slug($request->get('title'), '-');
            $new_articles->content = $request->get('content');
            $new_articles->create_by = \Auth::user()->id;
            $new_articles->status = $request->get('save_action');
            $new_articles->save();

            // Save categories
            if ($request->get('categories')) {
                $new_articles->categories()->attach($request->get('categories'));
            }

            DB::commit();

            return redirect()->route('articles.index')->with('success', 'Article successfully created');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create article: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = \App\Article::with('categories')->findOrFail($id);
        return view('articles.edit', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Validator::make($request->all(), [
            'title' => 'required|min:2|max:200|unique:articles,title,' . $id, // ← Ignore artikel ini sendiri
            'content' => 'required|min:10'
        ], [
            'title.unique' => 'Article with this title already exists. Please choose a different title.',
            'title.required' => 'Article title is required.',
            'title.min' => 'Article title must be at least 2 characters.',
            'title.max' => 'Article title cannot exceed 200 characters.',
            'content.required' => 'Article content is required.',
            'content.min' => 'Article content must be at least 10 characters.'
        ])->validate();

        try {
            DB::beginTransaction();

            $article = \App\Article::findOrFail($id);
            $article->title = $request->get('title');
            $article->slug = \Str::slug($request->get('title'), '-');
            $article->content = $request->get('content');
            $article->status = $request->get('save_action');
            $article->update_by = \Auth::user()->id;
            $article->save();

            // Sync categories
            if ($request->get('categories')) {
                $article->categories()->sync($request->get('categories'));
            } else {
                $article->categories()->detach();
            }

            DB::commit();

            return redirect()->route('articles.index')->with('success', 'Article successfully updated');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update article: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $article = \App\Article::findOrFail($id);
            
            // Hapus relasi kategori terlebih dahulu
            $article->categories()->detach();
            
            // Hapus artikel (gunakan delete() bukan forceDelete())
            $article->delete();

            DB::commit();

            return redirect()->route('articles.index')->with('success', 'Article successfully deleted');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('articles.index')->with('error', 'Failed to delete article: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            try {
                $originName = $request->file('upload')->getClientOriginalName();
                $fileName = pathinfo($originName, PATHINFO_FILENAME);
                $extension = $request->file('upload')->getClientOriginalExtension();
                $fileName = $fileName . '_' . time() . '.' . $extension;
                
                $request->file('upload')->move(public_path('images'), $fileName);
                
                $CKEditorFuncNum = $request->input('CKEditorFuncNum');
                $url = asset('images/' . $fileName);
                $msg = 'Image uploaded successfully';
                $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
                
                @header('Content-type: text/html; charset=utf-8');
                echo $response;

            } catch (\Exception $e) {
                $msg = 'Upload failed: ' . $e->getMessage();
                $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '', '$msg')</script>";
                @header('Content-type: text/html; charset=utf-8');
                echo $response;
            }
        }
    }
}
