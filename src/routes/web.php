<?php


use Hilsonxhero\Xauth\Facade\XauthFacade;
use Illuminate\Support\Facades\Route;

// Route::get('/test', function () {
//     $dd = XauthFacade::store();
//     dd($dd);
// });

Route::namespace('\Hilsonxhero\Xauth\Http\Controllers')->group(function () {
    Route::get('test', 'AuthController@index');
});
