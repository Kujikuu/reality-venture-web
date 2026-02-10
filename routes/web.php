<?php

use App\Http\Controllers\AdBannerController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Static pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/application-form', [PageController::class, 'applicationForm'])->name('application.form');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [PageController::class, 'termsOfService'])->name('terms.service');

// Form submission
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');

// Banner click tracking
Route::post('/banners/{adBanner}/click', [AdBannerController::class, 'trackClick'])->name('banners.click');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

// 404 fallback
Route::fallback([PageController::class, 'notFound']);
