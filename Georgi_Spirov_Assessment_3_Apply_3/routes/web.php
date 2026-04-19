<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyProjectImagesController;
use App\Http\Controllers\ProjectCommentsController;
use App\Http\Controllers\ProjectRatingsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\UserProjectFavouriteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyProjectsController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// projects
Route::get('/projects/{project}', [ProjectsController::class, 'show'])->name('projects.show');

// project comments
Route::get('/projects/{project}/comments', [ProjectsController::class, 'comments'])->name('projects.comments');

Route::middleware(['auth', 'verified'])->group(function () {
    // my projects
    Route::prefix('/my-projects')->group(function () {
        Route::get('/', [MyProjectsController::class, 'index'])->name('my-projects.index');
        Route::get('/create', [MyProjectsController::class, 'create'])->name('my-projects.create');
        Route::post('/create', [MyProjectsController::class, 'store'])->name('my-projects.store');

        // project aware
        Route::prefix('/{project}')->group(function () {
            Route::get('/edit', [MyProjectsController::class, 'edit'])->name('my-projects.edit');
            Route::post('/update', [MyProjectsController::class, 'update'])->name('my-projects.update');
            Route::post('/delete', [MyProjectsController::class, 'delete'])->name('my-projects.delete');
            Route::get('/images', [MyProjectImagesController::class, 'index'])->name('my-projects.images');
            Route::post('/images', [MyProjectImagesController::class, 'upload'])->name('my-projects.images.upload');
            Route::post('/images/{image}/delete', [MyProjectImagesController::class, 'delete'])->name('my-projects.images.delete');
        });
    });

    // project comments
    Route::post('/project-comments', [ProjectCommentsController::class, 'store'])->name('project-comments.store');
    Route::post('/project-comments/{comment}', [ProjectCommentsController::class, 'delete'])->name('project-comments.delete');

    // project ratings
    Route::post('/project-ratings', [ProjectRatingsController::class, 'store'])->name('project-ratings.store');

    // user project favourites
    Route::post('/user-project-favourites', [UserProjectFavouriteController::class, 'toggle'])->name('user-project-favourites.toggle');
});

require __DIR__.'/auth.php';
