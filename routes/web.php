<?php

use App\Http\Controllers\Admin\BlogPostsController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaxonomiesController;
use App\Http\Controllers\Admin\TermsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Front\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function (string $locale) {
    $supported = config('app.supported_locales', ['en', 'ru', 'am']);
    if (in_array($locale, $supported, true)) {
        session(['locale' => $locale]);
        cookie()->queue('locale', $locale, 60 * 24 * 365); // 1 year
    }

    return redirect()->back();
})->name('locale.switch');

Route::get('/', [PageController::class, 'home'])->name('front.home');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin(?:/|$)|login$|register$|logout$|locale(?:/|$)|dashboard$).+')
    ->name('front.page');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');
    Route::get('/dashboard', function () {
        return view('admin');
    })->name('dashboard');
    Route::get('/media', function () {
        return view('admin');
    })->name('media');
    Route::get('/media/create', function () {
        return view('admin');
    })->name('media.create');
    Route::get('/media/{media}/edit', function () {
        return view('admin');
    })->name('media.edit')->whereNumber('media');
    Route::get('/media-list-json', [MediaController::class, 'index'])->name('media.api');
    Route::post('/media/api', [MediaController::class, 'store'])->name('media.api.store');
    Route::get('/media/api/{media}', [MediaController::class, 'show'])->name('media.api.show')->whereNumber('media');
    Route::put('/media/api/{media}', [MediaController::class, 'update'])->name('media.api.update')->whereNumber('media');
    Route::delete('/media/api/{media}', [MediaController::class, 'destroy'])->name('media.api.destroy')->whereNumber('media');
    Route::get('/content', function () {
        return view('admin');
    })->name('content');
    Route::get('/content/pages/create', function () {
        return view('admin');
    })->name('content.pages.create');
    Route::get('/content/pages/{page}/edit', function () {
        return view('admin');
    })->name('content.pages.edit')->whereNumber('page');
    Route::get('/content/api/pages', [ContentController::class, 'index'])->name('content.api.pages');
    Route::get('/content/api/pages/check-slug', [ContentController::class, 'checkSlug'])->name('content.api.pages.checkSlug');
    Route::post('/content/api/pages', [ContentController::class, 'store'])->name('content.api.pages.store');
    Route::get('/content/api/pages/{page}', [ContentController::class, 'show'])->name('content.api.pages.show');
    Route::put('/content/api/pages/{page}', [ContentController::class, 'update'])->name('content.api.pages.update');
    Route::delete('/content/api/pages/{page}', [ContentController::class, 'destroy'])->name('content.api.pages.destroy');
    Route::get('/settings', function () {
        return redirect()->route('admin.settings.organization');
    })->name('settings');
    Route::get('/settings/organization', function () {
        return view('admin');
    })->name('settings.organization');
    Route::get('/settings/global', function () {
        return view('admin');
    })->name('settings.global');
    Route::get('/settings/languages', function () {
        return view('admin');
    })->name('settings.languages');
    Route::get('/settings/languages/create', function () {
        return view('admin');
    })->name('settings.languages.create');
    Route::get('/settings/languages/{id}/edit', function () {
        return view('admin');
    })->name('settings.languages.edit')->whereNumber('id');
    Route::get('/settings/taxonomies/create', function () {
        return view('admin');
    })->name('settings.taxonomies.create');
    Route::get('/settings/taxonomies/{taxonomy}/edit', function () {
        return view('admin');
    })->name('settings.taxonomies.edit');
    Route::get('/settings/taxonomies', function () {
        return view('admin');
    })->name('settings.taxonomies');
    Route::get('/settings/api/organization', [SettingsController::class, 'organization']);
    Route::post('/settings/api/organization', [SettingsController::class, 'saveOrganization']);
    Route::get('/settings/api/global', [SettingsController::class, 'global']);
    Route::post('/settings/api/global', [SettingsController::class, 'saveGlobal']);
    Route::get('/settings/api/languages', [SettingsController::class, 'languages']);
    Route::post('/settings/api/languages', [SettingsController::class, 'storeLanguage']);
    Route::get('/settings/api/languages/{language}', [SettingsController::class, 'getLanguage']);
    Route::put('/settings/api/languages/{language}', [SettingsController::class, 'updateLanguage']);
    Route::delete('/settings/api/languages/{language}', [SettingsController::class, 'destroyLanguage']);
    Route::get('/blog', function () {
        return view('admin');
    })->name('blog');
    Route::redirect('/blog/taxonomies', '/admin/settings/taxonomies')->name('blog.taxonomies');
    Route::get('/blog/taxonomies/{taxonomy}/terms/create', function () {
        return view('admin');
    })->name('blog.taxonomies.terms.create');
    Route::get('/blog/taxonomies/{taxonomy}/terms/{term}/edit', function () {
        return view('admin');
    })->name('blog.taxonomies.terms.edit')->whereNumber('term');
    Route::get('/blog/taxonomies/{taxonomy}', function () {
        return view('admin');
    })->name('blog.taxonomies.show');
    Route::get('/blog/api/taxonomies', [TaxonomiesController::class, 'index'])->name('blog.api.taxonomies');
    Route::post('/blog/api/taxonomies', [TaxonomiesController::class, 'store'])->name('blog.api.taxonomies.store');
    Route::get('/blog/api/taxonomies/{taxonomy}', [TaxonomiesController::class, 'show'])->name('blog.api.taxonomies.show');
    Route::put('/blog/api/taxonomies/{taxonomy}', [TaxonomiesController::class, 'update'])->name('blog.api.taxonomies.update');
    Route::delete('/blog/api/taxonomies/{taxonomy}', [TaxonomiesController::class, 'destroy'])->name('blog.api.taxonomies.destroy');
    Route::get('/blog/api/taxonomies/{taxonomy}/terms/{term}', [TermsController::class, 'show'])->name('blog.api.taxonomies.terms.show')->whereNumber('term');
    Route::post('/blog/api/taxonomies/{taxonomy}/terms', [TermsController::class, 'store'])->name('blog.api.taxonomies.terms.store');
    Route::put('/blog/api/taxonomies/{taxonomy}/terms/{term}', [TermsController::class, 'update'])->name('blog.api.taxonomies.terms.update')->whereNumber('term');
    Route::delete('/blog/api/taxonomies/{taxonomy}/terms/{term}', [TermsController::class, 'destroy'])->name('blog.api.taxonomies.terms.destroy')->whereNumber('term');
    Route::get('/blog/posts', function () {
        return view('admin');
    })->name('blog.posts');
    Route::get('/blog/posts/create', function () {
        return view('admin');
    })->name('blog.posts.create');
    Route::get('/blog/posts/{post}/edit', function () {
        return view('admin');
    })->name('blog.posts.edit')->whereNumber('post');
    Route::get('/blog/api/posts', [BlogPostsController::class, 'index'])->name('blog.api.posts');
    Route::get('/blog/api/posts/check-slug', [BlogPostsController::class, 'checkSlug'])->name('blog.api.posts.checkSlug');
    Route::post('/blog/api/posts', [BlogPostsController::class, 'store'])->name('blog.api.posts.store');
    Route::get('/blog/api/posts/{post}', [BlogPostsController::class, 'show'])->name('blog.api.posts.show')->whereNumber('post');
    Route::put('/blog/api/posts/{post}', [BlogPostsController::class, 'update'])->name('blog.api.posts.update')->whereNumber('post');
    Route::delete('/blog/api/posts/{post}', [BlogPostsController::class, 'destroy'])->name('blog.api.posts.destroy')->whereNumber('post');
    Route::get('/users', function () {
        return view('admin');
    })->name('users');
    Route::get('/users/create', function () {
        return view('admin');
    })->name('users.create');
    Route::get('/users/{user}/edit', function () {
        return view('admin');
    })->name('users.edit')->whereNumber('user');
    Route::get('/users/api', [UsersController::class, 'index'])->name('users.api');
    Route::post('/users/api', [UsersController::class, 'store'])->name('users.api.store');
    Route::get('/users/api/{user}', [UsersController::class, 'show'])->name('users.api.show')->whereNumber('user');
    Route::put('/users/api/{user}', [UsersController::class, 'update'])->name('users.api.update')->whereNumber('user');
    Route::delete('/users/api/{user}', [UsersController::class, 'destroy'])->name('users.api.destroy')->whereNumber('user');

});
