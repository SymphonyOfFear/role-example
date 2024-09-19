<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ResultsController;
use App\Http\Controllers\Teacher\ResultsController as TeacherResultsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware(['auth'])
    ->prefix('student')//deze prefix is toegevoegd zodat alle urls beginnen met student/
    ->name('student.')//deze naam is toegevoegd zodat alle routes in de groep de naam student. krijgen
    ->group(function () { //hier begint de groep
    Route::get('results', [ResultsController::class, 'index'])->name('results');

    // andere studentenroutes zoals profile, courses, grades, etc.
    // Route::get('courses', [CourseController::class, 'index'])->name('courses');
});//hier eindigt de groep

Route::prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('results', [TeacherResultsController::class, 'index'])
            ->name('results');
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
