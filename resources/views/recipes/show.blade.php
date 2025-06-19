@extends('layouts.app')

@section('content')
    <div class="mt-4">
        <h1 class="text-3xl font-bold">{{ $recipe->title }}</h1>
        <p class="text-gray-600 mt-2"><strong>作成者:</strong> {{ $recipe->user->name }}</p>
        <p class="text-gray-600"><strong>公開状態:</strong> {{ $recipe->is_public ? '公開' : '非公開' }}</p>

        <h2 class="text-2xl font-semibold mt-4">材料</h2>
        <ul class="mt-2 list-disc list-inside">
            @foreach($recipe->ingredients as $ingredient)
                <li>{{ $ingredient->ingredient_name }}：{{ $ingredient->pivot->num }}</li>
            @endforeach
        </ul>

        <h2 class="text-2xl font-semibold mt-4">作り方</h2>
        <p class="mt-2">{{ $recipe->instructions }}</p>
    </div>
@endsection
