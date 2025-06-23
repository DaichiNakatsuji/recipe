@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mt-4">公開レシピ一覧</h1>
    <ul class="mt-4">
        @foreach($recipes as $recipe)
            <li class="mb-2">
                <a href="{{ route('recipes.show', $recipe->id) }}" class="text-blue-600 hover:underline">
                    {{ $recipe->title }} by {{ $recipe->user->name }}
                </a>
            </li>
        @endforeach
    </ul>
    {{-- ページネーションのリンク --}}
    {{ $recipes->links() }}
@endsection
