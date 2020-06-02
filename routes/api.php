<?php

use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//Route::prefix('training')->middleware(['api'])->group(function () { 
Route::group(['prefix'=>'v1', 'middleware' => 'api'], function () {
    Route::post('login', 'Api\AuthController@login');

    Route::post('location', 'Api\LocationController@levelLocation');

    Route::post('preReports', 'Api\ReportsController@preReport');
    //Route::resource('reports', 'Api\ReportsController');
    Route::post('saveImage', 'Api\ReportsController@saveImage');
    Route::post('incentives', 'Api\ReportsController@getIncentives');

    /*Route::post('changeTermsConditions', 'Api\ConfigurationController@changeStateTermsConditions');
    Route::post('termsConditions', 'Api\ConfigurationController@termsConditions');
    Route::post('getTermsConditions', 'Api\ConfigurationController@getTermsConditions');
    Route::post('statePositions', 'Api\ConfigurationController@statePositions');
    Route::post('stateIncentives', 'Api\ConfigurationController@stateIncentives');
    Route::post('incentives', 'Api\ConfigurationController@getIncentives');
    Route::post('positions', 'Api\ConfigurationController@getPositions');

    Route::post('locations', 'Api\ConfigurationController@listLocations');
    Route::post('areas', 'Api\ConfigurationController@listAreas');
    Route::post('responsibles', 'Api\ConfigurationController@listResponsibles');*/

    Route::post('conditions-info', 'Api\ReportsController@info');

    

    //Route::post('listReporstUser', 'Api\ReportsController@listReportsUser');    

    //Route::post('company', 'Api\logoController@showFile');

    Route::group(['prefix'=>'inspections'], function () {
        //Route::post('qualifications/list', 'Api\InspectionController@qualificationsList');
        Route::post('list', 'Api\InspectionController@lisInspectionsAvailable');
        /*Route::post('create', 'Api\InspectionController@create');
        Route::post('register', 'Api\InspectionController@store');
        Route::post('imageItem', 'Api\InspectionController@imageItem');
        Route::post('quelifiedListUser', 'Api\InspectionController@quelifiedListUser');*/
    });
});
