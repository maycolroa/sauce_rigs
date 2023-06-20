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

    Route::post('changeTermsConditions', 'Api\ConfigurationController@changeStateTermsConditions');
    Route::post('termsConditions', 'Api\ConfigurationController@termsConditions');
    Route::post('getTermsConditions', 'Api\ConfigurationController@getTermsConditions');
    Route::post('usersActionPlan', 'Api\ConfigurationController@getUsersActionPlan');
    /*Route::post('statePositions', 'Api\ConfigurationController@statePositions');
    Route::post('stateIncentives', 'Api\ConfigurationController@stateIncentives');
    Route::post('incentives', 'Api\ConfigurationController@getIncentives');
    Route::post('positions', 'Api\ConfigurationController@getPositions');

    Route::post('locations', 'Api\ConfigurationController@listLocations');
    Route::post('areas', 'Api\ConfigurationController@listAreas');
    Route::post('responsibles', 'Api\ConfigurationController@listResponsibles');*/

    Route::post('conditions-info', 'Api\ReportsController@info');

    Route::post('imageApi', 'Api\ConfigurationController@saveImageApi');

    

    //Route::post('listReporstUser', 'Api\ReportsController@listReportsUser');    

    //Route::post('company', 'Api\logoController@showFile');

    Route::group(['prefix'=>'inspections'], function () {
        Route::post('qualifications/list', 'Api\InspectionController@qualificationsList');
        Route::post('list', 'Api\InspectionController@lisInspectionsAvailable');
        Route::post('create', 'Api\InspectionController@create');
        Route::post('register', 'Api\InspectionController@store');
        Route::post('imageItem', 'Api\InspectionController@imageItem');
        Route::post('quelifiedListUser', 'Api\InspectionController@quelifiedListUser');
        Route::post('optionsMasive', 'Api\InspectionController@optionsMasiveQualification');
        Route::post('getPlanActionMandatory', 'Api\InspectionController@getPlanActionMandatory');
        Route::post('getlevelRiskMandatory', 'Api\InspectionController@getLevelRiskMandatory');
    });


    Route::group(['prefix'=>'epp'], function () {
        Route::post('moduleEpp', 'Api\EppController@getModuleEpp');
        Route::post('location', 'Api\EppController@getLocation');
        Route::post('employee', 'Api\EppController@getEmployees');
        Route::post('elements', 'Api\EppController@getElementsLocation');
        Route::post('elementsQuantity', 'Api\EppController@getElementsQuantity');
        Route::post('createDelivery', 'Api\EppController@saveDelivery');
        Route::post('deliveryEmployee', 'Api\EppController@getDeliveryEmployee');
        Route::post('createReturns', 'Api\EppController@storeReturns');
        /*Route::post('imageItem', 'Api\InspectionController@imageItem');
        Route::post('quelifiedListUser', 'Api\InspectionController@quelifiedListUser');
        Route::post('optionsMasive', 'Api\InspectionController@optionsMasiveQualification');*/
    });

    Route::group(['prefix'=>'accidents'], function () {
        Route::post('employee', 'Api\AccidentsController@getEmployees');
        Route::post('positions', 'Api\AccidentsController@getPositions');
        Route::post('departaments', 'Api\AccidentsController@getDepartaments');
        Route::post('dataAccidents', 'Api\AccidentsController@dataAccidents');
        Route::post('create', 'Api\AccidentsController@createAccident');
        /* Route::post('quelifiedListUser', 'Api\InspectionController@quelifiedListUser');
        Route::post('optionsMasive', 'Api\InspectionController@optionsMasiveQualification');*/
    });
});

Route::post('amazon-sns/notifications', 'Api\AmazonController@handleBounceOrComplaint');
