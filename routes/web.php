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
    Route::post('setStateFilters', 'General\ApplicationController@setStateFilters');
    Route::post('getStateFilters', 'General\ApplicationController@getStateFilters');

    Route::prefix('configurableForm')->group(function () {
      Route::post('formModel', 'General\ConfigurableFormControlle@formModel');
      Route::post('selectOptions', 'General\ConfigurableFormControlle@selectOptions');
    });

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
        
        Route::prefix('reinstatements')->group(function () {
  
          Route::post('restriction/data', 'PreventiveOccupationalMedicine\Reinstatements\RestrictionController@data');
          Route::ApiResource('restriction', 'PreventiveOccupationalMedicine\Reinstatements\RestrictionController');

          Route::get('check/downloadOriginFile/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadOriginFile');
          Route::get('check/downloadPclFile/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadPclFile');
          Route::post('check/data', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@data');
          Route::ApiResource('check', 'PreventiveOccupationalMedicine\Reinstatements\CheckController');

          Route::ApiResource('cie10', 'PreventiveOccupationalMedicine\Reinstatements\Cie10Controller')->only('show');
        });
    });

    Route::prefix('selects')->group(function () {
        Route::post('employees', 'Administrative\Employees\EmployeesController@multiselect');
        Route::post('employeesDeal', 'Administrative\Employees\EmployeesController@multiselectDeal');  
        Route::post('employeesNames', 'Administrative\Employees\EmployeesController@multiselectNames');
        Route::post('employeesIdentifications', 'Administrative\Employees\EmployeesController@multiselectIdentifications');
        Route::post('users', 'Administrative\Users\UserController@multiselect');
        Route::post('responsiblesFilter', 'Administrative\ActionPlans\ActionPlanController@multiselectResponsiblesFilter');  
        Route::post('multiselect', 'General\ApplicationController@multiselect');
        Route::post('roles', 'Administrative\Roles\RoleController@multiselect');
        Route::post('modules', 'General\ApplicationController@multiselectModules');
        Route::post('modulesGroup', 'General\ApplicationController@multiselectGroupModules');
        Route::post('linceseModulesGroup', 'General\ApplicationController@multiselectGroupLicenseModules');
        Route::post('permissions', 'Administrative\Roles\RoleController@multiselectPermissions');
        Route::post('areas', 'Administrative\Areas\EmployeeAreaController@multiselect');  
        Route::post('years/audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectYears');
        Route::post('audiometry/severityGradeLeft', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectSeverityGradeLeft');
        Route::post('audiometry/severityGradeRight', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectSeverityGradeRight');  
        Route::post('regionals', 'Administrative\Regionals\EmployeeRegionalController@multiselect');
        Route::post('headquarters', 'Administrative\Headquarters\EmployeeHeadquarterController@multiselect');  
        Route::post('sexs', 'General\MultiSelectRadioController@sexs');  
        Route::post('processes', 'Administrative\Processes\EmployeeProcessController@multiselect');
        Route::post('positions', 'Administrative\Positions\EmployeePositionController@multiselect');
        Route::post('businesses', 'Administrative\Businesses\EmployeeBusinessController@multiselect');
        Route::post('eps', 'General\ApplicationController@multiselectEps');
        Route::post('afp', 'General\ApplicationController@multiselectAfp');
        Route::post('arl', 'General\ApplicationController@multiselectArl');
        Route::post('cie10', 'PreventiveOccupationalMedicine\Reinstatements\Cie10Controller@multiselect');
        Route::post('restrictions', 'PreventiveOccupationalMedicine\Reinstatements\RestrictionController@multiselect');
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
        Route::post('tagsSubstitution', 'IndustrialSecure\Tags\TagController@multiselectSubstitution');
        Route::post('tagsParticipants', 'IndustrialSecure\Tags\TagController@multiselectParticipants');
        Route::post('actionPlanStates', 'General\MultiSelectRadioController@actionPlanStates');
        Route::post('actionPlanModules', 'Administrative\ActionPlans\ActionPlanController@actionPlanModules');
        Route::post('contractors', 'LegalAspects\Contracs\ContractLesseeController@multiselect');
        Route::post('ctRoles', 'General\MultiSelectRadioController@ctRoles');
        Route::post('ctContractClassifications', 'General\MultiSelectRadioController@ctContractClassifications'); 
        Route::post('ctkindsRisks', 'General\MultiSelectRadioController@ctkindsRisks'); 
        Route::post('siNo', 'General\MultiSelectRadioController@siNoSelect');
        Route::post('companies', 'General\ApplicationController@multiselectCompanies');

        Route::prefix('evaluations')->group(function () {
          Route::post('objectives', 'LegalAspects\Contracs\EvaluationController@multiselectObjectives');
          Route::post('subobjectives', 'LegalAspects\Contracs\EvaluationController@multiselectSubobjectives');
        });

        Route::prefix('contracts')->group(function () {
          Route::post('sectionCategoryItems', 'LegalAspects\Contracs\SectionCategoryItemController@multiselect');
        });

        Route::prefix('legalMatrix')->group(function () {
          Route::post('interests', 'LegalAspects\LegalMatrix\InterestController@multiselect');
          Route::post('interestsCompany', 'LegalAspects\LegalMatrix\InterestController@multiselectCompany');
          Route::post('interestsSystem', 'LegalAspects\LegalMatrix\InterestController@multiselectSystem');
          Route::post('years', 'LegalAspects\LegalMatrix\LawController@lmYears');
          Route::post('responsibles', 'LegalAspects\LegalMatrix\LawController@lmLawResponsibles');
          Route::post('riskAspects', 'LegalAspects\LegalMatrix\RiskAspectController@multiselect');
          Route::post('sstRisks', 'LegalAspects\LegalMatrix\SstRiskController@multiselect');
          Route::post('entities', 'LegalAspects\LegalMatrix\EntityController@multiselect');
          Route::post('lawsTypes', 'LegalAspects\LegalMatrix\LawTypeController@multiselect');
          Route::post('repealed', 'General\MultiSelectRadioController@lmRepealed');
          Route::post('articlesQualifications', 'LegalAspects\LegalMatrix\LawController@articlesQualificationsMultiselect');
          Route::post('filterInterests', 'LegalAspects\LegalMatrix\LawController@filterInterestsMultiselect');
          Route::post('states', 'General\MultiSelectRadioController@lmStates');
          Route::post('systemApply', 'LegalAspects\LegalMatrix\SystemApplyController@multiselect');
          Route::post('systemApplySystem', 'LegalAspects\LegalMatrix\SystemApplyController@multiselectSystem');
          Route::post('systemApplyCompany', 'LegalAspects\LegalMatrix\SystemApplyController@multiselectCompany');
          Route::post('lawNumbers', 'LegalAspects\LegalMatrix\LawController@lmLawNumbers');
          Route::post('lawNumbersSystem', 'LegalAspects\LegalMatrix\LawController@lawNumbersSystem');
          Route::post('lawNumbersCompany', 'LegalAspects\LegalMatrix\LawController@lawNumbersCompany');
          Route::post('lawYears', 'LegalAspects\LegalMatrix\LawController@lmLawYears');
          Route::post('lawYearsSystem', 'LegalAspects\LegalMatrix\LawController@lmLawYearsSystem');
          Route::post('lawYearsCompany', 'LegalAspects\LegalMatrix\LawController@lmLawYearsCompany');
        });

        Route::prefix('system')->group(function () {
          Route::post('labels', 'System\Labels\LabelController@multiselect');
        });
    });

    Route::prefix('radios')->group(function () {
      Route::post('dmTypeActivities', 'General\MultiSelectRadioController@dmTypeActivities');
      Route::post('siNo', 'General\MultiSelectRadioController@siNo');
      Route::post('conf/locationLevelForm', 'Administrative\Configurations\ConfigurationController@radioLocationLevels');
      Route::post('ctTypesEvaluation', 'General\MultiSelectRadioController@ctTypesEvaluation');
      
      Route::prefix('legalMatrix')->group(function () {
        Route::post('interestsSystem', 'LegalAspects\LegalMatrix\InterestController@radioSystem');
      });
    });

    //Administrativo
    Route::prefix('administration')->group(function () {
			Route::post('users/data', 'Administrative\Users\UserController@data');
      Route::post('users/export', 'Administrative\Users\UserController@export');
      Route::post('users/changePassword', 'Administrative\Users\UserController@changePassword');
      Route::get('users/myDefaultModule', 'Administrative\Users\UserController@myDefaultModule');
      Route::post('users/defaultModule', 'Administrative\Users\UserController@defaultModule');
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

      Route::get('logo/download', 'Administrative\Logos\LogoController@download');
      Route::get('logo/view', 'Administrative\Logos\LogoController@show');
      Route::post('logo', 'Administrative\Logos\LogoController@store');

      Route::post('label/data', 'Administrative\Labels\LabelController@data');
      Route::ApiResource('label', 'Administrative\Labels\LabelController'); 
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
      Route::post('dangersMatrix/reportExport ', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportExport');
      Route::get('dangersMatrix/download/{dangersMatrix}', 'IndustrialSecure\DangerMatrix\DangerMatrixController@download');
      Route::ApiResource('dangersMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController');

      Route::post('dangersMatrixHistory/data', 'IndustrialSecure\DangerMatrix\DangerMatrixHistoryController@data');
		});
		
		//Aspectos Legales
		Route::prefix('legalAspects')->group(function () {
      Route::post('contracts/data', 'LegalAspects\Contracs\ContractLesseeController@data');
      Route::get('contracts/getInformation', 'LegalAspects\Contracs\ContractLesseeController@getInformation');
      Route::post('contracts/getListCheckItems', 'LegalAspects\Contracs\ContractLesseeController@getListCheckItems');
      Route::get('contracts/qualifications', 'LegalAspects\Contracs\ContractLesseeController@qualifications');
      Route::post('contracts/saveQualificationItems', 'LegalAspects\Contracs\ContractLesseeController@saveQualificationItems');
      Route::post('contractsListCheckHistory/data', 'LegalAspects\Contracs\ListCheckHistoryController@data');
      Route::post('contracts/retrySendMail/{contract}', 'LegalAspects\Contracs\ContractLesseeController@retrySendMail');
      Route::post('contracts/export', 'LegalAspects\Contracs\ContractLesseeController@export');
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

      Route::prefix('legalMatrix')->group(function () {

        Route::post('interest/data', 'LegalAspects\LegalMatrix\InterestController@data');
        Route::post('interest/saveInterests', 'LegalAspects\LegalMatrix\InterestController@saveInterests');
        Route::get('interest/myInterests', 'LegalAspects\LegalMatrix\InterestController@myInterests');
        Route::ApiResource('interest', 'LegalAspects\LegalMatrix\InterestController');

        Route::post('riskAspect/data', 'LegalAspects\LegalMatrix\RiskAspectController@data');
        Route::ApiResource('riskAspect', 'LegalAspects\LegalMatrix\RiskAspectController');

        Route::post('sstRisk/data', 'LegalAspects\LegalMatrix\SstRiskController@data');
        Route::ApiResource('sstRisk', 'LegalAspects\LegalMatrix\SstRiskController');

        Route::post('entity/data', 'LegalAspects\LegalMatrix\EntityController@data');
        Route::ApiResource('entity', 'LegalAspects\LegalMatrix\EntityController');

        Route::post('systemApply/data', 'LegalAspects\LegalMatrix\SystemApplyController@data');
        Route::ApiResource('systemApply', 'LegalAspects\LegalMatrix\SystemApplyController');

        Route::get('law/downloadArticleQualify/{articleFulfillment}', 'LegalAspects\LegalMatrix\LawController@downloadArticleQualify');
        Route::get('law/download/{law}', 'LegalAspects\LegalMatrix\LawController@download');
        Route::get('law/qualify/{law}', 'LegalAspects\LegalMatrix\LawController@getArticlesQualification');
        Route::post('law/saveArticlesQualification', 'LegalAspects\LegalMatrix\LawController@saveArticlesQualification');
        Route::post('law/data', 'LegalAspects\LegalMatrix\LawController@data');
        Route::post('law/report', 'LegalAspects\LegalMatrix\LawReportController@data');
        Route::post('law/report/export', 'LegalAspects\LegalMatrix\LawReportController@export');
        Route::ApiResource('law', 'LegalAspects\LegalMatrix\LawController');

        Route::post('articleHistory/data', 'LegalAspects\LegalMatrix\ArticleHistoryController@data');
        Route::post('articleFulfillmentHistory/data', 'LegalAspects\LegalMatrix\ArticleFulfillmentHistoryController@data');
      });
    });
    
    //Sistema
    Route::prefix('system')->group(function () {

      Route::post('license/history/data', 'System\Licenses\LicenseHistoryController@data');
      Route::post('license/data', 'System\Licenses\LicenseController@data');
      Route::ApiResource('license', 'System\Licenses\LicenseController');

      Route::post('logMail/data', 'System\LogMails\LogMailController@data');
      Route::ApiResource('logMail', 'System\LogMails\LogMailController');

      Route::post('label/data', 'System\Labels\LabelController@data');
      Route::ApiResource('label', 'System\Labels\LabelController');   
    });


    //Return view for spa
    Route::get('/{any}', 'General\ApplicationController@index')->where('any', '.*');
});