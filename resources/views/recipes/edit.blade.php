@extends('layouts.app')

@section('content')
    <div class="mt-4 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">レシピ編集</h1>

        <form action="{{ route('recipes.update', $recipe->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block font-medium">タイトル</label>
                <input type="text" name="title" value="{{ old('title', $recipe->title) }}"
                    class="input input-bordered w-full">
            </div>

            <div id="ingredients">
                <h2 class="font-medium mt-4">材料</h2>
                <button type="button" onclick="addIngredient()" class="btn btn-sm btn-info mb-2">材料追加</button>
                <div class="space-y-2">
                    @foreach($recipe->ingredients as $i => $ingredient)
                        <div class="flex space-x-2">
                            <select name="ingredients[{{ $i }}][name]" class="select select-bordered">
                                @foreach(App\Models\Ingredient::all() as $ing)
                                    <option value="{{ $ing->ingredient_name }}"
                                        {{ $ing->ingredient_name === $ingredient->ingredient_name ? 'selected' : '' }}>
                                        {{ $ing->ingredient_name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="ingredients[{{ $i }}][num]"
                                value="{{ old("ingredients.$i.num", $ingredient->pivot->num) }}"
                                placeholder="量" class="input input-bordered flex-1">
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block font-medium">作り方</label>
                <textarea name="instructions" rows="5"
                    class="textarea textarea-bordered w-full">{{ old('instructions', $recipe->instructions) }}</textarea>
            </div>

            <div class="flex items-center space-x-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_public" value="1"
                        {{ old('is_public', $recipe->is_public) ? 'checked' : '' }} class="checkbox">
                    <span>公開する</span>
                </label>
                <label class="flex flex-col">
                    <span>公開日時</span>
                    <input type="datetime-local" name="publish_at"
                        value="{{ old('publish_at', optional($recipe->publish_at)->format('Y-m-d\TH:i')) }}"
                        class="input input-bordered">
                </label>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">更新</button>
            </div>
        </form>
    </div>

    <script>
        let index = {{ $recipe->ingredients->count() }};
        function addIngredient() {
            const container = document.querySelector('#ingredients .space-y-2');
            const div = document.createElement('div');
            div.className = 'flex space-x-2';
            div.innerHTML = `
                <select name="ingredients[${index}][name]" class="select select-bordered">
                    @foreach(App\Models\Ingredient::all() as $ingredient)
                        <option value="{{ $ingredient->ingredient_name }}">{{ $ingredient->ingredient_name }}</option>
                    @endforeach
                </select>
                <input type="text" name="ingredients[${index}][num]" placeholder="量" class="input input-bordered flex-1">
            `;
            container.appendChild(div);
            index++;
        }
    </script>
@endsection
