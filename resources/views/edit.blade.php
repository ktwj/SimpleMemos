@extends('layouts.app')

@section('javascript')
<script src="/js/confirm.js"></script>
@endsection 

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">メモ編集
        <form id="delete-form" action="{{ route('destroy') }}" method="POST">
            @csrf
            <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}" />
            <i class="fas fa-trash mr-4" type="submit" onclick="deleteHandle(event);"></i>
        </form>
    </div>
    <p class="text-right mt-2 mb-0 mr-3">
        作成日：{{ $edit_memo[0]['created_at'] }}<br>
        最終更新日：{{ $edit_memo[0]['updated_at'] }}
    </p>
    <form class="card-body my-card-body2" action="{{ route('update') }}" method="POST">
        @csrf
        <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}" />
        <div class="mb-3">
            <textarea class="form-control" name="content" rows="3" placeholder="ここに入力してください。">{{ $edit_memo[0]['content'] }}</textarea>
        </div>

        @error('content')
                <div class="alert alert-danger">メモ内容を入力してください。</div>
        @enderror

        @foreach($tags as $tag)
            <input class="form-check-label" type="checkbox" name="tags[]" id="{{ $tag['id'] }}" value="{{ $tag['id'] }}" {{ in_array($tag['id'],$include_tags) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $tag['id'] }}" >{{ $tag['name'] }}</label>
        @endforeach
        <br>
        <input type="text" class="form-controll w-50 mt-3 mb-4" name="new_tag" placeholder="新しいタグを入力" />
        <button type="submit" class="btn btn-primary mt-3 float-right">更新する</button>
    </form>  
</div>
@endsection
