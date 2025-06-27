@extends('layouts.app')

@section('content')

    <!-- <div class="prose ml-4">
        <h2 class="text-lg">レシピ作成</h2>
    </div> -->

    <div class="mt-4 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">レシピ作成</h1>

        <form method="POST" action="{{ route('recipes.store') }}">
            @csrf

                <div class="form-control my-4">
                    <label for="title" class="label">
                        <span class="label-text">タイトル:</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" class="input input-bordered w-full">
                </div>

                <div id="ingredients" class="form-control my-4">
                    <label class="label">
                        <span class="label-text">材料:</span>
                    </label>
                    <div class="space-y-2" id="ingredients-list">
                        <div class="flex space-x-2">
                            <select name="ingredients[0][name]" class="select select-bordered flex-1">
                                <option value="">-- 材料を選択 --</option>
                                @foreach ($ingredients as $ingredient)
                                    <option value="{{ $ingredient->ingredient_name }}"
                                        {{ old('ingredients.0.name') == $ingredient->ingredient_name ? 'selected' : '' }}>
                                        {{ $ingredient->ingredient_name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="ingredients[0][num]" placeholder="量" value="{{ old('ingredients.0.num') }}" class="input input-bordered flex-1">
                            <button type="button" class="btn btn-sm btn-danger remove-ingredient">削除</button>
                        </div>
                    </div>
                    <button type="button" id="add-ingredient-btn" class="btn btn-sm btn-info mt-2">材料追加</button>
                    <div class="flex space-x-2 mt-2">
                        <input type="text" id="new-ingredient" name="new_ingredients[0]" placeholder="新しい材料名を入力" class="input input-bordered flex-1">
                        <button type="button" id="add-new-ingredient" class="btn btn-sm btn-info">新しい材料を追加</button>
                    </div>
                </div>

                <div class="form-control my-4">
                    <label for="instructions" class="label">
                        <span class="label-text">作り方:</span>
                    </label>
                    <textarea name="instructions" rows="5" class="textarea textarea-bordered w-full">{{ old('instructions') }}</textarea>
                </div>

                <div class="form-control my-4">
                    <label class="cursor-pointer label">
                        <span class="label-text">公開する</span>
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }} class="checkbox">
                    </label>
                </div>

                <div class="form-control my-4">
                    <label class="label">
                        <span class="label-text">公開日時:</span>
                    </label>
                    <input type="datetime-local" name="publish_at" value="{{ old('publish_at') }}" class="input input-bordered">
                </div>

            <button type="submit" class="btn btn-primary btn-outline">保存</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addBtn = document.getElementById('add-ingredient-btn');
            const addNewIngredientBtn = document.getElementById('add-new-ingredient');
            const list = document.getElementById('ingredients-list');
            const newIngredientInput = document.getElementById('new-ingredient');
            let index = 1;

            const ingredientOptions = `@foreach ($ingredients as $ingredient)
                <option value="{{ $ingredient->ingredient_name }}">{{ $ingredient->ingredient_name }}</option>
            @endforeach`;

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

            // 削除ボタンのイベントリスナー（親要素にイベントをバインド）
            list.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-ingredient')) {
                    e.target.closest('div').remove();
                }
            });
        });
    </script>
@endsection
