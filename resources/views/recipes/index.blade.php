@extends('layouts.app')

@section('content')
    <div class="mt-4 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mt-4">公開レシピ一覧</h1>
        <ul class="mt-4">
            @foreach($recipes as $recipe)
                <li class="mb-2">
                    <a href="{{ route('recipes.show', $recipe->id) }}" class="text-blue-600 hover:underline text-lg">
                        {{ $recipe->title }}
                    </a>
                    by {{ $recipe->user->name }}
                </li>
            @endforeach
        </ul>
        {{-- ページネーションのリンク --}}
        {{ $recipes->links() }}
    </div>
@endsection
