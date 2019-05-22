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

//Generar contraseña
Route::get('/password/generate/{token}', 'Auth\GeneratePasswordController@generatePassword');
Route::post('/password/generate/{id}', 'Auth\GeneratePasswordController@updatePassword');

Route::middleware(['auth'])->group(function () { 
    Route::get('appWithModules', 'General\ApplicationController@appsWhithModules');
    Route::get('getCompanies', 'General\ApplicationController@getCompanies');
    Route::post('changeCompany', 'General\ApplicationController@changeCompany');
    Route::post('vuetableCustomColumns', 'General\ApplicationController@vuetableCustomColumns');

    Route::get('templates/audiometryimport','PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@downloadTemplateImport');

	//Cerrar sesión 
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
        Route::post('employees', 'Administrative\Employees\EmployeesController@multiselect');  
        Route::post('users', 'Administrative\Users\UserController@multiselect');
        Route::post('usersAll', 'Administrative\Users\UserController@multiselectAll');  
        Route::post('multiselect', 'General\ApplicationController@multiselect');
        Route::post('roles', 'Administrative\Roles\RoleController@multiselect');
        Route::post('modulesGroup', 'General\ApplicationController@multiselectGroupModules');
        Route::post('permissions', 'Administrative\Roles\RoleController@multiselectPermissions');
        Route::post('areas', 'Administrative\Areas\EmployeeAreaController@multiselect');  
        Route::post('years/audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectYears');  
        Route::post('regionals', 'Administrative\Regionals\EmployeeRegionalController@multiselect');
        Route::post('headquarters', 'Administrative\Headquarters\EmployeeHeadquarterController@multiselect');  
        Route::post('sexs', 'General\MultiSelectRadioController@sexs');  
        Route::post('processes', 'Administrative\Processes\EmployeeProcessController@multiselect');
        Route::post('positions', 'Administrative\Positions\EmployeePositionController@multiselect');
        Route::post('businesses', 'Administrative\Businesses\EmployeeBusinessController@multiselect');
        Route::post('eps', 'General\ApplicationController@multiselectEps');
        Route::post('multiselectBar', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@multiselectBar');
        Route::post('multiselectBarPercentage', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@multiselectBarPercentage');
        Route::post('dmActivities', 'IndustrialSecure\Activities\ActivityController@multiselect');
        Route::post('dmDangers', 'IndustrialSecure\Dangers\DangerController@multiselect');
        Route::post('dmDangerMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController@multiselect');
        Route::post('dmGeneratedDangers', 'General\MultiSelectRadioController@dmGeneratedDangers');
        Route::post('tagsAdministrativeControls', 'IndustrialSecure\Tags\TagController@multiselectAdministrativeControls');
        Route::post('tagsEngineeringControls', 'IndustrialSecure\Tags\TagController@multiselectEngineeringControls');
        Route::post('tagsEpp', 'IndustrialSecure\Tags\TagController@multiselectEpp');
        Route::post('tagsPossibleConsequencesDanger', 'IndustrialSecure\Tags\TagController@multiselectPossibleConsequencesDanger');
        Route::post('tagsWarningSignage', 'IndustrialSecure\Tags\TagController@multiselectWarningSignage');
        Route::post('tagsTypeProcess', 'General\TagController@multiselectTypeProcess');
        Route::post('actionPlanStates', 'General\MultiSelectRadioController@actionPlanStates');
        Route::post('actionPlanModules', 'Administrative\ActionPlans\ActionPlanController@actionPlanModules');
        Route::post('contractors', 'LegalAspects\Contracs\ContractLesseeController@multiselect');
        Route::post('ctRoles', 'General\MultiSelectRadioController@ctRoles');
        Route::post('ctContractClassifications', 'General\MultiSelectRadioController@ctContractClassifications'); 
        Route::post('ctkindsRisks', 'General\MultiSelectRadioController@ctkindsRisks'); 

        Route::prefix('evaluations')->group(function () {
          Route::post('objectives', 'LegalAspects\Contracs\EvaluationController@multiselectObjectives');
          Route::post('subobjectives', 'LegalAspects\Contracs\EvaluationController@multiselectSubobjectives');
        });

        Route::prefix('contracts')->group(function () {
          Route::post('sectionCategoryItems', 'LegalAspects\Contracs\SectionCategoryItemController@multiselect');
        });
    });

    Route::prefix('radios')->group(function () {
      Route::post('dmTypeActivities', 'General\MultiSelectRadioController@dmTypeActivities');
      Route::post('siNo', 'General\MultiSelectRadioController@siNo');
      Route::post('conf/locationLevelForm', 'Administrative\Configurations\ConfigurationController@radioLocationLevels');
      Route::post('ctTypesEvaluation', 'General\MultiSelectRadioController@ctTypesEvaluation');
    });

    //Administrativo
    Route::prefix('administration')->group(function () {
			Route::post('users/data', 'Administrative\Users\UserController@data');
			Route::post('users/export', 'Administrative\Users\UserController@export');
			Route::ApiResource('users', 'Administrative\Users\UserController');

			Route::post('role/data', 'Administrative\Roles\RoleController@data');
			Route::ApiResource('role', 'Administrative\Roles\RoleController');

			Route::post('position/data', 'Administrative\Positions\EmployeePositionController@data');
			Route::ApiResource('position', 'Administrative\Positions\EmployeePositionController');

			Route::post('regional/data', 'Administrative\Regionals\EmployeeRegionalController@data');
			Route::ApiResource('regional', 'Administrative\Regionals\EmployeeRegionalController');

			Route::post('business/data', 'Administrative\Businesses\EmployeeBusinessController@data');
			Route::ApiResource('business', 'Administrative\Businesses\EmployeeBusinessController');

			Route::post('headquarter/data', 'Administrative\Headquarters\EmployeeHeadquarterController@data');
			Route::ApiResource('headquarter', 'Administrative\Headquarters\EmployeeHeadquarterController');

			Route::post('area/data', 'Administrative\Areas\EmployeeAreaController@data');
			Route::ApiResource('area', 'Administrative\Areas\EmployeeAreaController');

			Route::post('process/data', 'Administrative\Processes\EmployeeProcessController@data');
			Route::ApiResource('process', 'Administrative\Processes\EmployeeProcessController');

			Route::post('employee/data', 'Administrative\Employees\EmployeesController@data');
			Route::ApiResource('employee', 'Administrative\Employees\EmployeesController');

      Route::post('configuration', 'Administrative\Configurations\ConfigurationController@store');
      Route::get('configuration/view', 'Administrative\Configurations\ConfigurationController@show');
					

      Route::prefix('configurations')->group(function () {
        Route::post('locationLevelForms/getConfModule', 'Administrative\Configurations\LocationLevelFormController@getConfModule');
				Route::post('locationLevelForms/data', 'Administrative\Configurations\LocationLevelFormController@data');
				Route::ApiResource('locationLevelForms', 'Administrative\Configurations\LocationLevelFormController');
      });

      Route::post('actionplan/data', 'Administrative\ActionPlans\ActionPlanController@data');
      Route::post('actionplan/export', 'Administrative\ActionPlans\ActionPlanController@export');
      Route::ApiResource('actionplan', 'Administrative\ActionPlans\ActionPlanController');
    });

    //Seguridad Industrial
    Route::prefix('industrialSecurity')->group(function () {
      Route::post('activity/data', 'IndustrialSecure\Activities\ActivityController@data');
      Route::ApiResource('activity', 'IndustrialSecure\Activities\ActivityController');

      Route::post('danger/data', 'IndustrialSecure\Dangers\DangerController@data');
      Route::ApiResource('danger', 'IndustrialSecure\Dangers\DangerController');

      Route::post('dangersMatrix/data', 'IndustrialSecure\DangerMatrix\DangerMatrixController@data');
      Route::post('dangersMatrix/getQualificationsComponent', 'IndustrialSecure\DangerMatrix\QualificationController@getQualificationsComponent');
      Route::post('dangersMatrix/report', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@report');
      Route::post('dangersMatrix/reportDangerTable', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportDangerTable');
      Route::ApiResource('dangersMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController');

      Route::post('dangersMatrixHistory/data', 'IndustrialSecure\DangerMatrix\DangerMatrixHistoryController@data');
		});
		
		//Aspectos Legales
		Route::prefix('legalAspects')->group(function () {
			/*Route::get('contracts/qualifications', 'LegalAspects\Contracs\ContractLesseeController@qualifications');
			Route::post('contracts/validateActionPlanItem', 'LegalAspects\Contracs\ContractLesseeController@validateActionPlanItem');
			Route::post('contracts/validateFilesItem', 'LegalAspects\Contracs\ContractLesseeController@validateFilesItem');
			Route::get('contracts/data', 'LegalAspects\Contracs\ContractLesseeController@data');
      Route::post('contracts/saveQualificationItems', 'LegalAspects\Contracs\ContractLesseeController@saveQualificationItems');
      
      Route::ApiResource('contracts', 'LegalAspects\Contracs\ContractLesseeController');
      Route::ApiResource('contract', 'LegalAspects\Contracs\ContractLesseeController');*/

      Route::post('contracts/data', 'LegalAspects\Contracs\ContractLesseeController@data');
      Route::get('contracts/getInformation', 'LegalAspects\Contracs\ContractLesseeController@getInformation');
      Route::post('contracts/getListCheckItems', 'LegalAspects\Contracs\ContractLesseeController@getListCheckItems');
      Route::get('contracts/qualifications', 'LegalAspects\Contracs\ContractLesseeController@qualifications');
      Route::post('contracts/saveQualificationItems', 'LegalAspects\Contracs\ContractLesseeController@saveQualificationItems');
      Route::post('contractsListCheckHistory/data', 'LegalAspects\Contracs\ListCheckHistoryController@data');
      Route::ApiResource('contracts', 'LegalAspects\Contracs\ContractLesseeController');

      Route::post('fileUpload/data', 'LegalAspects\Contracs\FileUploadController@data');
      Route::get('fileUpload/download/{fileUpload}', 'LegalAspects\Contracs\FileUploadController@download');
      Route::post('fileUpload/getFilesItem', 'LegalAspects\Contracs\FileUploadController@getFilesItem');
      Route::ApiResource('fileUpload', 'LegalAspects\Contracs\FileUploadController');

      Route::post('typeRating/data', 'LegalAspects\Contracs\TypeRatingController@data');
      Route::post('typeRating/AllTypesRating', 'LegalAspects\Contracs\TypeRatingController@getAllTypesRating');
      Route::ApiResource('typeRating', 'LegalAspects\Contracs\TypeRatingController');

      Route::post('evaluation/data', 'LegalAspects\Contracs\EvaluationController@data');
      Route::post('evaluation/export', 'LegalAspects\Contracs\EvaluationController@export');
      Route::ApiResource('evaluation', 'LegalAspects\Contracs\EvaluationController');

      Route::post('evaluationContract/data', 'LegalAspects\Contracs\EvaluationContractController@data');
      Route::get('evaluationContract/getData/{evaluationContract}', 'LegalAspects\Contracs\EvaluationContractController@getData');
      Route::post('evaluationContract/report', 'LegalAspects\Contracs\EvaluationContractController@report');
      Route::post('evaluationContract/exportReport', 'LegalAspects\Contracs\EvaluationContractController@exportReport');
      Route::post('evaluationContract/getTotales', 'LegalAspects\Contracs\EvaluationContractController@getTotales');
      Route::ApiResource('evaluationContract', 'LegalAspects\Contracs\EvaluationContractController');

      Route::post('evaluationContractHistory/data', 'LegalAspects\Contracs\EvaluationContractHistoryController@data');
		});


    //Return view for spa
    Route::get('/{any}', 'General\ApplicationController@index')->where('any', '.*');
});