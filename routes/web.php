<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Food Routes
Route::get('/food', [App\Http\Controllers\FoodController::class, 'index'])->name('food.index');
Route::get('/food/create', [App\Http\Controllers\FoodController::class, 'create'])->name('food.create');
Route::post('/food/store', [App\Http\Controllers\FoodController::class, 'store'])->name('food.store');
Route::get('/food/{food}', [App\Http\Controllers\FoodController::class, 'show'])->name('food.show');
Route::post('/food/{food}/update', [App\Http\Controllers\FoodController::class, 'update'])->name('food.update');
Route::get('/food/{food}/delete', [App\Http\Controllers\FoodController::class, 'delete'])->name('food.delete');

// Category Routes
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories/store', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');
Route::post('/categories/{category}/update', [App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
Route::get('/categories/{category}/delete', [App\Http\Controllers\CategoryController::class, 'delete'])->name('categories.delete');

// Table Routes
Route::get('/tables', [App\Http\Controllers\TableController::class, 'index'])->name('tables.index');
Route::get('/tables/create', [App\Http\Controllers\TableController::class, 'create'])->name('tables.create');
Route::post('/tables/store', [App\Http\Controllers\TableController::class, 'store'])->name('tables.store');
Route::get('/tables/{table}', [App\Http\Controllers\TableController::class, 'show'])->name('tables.show');
Route::post('/tables/{table}/update', [App\Http\Controllers\TableController::class, 'update'])->name('tables.update');
Route::get('/tables/{table}/delete', [App\Http\Controllers\TableController::class, 'delete'])->name('tables.delete');

// User CRUD Management Routes (Gated by Admin)
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/update', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
});

// Organisation CRUD Management Routes (Gated by Superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/organisations', [App\Http\Controllers\OrganisationController::class, 'index'])->name('organisations.index');
    Route::get('/organisations/create', [App\Http\Controllers\OrganisationController::class, 'create'])->name('organisations.create');
    Route::post('/organisations/store', [App\Http\Controllers\OrganisationController::class, 'store'])->name('organisations.store');
    Route::get('/organisations/{organisation}', [App\Http\Controllers\OrganisationController::class, 'show'])->name('organisations.show');
    Route::post('/organisations/{organisation}/update', [App\Http\Controllers\OrganisationController::class, 'update'])->name('organisations.update');
    Route::get('/organisations/{organisation}/delete', [App\Http\Controllers\OrganisationController::class, 'delete'])->name('organisations.delete');
});


