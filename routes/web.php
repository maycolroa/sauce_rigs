<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/{any}', 'ApplicationController')->where('any', '.*');
    Route::get('/', function(){
        return redirect('/biologicalmonitoring/audiometry');
    });
    Route::prefix('biologicalmonitoring')->group(function () {
        Route::post('audiometry/data', 'BiologicalMonitoring\AudiometryController@data');
        Route::resource('audiometry', 'BiologicalMonitoring\AudiometryController');
            
    });
    Route::prefix('selects')->group(function () {
        Route::post('employees', 'Administrative\EmployeesController@data');  
    });

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
});
