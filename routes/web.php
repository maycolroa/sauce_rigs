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

Route::get('export/{url}',function($url){
  return Storage::disk('public')->download(base64_decode($url));
});

Route::middleware(['auth'])->group(function () { 
    Route::get('appWithModules', 'ApplicationController@appsWhithModules');
    Route::get('getCompanies', 'ApplicationController@getCompanies');
    Route::post('changeCompany', 'ApplicationController@changeCompany');

    //GET methods
    Route::get('templates/audiometryimport',function(){
      return Storage::disk('local')->download('/templates/PlantillaImportacionAudiometria.xlsx');
      
    });

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    //Routes diferents to GET
    Route::prefix('biologicalmonitoring')->group(function () {
      Route::post('audiometry/data', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@data');
      Route::post('audiometry/import', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@import');
      Route::post('audiometry/export', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@export');
      Route::post('audiometry/multiselect_epp', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectEPP');
      Route::post('audiometry/reportPta', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@reportPta');
      Route::ApiResource('audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController');   
    });

    Route::prefix('selects')->group(function () {
        Route::post('employees', 'Administrative\EmployeesController@multiselect');  
        Route::post('multiselect', 'ApplicationController@multiselect');
        Route::post('roles', 'Administrative\Roles\RoleController@multiselect');  
        Route::post('modulesGroup', 'ApplicationController@multiselectGroupModules');
        Route::post('permissions', 'Administrative\Roles\RoleController@multiselectPermissions');
        Route::post('areas', 'Administrative\EmployeeAreaController@multiselect');  
        Route::post('regionales', 'Administrative\EmployeeRegionalController@multiselect');
        Route::post('years/audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectYears');  
    });

    //Administrativo
    Route::prefix('administration')->group(function () {
      Route::post('users/data', 'Administrative\Users\UserController@data');
      Route::post('users/export', 'Administrative\Users\UserController@export');
      Route::ApiResource('users', 'Administrative\Users\UserController');

      Route::post('role/data', 'Administrative\Roles\RoleController@data');
      Route::ApiResource('role', 'Administrative\Roles\RoleController');
    });

    //Return view for spa
    Route::get('/{any}', 'ApplicationController@index')->where('any', '.*');
});