<?php

use App\Http\Controllers\Admin\AdminController;
use App\Models\Post;
use Livewire\Volt\Volt;
use App\Livewire\ListUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ChatStarterController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\InvitationController;

Volt::route('/', 'auth.login')->name('login');


Route::middleware(['auth', 'admins'])->group(function () {

    Volt::route('/admins/communities/{community}/view', 'admins.view-community')->name('admins.communities.view');
    Route::get('admins/user/{account}', [ProfileController::class, 'AdminUseraccount'])->name('admins.user.view');
    Route::get('/admin/users', ListUser::class)->name('admins.users');
    Route::view('admins/dashboard', 'admins.dashboard')->name('dashboard');
    Route::view('admins/analyse', 'admins.analyse')->name('analyse');
    Volt::route('/admins/communities', 'admins.community.index')->name('admins.communities.index');
    Volt::route('/admins/admins', 'admins.admins.index')->name('admins.admins.index');
    Volt::route('admin/reports/{post}', 'admins.showpost')->name('admins.post');
    Route::delete('/communities/{community}/posts/{post}', [AdminController::class, 'destroy'])->name('posts.destroy');

    Route::middleware(['super-admin'])->group(function(){
        Volt::route('/admins/communities/create', 'admins.community.create')->name('admins.communities.create');
        Volt::route('/admins/admins/create', 'admins.admins.create')->name('admins.admins.create');
        Volt::route('/admins/communities/{community}/edit', 'admins.community.edit')->name('admins.communities.edit');
    });

});

Route::middleware(['auth', 'users'])->group(function () {

    Volt::route('/users/home', 'user.home')->name('home');
    Volt::route('/users/friends', 'user.friends-list')->name('friends');
    Volt::route('/users/message', 'user.messages')->name('message');
    Volt::route('/users/search', 'user.community-search')->name('search');
    Volt::route('/users/favory', 'user.favory')->name('favory');
    Volt::route('/users/message/show/{conversation}', 'user.conversation')->name('chat.show');
    Route::get('/users/message/{user}/show', [ChatStarterController::class, 'startPrivateConversation'])->name('conversation');
    Route::view('users/view-profile/{profile}', 'users.view-profile')->name('view.profile');
    Route::get('users/profile', [ProfileController::class, 'show'])->name('users.profile');
    Route::get('users/account', [ProfileController::class, 'account'])->name('users.account');
    Route::get('users/account/{account}', [ProfileController::class, 'Useraccount'])->name('users.account.view');
    Route::post('users/profile/add-school', [ProfileController::class, 'addSchool'])->name('users.add.school');
    Route::post('users/invitation/{user}/send', [InvitationController::class, 'send'])->name('users.invitation.send');
    Route::post('users/invitation/{user}/accept', [InvitationController::class, 'accept'])->name('users.invitation.accept');
    Route::delete('users/profile/school/{school}/delete', [ProfileController::class, 'deleteSchool'])->name('users.delete.school');
    Route::put('users/profile/edit-generale', [ProfileController::class, 'general'])->name('users.edit.general');
    Route::put('users/profile/edit-profile', [ProfileController::class, 'updateProfile'])->name('users.edit.profile');
    Route::put('users/profile/edit-bio', [ProfileController::class, 'updateBio'])->name('users.edit.bio');
    Volt::route('/users/community/{community}', 'user.community')->name('community');
    Volt::route('/users/community-show/{community}', 'user.community-show')->name('community.show');

});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('auth.provider');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

require __DIR__.'/auth.php';
