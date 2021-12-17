<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*',function($view){
            //Memoモデルをインスタンス化
            $memo_model = new Memo();
            //メモを取得
            $memos = $memo_model->getMyMemo();

            $tags = Tag::where('user_id','=',\Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id','desc')
            ->get();

            $view->with('memos',$memos)->with('tags',$tags);
        });
        Schema::defaultStringLength(191);
    }
}
