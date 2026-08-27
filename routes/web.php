<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('posts.index'));
Route::resource('posts', PostController::class);
