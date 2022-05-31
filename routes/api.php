<?php

use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\File\DirectoryController;
use App\Http\Controllers\Api\v1\File\FileController;
use App\Http\Middleware\VerifyApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(FileController::class)->prefix('v1/files')
    ->group(function () {
        // Route::get('', 'index')->name('file.index');
        Route::post('', 'store')->name('file.store');
        Route::get('{id}', 'download')->name('file.download');
    });
Route::controller(DirectoryController::class)->prefix('v1/directories')
    ->group(function () {
        Route::get('{id}', 'index')
            ->name('directory.index');
        // Route::get('v1/directories/{id}', [DirectoryController::class, 'show'])
        //     ->name('directory.show');
        Route::post('',  'store')
            ->name('directory.store');
    });

Route::post('v1/register', [RegisterController::class, 'store'])
    ->name('register.store')->withoutMiddleware(VerifyApiToken::class);
