<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

Route::get('/', function(){return redirect('/home');});
Route::get('/home', 'UserController@home')->name('home');
Route::get('/blog', 'UserController@blog')->name('blog');
Route::get('/blog/{slug}', 'UserController@show_article')->name('blog.show');
Route::get('/destination', 'UserController@destination')->name('destination');
Route::get('/destination/{slug}', 'UserController@show_destination')->name('destination.show');
Route::get('/contact', 'UserController@contact')->name('contact');

Route::prefix('admin')->group(function(){

  Route::get('/', function(){
    return view('auth/login');
  });
  
  // handle route register
  Route::match(["GET", "POST"], "/register", function(){ 
    return redirect("/login"); 
  })->name("register");
  
  Auth::routes();
  
  // Route Dashboard
  Route::get('/dashboard', 'DashboardController@index')->middleware('auth');
  
  // route categories
  Route::get('/categories/{category}/restore', 'CategoryController@restore')->name('categories.restore');
  Route::delete('/categories/{category}/delete-permanent', 'CategoryController@deletePermanent')->name('categories.delete-permanent');
  Route::get('/ajax/categories/search', 'CategoryController@ajaxSearch');
  Route::resource('categories', 'CategoryController')->middleware('auth');
  
  // route article
  Route::post('/articles/upload', 'ArticleController@upload')->name('articles.upload')->middleware('auth');
  Route::resource('/articles', 'ArticleController')->middleware('auth');
  
  // route destination
  Route::resource('/destinations', 'DestinationController')->middleware('auth');
    
  // Route about
  Route::get('/abouts', 'AboutController@index')->name('abouts.index')->middleware('auth');
  Route::get('/abouts/{about}/edit', 'AboutController@edit')->name('abouts.edit')->middleware('auth');
  Route::put('/abouts/{about}', 'AboutController@update')->name('abouts.update')->middleware('auth');
    
  // route contact
  Route::post('/contact/send', 'ContactController@send')->name('contact.send');
  
  // route twitter integration (localhost only)
  Route::get('/twitter/test', function() {
    if (app()->environment('local')) {
      $twitterService = new \App\Services\TwitterService();
      $tweets = $twitterService->fetchLatestTweets(5);
      return response()->json([
        'message' => 'Twitter API Test',
        'tweets_found' => count($tweets),
        'tweets' => $tweets
      ]);
    }
    abort(404);
  })->name('admin.twitter.test')->middleware('auth');
  
  Route::get('/twitter/sync', function() {
    if (app()->environment('local')) {
      $twitterService = new \App\Services\TwitterService();
      $synced = $twitterService->syncTweetsToArticles();
      return response()->json([
        'message' => "Successfully synced {$synced} tweets",
        'synced' => $synced
      ]);
    }
    abort(404);
  })->name('admin.twitter.sync')->middleware('auth');
});