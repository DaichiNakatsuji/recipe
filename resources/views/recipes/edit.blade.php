@extends('layouts.app')

@section('content')
    <div class="mt-4 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">レシピ編集</h1>

        <form action="{{ route('recipes.update', $recipe->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div class="form-control my-4">
                <label for="title" class="label">
                    <span class="label-text">タイトル:</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $recipe->title) }}" class="input input-bordered w-full">
            </div>

            <div id="ingredients" class="form-control my-4">
                <label class="label">
                    <span class="label-text">材料:</span>
                </label>
                <div class="space-y-2" id="ingredients-list">
                    @foreach($recipe->ingredients as $i => $ingredient)
                        <div class="flex space-x-2">
                            <select name="ingredients[{{ $i }}][name]" class="select select-bordered flex-1">
                                @foreach(App\Models\Ingredient::all() as $ing)
                                    <option value="{{ $ing->ingredient_name }}"
                                        {{ $ing->ingredient_name === $ingredient->ingredient_name ? 'selected' : '' }}>
                                        {{ $ing->ingredient_name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="ingredients[{{ $i }}][num]" value="{{ old("ingredients.$i.num", $ingredient->pivot->num) }}" placeholder="量" class="input input-bordered flex-1">
                            <button type="button" class="btn btn-sm btn-danger remove-ingredient">削除</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-ingredient-btn" class="btn btn-sm btn-info mt-2">材料追加</button>
                <div class="flex space-x-2 mt-2">
                    <input type="text" id="new-ingredient" name="new_ingredient" placeholder="新しい材料名を入力" class="input input-bordered flex-1">
                    <button type="button" id="add-new-ingredient" class="btn btn-sm btn-info">新しい材料を追加</button>
                </div>
            </div>

            <div class="form-control my-4">
                <label for="instructions" class="label">
                    <span class="label-text">作り方:</span>
                </label>
                <textarea name="instructions" rows="5" class="textarea textarea-bordered w-full">{{ old('instructions', $recipe->instructions) }}</textarea>
            </div>

            <div class="form-control my-4">
                <label class="cursor-pointer label">
                    <span class="label-text">公開する</span>
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $recipe->is_public) ? 'checked' : '' }} class="checkbox">
                </label>
            </div>

            <div class="form-control my-4">
                <label class="label">
                    <span class="label-text">公開日時:</span>
                </label>
                <input type="datetime-local" name="publish_at"
                    value="{{ old('publish_at', optional($recipe->publish_at)->format('Y-m-d\TH:i')) }}" class="input input-bordered">
            </div>

            <button type="submit" class="btn btn-primary btn-outline">更新</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addBtn = document.getElementById('add-ingredient-btn');
            const addNewIngredientBtn = document.getElementById('add-new-ingredient');
            const list = document.getElementById('ingredients-list');
            const newIngredientInput = document.getElementById('new-ingredient');
            let index = {{ $recipe->ingredients->count() }};

            // 材料の選択肢を動的に生成
            const ingredientOptions = `@foreach (App\Models\Ingredient::all() as $ingredient)
                <option value="{{ $ingredient->ingredient_name }}">{{ $ingredient->ingredient_name }}</option>
            @endforeach`;

            // 材料追加ボタンのイベントリスナー
            addBtn.addEventListener('click', function () {
                const div = document.createElement('div');
                div.className = 'flex space-x-2';
                div.innerHTML = `
                    <select name="ingredients[${index}][name]" class="select select-bordered flex-1">
                        ${ingredientOptions}
                    </select>
                    <input type="text" name="ingredients[${index}][num]" placeholder="量" class="input input-bordered flex-1">
                    <button type="button" class="btn btn-sm btn-danger remove-ingredient">削除</button>
                `;
                list.appendChild(div);
                index++;
            });

            // 新しい材料を追加ボタンのイベントリスナー
            addNewIngredientBtn.addEventListener('click', function () {
                const newIngredient = newIngredientInput.value.trim();
                if (newIngredient) {
                    const div = document.createElement('div');
                    div.className = 'flex space-x-2';
                    div.innerHTML = `
                        <select name="ingredients[${index}][name]" class="select select-bordered flex-1">
                            <option value="${newIngredient}" selected>${newIngredient}</option>
                        </select>
                        <input type="text" name="ingredients[${index}][num]" placeholder="量" class="input input-bordered flex-1">
                        <button type="button" class="btn btn-sm btn-danger remove-ingredient">削除</button>
                    `;
                    list.appendChild(div);
                    index++;
                    newIngredientInput.value = '';
                }
            });

            // 削除ボタンのイベントリスナー
            list.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-ingredient')) {
                    e.target.closest('div').remove();
                }
            });
        });
    </script>
@endsection
