@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">新規メモ作成</div>
        <form class="card-body my-card-body" action="{{ route('store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <textarea class="form-control" name="content" rows="3" placeholder="ここに入力してください。"></textarea>
            </div>
            @error('content')
                <div class="alert alert-danger">メモ内容を入力してください。</div>
            @enderror
            @foreach($tags as $tag)
                <input class="form-check-label" type="checkbox" name="tags[]" id="{{ $tag['id'] }}" value="{{ $tag['id'] }}">
                <label class="form-check-label" for="{{ $tag['id'] }}">{{ $tag['name'] }}　</label>
            @endforeach
            <br>
            <input type="text" class="form-controll w-50 mt-3 mb-4" name="new_tag" placeholder="新しいタグを入力" />
            <button type="submit" class="btn btn-primary mt-3 float-right">保存する</button>
        </form>
    </div>  
</div>
@endsection
