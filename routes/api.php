<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\UserSettingController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Domain\DomainController;
use App\Http\Controllers\Domain\DomainSettingController;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Event\EventInviteController;
use App\Http\Controllers\Media\FileController;
use App\Http\Controllers\Media\DirectoryController;
use App\Http\Controllers\Media\MediaController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Permission\RoleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__ . '/api/debug.php';

// Routes needing: no authentication
Route::get('/domain/settings', [DomainController::class, 'index']);
Route::get('/event-invite/{code}', [EventController::class, 'assignInvite'])->name('event-invite.assign');

// Routes needing: authentication
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth-info of current user
    Route::get('/user', [AuthUserController::class, 'getUser']);
    Route::get('/session', [AuthUserController::class, 'getSession']);

    // Domain info base units
    Route::get('/domain/units', [DomainController::class, 'indexUnits']);

    // Routes needing: authentication, two factor authentication
    Route::middleware(['verified', 'verified.tfa'])->group(function () {

        // Personal User Routes
        Route::prefix('user')->group(function () {
            Route::patch('/settings', [UserSettingController::class, 'update']);
            Route::patch('/password', [AuthUserController::class, 'updatePassword']);
            Route::delete('/', [AuthUserController::class, 'delete']);
    
            Route::prefix('two-factor')->group(function () {
                Route::put('/default/{method}', [TwoFactorController::class, 'setDefaultTfaMethod']);
                Route::delete('/destroy/{method}', [TwoFactorController::class, 'destroyTfaMethod']);
                
                Route::prefix('backup')->group(function () {
                    Route::get('/show', [TwoFactorController::class, 'showTfaBackupCodes']);
                    Route::post('/generate', [TwoFactorController::class, 'generateTfaBackupCodes']);
                });
        
                Route::prefix('totp')->group(function () {
                    Route::put('/setup', [TwoFactorController::class, 'setupTfaTotp']);
                    Route::put('/enable', [TwoFactorController::class, 'enableTfaTotp']);
                });
            });
        });
    
        // Domain
        Route::patch('/settings', [DomainSettingController::class, 'update']);
    
        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index']);
    
        // Roles
        Route::get('/roles/basic', [RoleController::class, 'indexBasic']);
        Route::resource('/roles', RoleController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        Route::delete('/roles', [RoleController::class, 'destroyMany']);
    
        // Users
        Route::get('/users/basic', [UserController::class, 'indexBasic']);
        Route::resource('/users', UserController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        Route::delete('/users', [UserController::class, 'destroyMany']);
    
        // Companies
        Route::resource('/companies', CompanyController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        Route::delete('/companies', [CompanyController::class, 'destroyMany']);
    
        // Media Upload
        Route::post('/upload', [FileController::class, 'upload']);
    
        // Media directory
        Route::post('/directory', [DirectoryController::class, 'store']);
    
        // Media
        Route::get('/media/{path}', [MediaController::class, 'index'])->where('path', '(.*)');
        Route::post('/media/copy', [MediaController::class, 'copy']);
        Route::patch('/media/share', [MediaController::class, 'share']);
        Route::patch('/media/move', [MediaController::class, 'move']);
        Route::patch('/media/rename', [MediaController::class, 'rename']);
        Route::patch('/media/discovery', [MediaController::class, 'discovery']);
        Route::delete('/media', [MediaController::class, 'destroy']);



        // Event
        Route::post('/events/{event}/invites/import', [EventInviteController::class, 'import']);
        Route::patch('/events/{event}/invites/email', [EventInviteController::class, 'sendTemplatedEmail']);
        Route::delete('/events/{event}/invites', [EventInviteController::class, 'destroyMany']);
        Route::resource('/events/{event}/invites', EventInviteController::class)->only(['show', 'index', 'store', 'update', 'destroy']);

        Route::resource('/events', EventController::class)->only(['show', 'index', 'store', 'update', 'destroy']);
        Route::delete('/events', [EventController::class, 'destroyMany']);
    });
});