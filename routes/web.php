<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ApplicationController;

// Static pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/application-form', [PageController::class, 'applicationForm'])->name('application.form');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms.service');

// Form submission
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');

// 404 fallback
Route::fallback([PageController::class, 'notFound']);
