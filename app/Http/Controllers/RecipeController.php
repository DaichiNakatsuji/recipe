<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    // 一般公開されているレシピ一覧（トップページ）
    public function index()
    {
        $recipes = Recipe::where('is_public', true)->where(function($query) {$query->whereNull('publish_at')->orWhere('publish_at', '<=', now());})->with('user')->latest()->paginate(25);
        return view('recipes.index', compact('recipes'));
    }

    // レシピ詳細（誰でも閲覧可能）
    public function show($id)
    {
        // dd('test_show');
        $recipe = Recipe::with(['ingredients', 'user'])->findOrFail($id);

        // 非公開レシピにアクセスしようとしたら、トップにリダイレクト
        if (!$recipe->is_public && (!Auth::check() || Auth::id() !== $recipe->user_id)) {
            return redirect()->route('recipes.index')->with('error', 'このレシピは公開されていません。');
        }

        return view('recipes.show', compact('recipe'));
    }

    // ユーザーのマイページ用レシピ一覧
    public function myRecipes()
    {
        $recipes = Auth::user()->recipes()->with('ingredients')->latest()->get();
        return view('recipes.my_recipes', compact('recipes'));
    }

    // レシピ作成フォーム表示
    public function create()
    {
        $ingredients = Ingredient::all();
        // dd('test_create');
        return view('recipes.create', compact('ingredients'));
    }

    // レシピ保存処理
    public function store(Request $request)
    {
        // バリデーションのルール
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'is_public' => 'boolean',
            'publish_at' => 'nullable|date',
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'nullable|string|max:255',  // 材料は選択しなくてもOK
            'ingredients.*.num' => 'nullable|string|max:255',   // 量も指定しなくてもOK
            'new_ingredients' => 'nullable|array',
            'new_ingredients.*' => 'nullable|string|max:255',    // 新しい材料のバリデーション
        ]);

        DB::transaction(function () use ($validated) {
            // レシピの作成
            $recipe = Recipe::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'instructions' => $validated['instructions'],
                'is_public' => $validated['is_public'] ?? false,
                'publish_at' => $validated['publish_at'],
            ]);

            // 新しい材料の追加
            if (isset($validated['new_ingredients']) && is_array($validated['new_ingredients'])) {
                foreach ($validated['new_ingredients'] as $newIngredient) {
                    $ingredient = Ingredient::firstOrCreate([
                        'ingredient_name' => $newIngredient
                    ]);

                    $recipe->ingredients()->attach($ingredient->id);
                }
            }

            // 既存の材料の処理
            foreach ($validated['ingredients'] as $ingredientData) {
                if (!empty($ingredientData['name'])) { // 空の材料を無視
                    $ingredient = Ingredient::firstOrCreate([
                        'ingredient_name' => $ingredientData['name']
                    ]);

                    $recipe->ingredients()->attach($ingredient->id, [
                        'num' => $ingredientData['num'] ?? ''
                    ]);
                }
            }
        });

        return redirect()->route('recipes.my')->with('success', 'レシピを作成しました。');
    }

    // レシピ編集フォーム表示
    public function edit($id)
    {
        $recipe = Recipe::with('ingredients')->where('user_id', Auth::id())->findOrFail($id);
        return view('recipes.edit', compact('recipe'));
    }

    // レシピ更新処理
    public function update(Request $request, $id)
    {
        // ユーザーが所有するレシピを取得
        $recipe = Recipe::where('user_id', Auth::id())->findOrFail($id);

        // バリデーション
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'is_public' => 'boolean',
            'publish_at' => 'nullable|date',
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'required|string|max:255',  // 材料名のバリデーション
            'ingredients.*.num' => 'required|string|max:255',   // 材料の量のバリデーション
            'new_ingredient' => 'nullable|string|max:255',  // 新しい材料のバリデーション（もし存在する場合）
        ]);

        // トランザクションを開始
        DB::transaction(function () use ($recipe, $validated) {
            // レシピの基本情報を更新
            $recipe->update([
                'title' => $validated['title'],
                'instructions' => $validated['instructions'],
                'is_public' => $validated['is_public'] ?? false,
                'publish_at' => $validated['publish_at'],
            ]);

            // 既存の材料を削除
            $recipe->ingredients()->detach();

            // 新しい材料を追加（新しい材料がある場合は処理）
            if ($validated['new_ingredient']) {
                // 新しい材料を作成
                $ingredient = Ingredient::firstOrCreate([
                    'ingredient_name' => $validated['new_ingredient']
                ]);

                // 新しい材料をレシピに関連付ける
                $recipe->ingredients()->attach($ingredient->id, [
                    'num' => $validated['ingredients'][0]['num']  // 新しい材料の量
                ]);
            }

            // 既存の材料を更新
            foreach ($validated['ingredients'] as $ingredientData) {
                // 既存の材料を作成または取得
                $ingredient = Ingredient::firstOrCreate([
                    'ingredient_name' => $ingredientData['name']
                ]);

                // レシピと材料を関連付ける
                $recipe->ingredients()->attach($ingredient->id, [
                    'num' => $ingredientData['num']  // 材料の量を保存
                ]);
            }
        });

        // レシピが更新された後のリダイレクト
        return redirect()->route('recipes.my')->with('success', 'レシピを更新しました。');
    }

    // レシピ削除
    public function destroy($id)
    {
        $recipe = Recipe::where('user_id', Auth::id())->findOrFail($id);
        $recipe->delete();

        return redirect()->route('recipes.my')->with('success', 'レシピを削除しました。');
    }

    // 公開/非公開切り替え
    public function toggleVisibility($id)
    {
        $recipe = Recipe::where('user_id', Auth::id())->findOrFail($id);
        $recipe->is_public = !$recipe->is_public;
        $recipe->save();

        return redirect()->route('recipes.my')->with('success', '公開設定を更新しました。');
    }
}
