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
        $recipes = Recipe::where('is_public', true)
          ->where(function($query) {
              $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
          })
          ->with('user')
          ->latest()
          ->get();

        return view('recipes.index', compact('recipes'));
    }

    // レシピ詳細（誰でも閲覧可能）
    public function show($id)
    {
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
        $ingredients = \App\Models\Ingredient::all();
        return view('recipes.create', compact('ingredients'));
    }

    // レシピ保存処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'is_public' => 'boolean',
            'publish_at' => 'nullable|date',
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.num' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $recipe = Recipe::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'instructions' => $validated['instructions'],
                'is_public' => $validated['is_public'] ?? false,
                'publish_at' => $validated['publish_at'],
            ]);

            foreach ($validated['ingredients'] as $ingredientData) {
                $ingredient = Ingredient::firstOrCreate([
                    'ingredient_name' => $ingredientData['name']
                ]);

                $recipe->ingredients()->attach($ingredient->id, [
                    'num' => $ingredientData['num']
                ]);
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
        $recipe = Recipe::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'is_public' => 'boolean',
            'publish_at' => 'nullable|date',
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.num' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($recipe, $validated) {
            $recipe->update([
                'title' => $validated['title'],
                'instructions' => $validated['instructions'],
                'is_public' => $validated['is_public'] ?? false,
                'publish_at' => $validated['publish_at'],
            ]);

            $recipe->ingredients()->detach();

            foreach ($validated['ingredients'] as $ingredientData) {
                $ingredient = Ingredient::firstOrCreate([
                    'ingredient_name' => $ingredientData['name']
                ]);

                $recipe->ingredients()->attach($ingredient->id, [
                    'num' => $ingredientData['num']
                ]);
            }
        });

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
