<?php

// use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// トップページ（公開レシピ一覧）
Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');

// レシピ詳細（公開されているもの、もしくは自分のレシピのみ）
Route::get('/recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show');

// 認証済ユーザー用ルート（マイページ、投稿・編集・削除など）
Route::middleware(['auth'])->group(function () {

    // マイページ（ユーザー自身のレシピ一覧）
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');

    // レシピ作成
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');

    // レシピ編集
    Route::get('/recipes/{id}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{id}', [RecipeController::class, 'update'])->name('recipes.update');

    // レシピ削除
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // 公開/非公開切り替え
    Route::put('/recipes/{id}/toggle', [RecipeController::class, 'toggleVisibility'])->name('recipes.toggle');
});

require __DIR__.'/auth.php';
