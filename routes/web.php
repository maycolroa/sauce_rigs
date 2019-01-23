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
      //Route::post('audiometry/reportPta', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@reportPta');
      Route::ApiResource('audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController');   
      Route::post('audiometry/informs', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@data');
      Route::post('audiometry/informs/individual', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@dataIndividual');
    });

    Route::prefix('selects')->group(function () {
        Route::post('employees', 'Administrative\EmployeesController@multiselect');  
        Route::post('multiselect', 'ApplicationController@multiselect');
        Route::post('roles', 'Administrative\Roles\RoleController@multiselect');  
        Route::post('modulesGroup', 'ApplicationController@multiselectGroupModules');
        Route::post('permissions', 'Administrative\Roles\RoleController@multiselectPermissions');
        Route::post('areas', 'Administrative\EmployeeAreaController@multiselect');  
        Route::post('years/audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectYears');  
        Route::post('regionals', 'Administrative\EmployeeRegionalController@multiselect');
        Route::post('headquarters', 'Administrative\EmployeeHeadquarterController@multiselect');  
        Route::post('sexs', 'ApplicationController@multiselectSexs');  
        Route::post('processes', 'Administrative\EmployeeProcessController@multiselect');
        Route::post('positions', 'Administrative\EmployeePositionController@multiselect');
        Route::post('businesses', 'Administrative\EmployeeBusinessController@multiselect');
        Route::post('eps', 'ApplicationController@multiselectEps');
        Route::post('multiselectBar', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@multiselectBar');
        Route::post('dmActivities', 'IndustrialSecure\ActivityController@multiselect');
        Route::post('dmDangers', 'IndustrialSecure\DangerController@multiselect');
        Route::post('dmGeneratedDangers', 'IndustrialSecure\DangerController@multiselectGeneratedDangers');
        Route::post('tagsAdministrativeControls', 'IndustrialSecure\TagController@multiselectAdministrativeControls');
        Route::post('tagsEngineeringControls', 'IndustrialSecure\TagController@multiselectEngineeringControls');
        Route::post('tagsEpp', 'IndustrialSecure\TagController@multiselectEpp');
        Route::post('tagsPossibleConsequencesDanger', 'IndustrialSecure\TagController@multiselectPossibleConsequencesDanger');
        Route::post('tagsWarningSignage', 'IndustrialSecure\TagController@multiselectWarningSignage');
        Route::post('conf/locationLevelFormModules', 'Administrative\Configurations\LocationLevelFormController@multiselectModules');
        Route::post('actionPlanStates', 'MultiSelectController@actionPlanStates');
      });

    Route::prefix('radios')->group(function () {
      Route::post('dmTypeActivities', 'IndustrialSecure\ActivityController@radioTypeActivities');
      Route::post('siNo', 'ApplicationController@radioSiNo');
      Route::post('conf/locationLevelForm', 'Administrative\Configurations\LocationLevelFormController@radioLocationLevels');
    });

    //Administrativo
    Route::prefix('administration')->group(function () {
      Route::post('users/data', 'Administrative\Users\UserController@data');
      Route::post('users/export', 'Administrative\Users\UserController@export');
      Route::ApiResource('users', 'Administrative\Users\UserController');

      Route::post('role/data', 'Administrative\Roles\RoleController@data');
      Route::ApiResource('role', 'Administrative\Roles\RoleController');

      Route::post('position/data', 'Administrative\EmployeePositionController@data');
      Route::ApiResource('position', 'Administrative\EmployeePositionController');

      Route::post('regional/data', 'Administrative\EmployeeRegionalController@data');
      Route::ApiResource('regional', 'Administrative\EmployeeRegionalController');

      Route::post('business/data', 'Administrative\EmployeeBusinessController@data');
      Route::ApiResource('business', 'Administrative\EmployeeBusinessController');

      Route::post('headquarter/data', 'Administrative\EmployeeHeadquarterController@data');
      Route::ApiResource('headquarter', 'Administrative\EmployeeHeadquarterController');

      Route::post('area/data', 'Administrative\EmployeeAreaController@data');
      Route::ApiResource('area', 'Administrative\EmployeeAreaController');

      Route::post('process/data', 'Administrative\EmployeeProcessController@data');
      Route::ApiResource('process', 'Administrative\EmployeeProcessController');

      Route::post('employee/data', 'Administrative\EmployeesController@data');
      Route::ApiResource('employee', 'Administrative\EmployeesController');

      Route::prefix('configurations')->group(function () {
        Route::post('locationLevelForms/data', 'Administrative\Configurations\LocationLevelFormController@data');
        Route::ApiResource('locationLevelForms', 'Administrative\Configurations\LocationLevelFormController');
        Route::post('locationLevelForms/getConfModule', 'Administrative\Configurations\LocationLevelFormController@getConfModule');

        Route::prefix('industrialSecurity')->group(function () {
          Route::prefix('dangersMatrix')->group(function () {
            Route::post('getQualificationsComponent', 'Administrative\Configurations\IndustrialSecure\DangerMatrix\QualificationController@getQualificationsComponent');
          });
        });
      });
    });

    //Seguridad Industrial
    Route::prefix('industrialSecurity')->group(function () {
      Route::post('activity/data', 'IndustrialSecure\ActivityController@data');
      Route::ApiResource('activity', 'IndustrialSecure\ActivityController');

      Route::post('danger/data', 'IndustrialSecure\DangerController@data');
      Route::ApiResource('danger', 'IndustrialSecure\DangerController');

      Route::post('dangersMatrix/data', 'IndustrialSecure\DangerMatrixController@data');
      Route::ApiResource('dangersMatrix', 'IndustrialSecure\DangerMatrixController');
    });

    //Return view for spa
    Route::get('/{any}', 'ApplicationController@index')->where('any', '.*');
});