<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Memo;
use App\Models\Tag;
use App\Models\MemoTag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //ここでメモを取得

        $tags = Tag::where('user_id', '=', \Auth::id())
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc')
                    ->get();
        //dd($memos);   //取得したメモのテスト
        //dd($tags);    //取得したタグのテスト

        return view('create', compact('tags'));
    }

    public function store(Request $request)
    {
        $posts = $request->all();
        $request->validate([ 'content' => 'required' ]);
        //dd($posts);
        
        DB::transaction(function() use($posts) {
            $memo_id = Memo::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            $tag_exist = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])
                ->exists();
            if(!empty($posts['new_tag']) && !$tag_exist){
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                MemoTag::insert(['memo_id' => $memo_id,'tag_id' => $tag_id]);                
            }
            if(!empty($posts['tags'])){
            foreach($posts['tags'] as $tag){
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
            }}

        });
        return redirect(route('home'));
    }

    public function edit($id)
    {
        //ここでメモを取得
        $edit_memo = Memo::select('memos.*','tags.id as tag_id')
                        ->leftJoin('memo_tags','memo_tags.memo_id','=','memos.id')
                        ->leftJoin('tags','memo_tags.tag_id','=','tags.id')
                        ->where('memos.user_id','=',\Auth::id())
                        ->where('memos.id','=',$id)
                        ->whereNull('memos.deleted_at')
                        ->get();

        $include_tags = [];

        foreach($edit_memo as $memo){
            array_push($include_tags,$memo['tag_id']);
        }

        $tags = Tag::where('user_id', '=', \Auth::id())
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc')
                    ->get();
        //dd($memos,$edit_memo,$include_tags);   //取得したメモのテスト
        return view('edit', compact('edit_memo','include_tags','tags'));
    }

    public function update(Request $request)
    {
        $posts = $request->all();
        //dd(\Auth::id(),$posts);//ログインIDとメソッドの引数のとった値を展開するテスト
        $request->validate([ 'content' => 'required' ]);

        DB::transaction(function() use($posts){
        Memo::where('id', $posts['memo_id'])->update(['content' => $posts['content'], 'user_id' => \Auth::id()]);

        //メモとタグの紐づけをすべて解除
        MemoTag::where('memo_id','=', $posts['memo_id'])->delete();

        //再度メモとタグの紐づけ
        if(!empty($posts['tags'])){
        foreach($posts['tags'] as $tag){
            MemoTag::insert(['memo_id' => $posts['memo_id'],'tag_id' => $tag]);
        }}

        //新しいタグの入力があればinsertして紐づけ
        $tag_exist = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])
            ->exists();
        if(!empty($posts['new_tag']) && !$tag_exist){
            $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
            MemoTag::insert(['memo_id' => $posts['memo_id'],'tag_id' => $tag_id]);
        }
        });

        return redirect(route('home'));
    }

    public function destroy(Request $request)
    {
        $posts = $request->all();
//      dd(\Auth::id(),$posts); //ログインIDとメソッドの引数のとった値を展開するテスト

//      Memo::where('id', $posts['memo_id'])-delete(); これをやると物理削除になりデータごと消える
        Memo::where('id', $posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s",time())]);
        return redirect(route('home'));
    }
}
