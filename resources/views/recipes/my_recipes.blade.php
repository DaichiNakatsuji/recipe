@extends('layouts.app')

@section('content')
    <div class="mt-4 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold">マイレシピ一覧</h1>
        <div class="mt-2">
            <a href="{{ route('recipes.create') }}" class="btn btn-primary">
                新しいレシピを作成
            </a>
        </div>

        <ul class="mt-4">
            @foreach($recipes as $recipe)
                <li class="mb-4 p-4 border rounded">
                    <div class="flex justify-between items-center">
                        <div>
                            <strong>{{ $recipe->title }}</strong>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('recipes.edit', $recipe->id) }}" class="btn btn-sm btn-secondary">編集</a>
                            <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error"
                                    onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                            <form action="{{ route('recipes.toggle', $recipe->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-outline">
                                    {{ $recipe->is_public ? '非公開にする' : '公開にする' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
