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
Route::get('/accidentWork/causesExport/{id}', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@prueba')->name('diagrama.foto');

Route::prefix('training')->group(function () {
  Route::get('{training}/{token}', 'LegalAspects\Contracs\TrainingEmployeeController@index');
  Route::post('response', 'LegalAspects\Contracs\TrainingEmployeeController@saveTraining');
  Route::get('download/file/{id}', 'LegalAspects\Contracs\TrainingEmployeeController@download');
});

Route::prefix('epp')->group(function () {
  Route::get('{transaction}/{employee}', 'IndustrialSecure\EPP\TransactionFirmController@index');
  Route::post('firm', 'IndustrialSecure\EPP\TransactionFirmController@saveFirm');
});

Route::middleware(['auth'])->group(function () { 
    Route::get('appWithModules', 'General\ApplicationController@appsWhithModules');
    Route::get('getCompanies', 'General\ApplicationController@getCompanies');
    Route::post('changeCompany', 'General\ApplicationController@changeCompany');
    Route::post('vuetableCustomColumns', 'General\ApplicationController@vuetableCustomColumns');
    Route::post('setStateFilters', 'General\ApplicationController@setStateFilters');
    Route::post('setStatePageVuetable', 'General\ApplicationController@setStatePageVuetable');
    Route::post('getStateFilters', 'General\ApplicationController@getStateFilters');    
    Route::post('getPageVuetable', 'General\ApplicationController@getPageVuetable');    
    Route::post('userActivity', 'General\ApplicationController@userActivity');
    Route::get('get_terms_conditions', 'General\ApplicationController@getTermsConditionsUsers');
    Route::post('accept_terms_conditions', 'General\ApplicationController@accepTermsConditionsUsers');

    Route::prefix('configurableForm')->group(function () {
      Route::post('formModel', 'General\ConfigurableFormControlle@formModel');
      Route::post('selectOptions', 'General\ConfigurableFormControlle@selectOptions');
    });

    Route::get('templates/audiometryimport','PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@downloadTemplateImport');
    Route::get('templates/employeeimport','Administrative\Employees\EmployeesController@downloadTemplateImport');    
    Route::get('templates/employeeinactiveimport','Administrative\Employees\EmployeesController@downloadTemplateInactiveImport');   
    Route::get('templates/dangermatriximport','IndustrialSecure\DangerMatrix\DangerMatrixController@downloadTemplateImport');
    Route::get('templates/contractimport','LegalAspects\Contracs\ContractLesseeController@downloadTemplateImport');
    Route::get('templates/legalmatriximport','LegalAspects\LegalMatrix\LawController@downloadTemplateImport');  
    Route::get('templates/usersimport','Administrative\Users\UserController@downloadTemplateImport');      
    Route::get('templates/contractemployeeimport','LegalAspects\Contracs\ContractEmployeeController@downloadTemplateImport');  
    Route::get('templates/inspectionsimport','IndustrialSecure\DangerousConditions\Inspections\InspectionController@downloadTemplateImport');
    Route::get('templates/riskmatriximport','IndustrialSecure\RiskMatrix\RiskMatrixController@downloadTemplateImport');
    Route::get('templates/musculoskeletalimport','PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@downloadTemplateImport');
    Route::get('templates/respiratoryAnalysisimport','PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@downloadTemplateImport');
    Route::get('templates/elementimport','IndustrialSecure\EPP\ElementController@downloadTemplateImport');
    Route::get('templates/locationimport','IndustrialSecure\EPP\LocationController@downloadTemplateImport');

    Route::get('templates/positionimport','Administrative\Positions\EmployeePositionController@downloadTemplateImport');
    Route::get('templates/elementnotidentimport','IndustrialSecure\EPP\ElementController@elementNotIdentImport');
    Route::get('templates/elementidentimport','IndustrialSecure\EPP\ElementController@elementIdentImport');
    Route::get('templates/regionalsimport','Administrative\Regionals\EmployeeRegionalController@downloadTemplateImport');

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

        Route::prefix('audiometry')->group(function () {
          Route::post('evaluation/dataAudiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationController@dataAudiometry');
          Route::ApiResource('evaluation', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationController');          
          Route::post('evaluation/block', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationController@inEdit');

          Route::ApiResource('evaluationPerform', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerformController');

          Route::post('evaluationPerform/data', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerformController@data');
          Route::get('getData/{evaluation}', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerformController@getData');
          Route::get('evaluationPerform/downloadFile/{evaluationFile}', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerformController@downloadFile');
          Route::get('evaluationPerform/downloadPdf/{evaluationPerform}', 'PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerformController@downloadPdf');
        });

        
        Route::prefix('reinstatements')->group(function () {
  
          Route::post('restriction/data', 'PreventiveOccupationalMedicine\Reinstatements\RestrictionController@data');
          Route::ApiResource('restriction', 'PreventiveOccupationalMedicine\Reinstatements\RestrictionController');
          Route::post('getMessageIncapacitate', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@getMessageIncapacitate');

          Route::post('saveLaborRelationsNotes', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@saveLaborRelationsNotes');
          Route::post('check/sendEmailRecommendations', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@sendEmailRecommendations');
          Route::post('check/export', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@export');
          Route::get('check/generateLetter', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@generateLetter')->name('checks.generateLetter');
          Route::get('check/generateLetterTracingGlobal', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@generateTracingGlobal')->name('checks.generateLetterTracingGlobal');
          Route::get('check/generateTracing', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@generateTracing')->name('checks.generateTracing');
          Route::get('check/downloadOriginFile/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadOriginFile');
          Route::get('check/downloadPclFile/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadPclFile');
          Route::post('check/data', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@data');
          Route::ApiResource('check', 'PreventiveOccupationalMedicine\Reinstatements\CheckController');
          Route::post('check/informs', 'PreventiveOccupationalMedicine\Reinstatements\CheckInformController@data');
          Route::put('check/switchStatus/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@toggleState');
          Route::post('check/tracingOthers', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@tracingOthers');
          Route::get('check/downloadFile/{file}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadFile');

          Route::post('check/data2', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@dataLetters');

          Route::post('check/oldReport', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@data2');

          Route::get('check/generatePdf/{id}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@regenerateLetter');

          Route::ApiResource('cie10', 'PreventiveOccupationalMedicine\Reinstatements\Cie10Controller')->only('show');

          Route::post('diseaseOrigin/data', 'PreventiveOccupationalMedicine\Reinstatements\DiseaseOriginController@data');
          Route::ApiResource('diseaseOrigin', 'PreventiveOccupationalMedicine\Reinstatements\DiseaseOriginController');

          Route::post('originAdvisor/data', 'PreventiveOccupationalMedicine\Reinstatements\OriginAdvisorController@data');
          Route::ApiResource('originAdvisor', 'PreventiveOccupationalMedicine\Reinstatements\OriginAdvisorController');
          
          Route::post('laborConclusion/data', 'PreventiveOccupationalMedicine\Reinstatements\LaborConclusionController@data');
          Route::ApiResource('laborConclusion', 'PreventiveOccupationalMedicine\Reinstatements\LaborConclusionController');

          Route::post('medicalConclusion/data', 'PreventiveOccupationalMedicine\Reinstatements\MedicalConclusionController@data');
          Route::ApiResource('medicalConclusion', 'PreventiveOccupationalMedicine\Reinstatements\MedicalConclusionController');
          Route::get('check/downloadPdf/{id}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadPdf');

          Route::post('configuration', 'PreventiveOccupationalMedicine\Reinstatements\ConfigurationController@store');
          Route::get('configuration/view', 'PreventiveOccupationalMedicine\Reinstatements\ConfigurationController@show');
        });
        
        Route::post('musculoskeletalAnalysis/reportIndividual', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisInformController@dataIndividual');
        Route::post('musculoskeletalAnalysis/informs', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisInformController@data');
        Route::post('musculoskeletalAnalysis/saveTracing', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@saveTracing');
        Route::post('musculoskeletalAnalysis/import', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@import');
        Route::post('musculoskeletalAnalysis/export', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@export');
        Route::post('musculoskeletalAnalysis/data', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@data');
        Route::ApiResource('musculoskeletalAnalysis', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController'); 

        Route::post('respiratoryAnalysis/reportIndividual', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisInformController@dataIndividual');
        Route::post('respiratoryAnalysis/informs', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisInformController@data');
        Route::post('respiratoryAnalysis/saveTracing', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@saveTracing');
        Route::post('respiratoryAnalysis/import', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@import');
        Route::post('respiratoryAnalysis/export', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@export');
        Route::post('respiratoryAnalysis/data', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@data');
        Route::ApiResource('respiratoryAnalysis', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController');  

        Route::prefix('absenteeism')->group(function () {
          Route::post('report/data', 'PreventiveOccupationalMedicine\Absenteeism\ReportController@data');
          Route::post('report/monitorView/{id}', 'PreventiveOccupationalMedicine\Absenteeism\ReportController@monitorViews');
          Route::ApiResource('report', 'PreventiveOccupationalMedicine\Absenteeism\ReportController');
          
          Route::get('fileUpload/download/{fileUpload}', 'PreventiveOccupationalMedicine\Absenteeism\FileUploadController@download');
          Route::post('fileUpload/data', 'PreventiveOccupationalMedicine\Absenteeism\FileUploadController@data');
          Route::ApiResource('fileUpload', 'PreventiveOccupationalMedicine\Absenteeism\FileUploadController');

          Route::post('talendUpload/talendExist', 'PreventiveOccupationalMedicine\Absenteeism\TalendController@talendExist');
          Route::post('talendUpload/switchStatus/{talendUpload}', 'PreventiveOccupationalMedicine\Absenteeism\TalendController@toggleState');
          Route::get('talendUpload/download/{talendUpload}', 'PreventiveOccupationalMedicine\Absenteeism\TalendController@download');
          Route::post('talendUpload/data', 'PreventiveOccupationalMedicine\Absenteeism\TalendController@data');
          Route::ApiResource('talendUpload', 'PreventiveOccupationalMedicine\Absenteeism\TalendController');

          Route::post('configuration', 'PreventiveOccupationalMedicine\Absenteeism\ConfigurationController@store');
          Route::get('configuration/view', 'PreventiveOccupationalMedicine\Absenteeism\ConfigurationController@show');
        });

        Route::get('document/download/{document}', 'PreventiveOccupationalMedicine\Documents\DocumentController@download');
        Route::post('document/data', 'PreventiveOccupationalMedicine\Documents\DocumentController@data');
        Route::ApiResource('document', 'PreventiveOccupationalMedicine\Documents\DocumentController');
    });
    
    Route::prefix('selects')->group(function () {
        Route::post('employees', 'Administrative\Employees\EmployeesController@multiselect');
        Route::post('employeesId', 'Administrative\Employees\EmployeesController@multiselectEmployee');
        Route::post('employeesDeal', 'Administrative\Employees\EmployeesController@multiselectDeal');  
        Route::post('employeesNames', 'Administrative\Employees\EmployeesController@multiselectNames');
        Route::post('employeesIdentifications', 'Administrative\Employees\EmployeesController@multiselectIdentifications');
        Route::post('users', 'Administrative\Users\UserController@multiselect');
        Route::post('usersActivitiesModule', 'Administrative\Users\UserController@multiselectModuleActivity');
        Route::post('usersActionPlan', 'Administrative\Users\UserController@multiselectUsersActionPlan');
        Route::post('usersActionPlanContract', 'Administrative\Users\UserController@multiselectUsersActionPlanContract');
        Route::post('usersOtherCompany', 'Administrative\Users\UserController@multiselectUsers');
        Route::post('usersAutomaticSend', 'Administrative\Users\UserController@multiselectUsersAutomaticSend');
        Route::post('responsiblesFilter', 'Administrative\ActionPlans\ActionPlanController@multiselectResponsiblesFilter');  
        Route::post('multiselect', 'General\ApplicationController@multiselect');
        Route::post('roles', 'Administrative\Roles\RoleController@multiselect');
        Route::post('centers', 'System\Companies\CompanyController@multiselectCenters');
        Route::post('rolesDefined', 'Administrative\Roles\RoleController@multiselectDefined');
        Route::post('modules', 'General\ApplicationController@multiselectModules');
        Route::post('modulesGroup', 'General\ApplicationController@multiselectGroupModules');
        Route::post('linceseModulesGroup', 'General\ApplicationController@multiselectGroupLicenseModules');
        Route::post('permissions', 'Administrative\Roles\RoleController@multiselectPermissions');
        Route::post('permissionsAlls', 'Administrative\Roles\RoleController@permissionsMultiselect');
        Route::post('areas', 'Administrative\Areas\EmployeeAreaController@multiselect');  
        Route::post('years/audiometry', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectYears');
        Route::post('audiometry/severityGradeLeft', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectSeverityGradeLeft');
        Route::post('audiometry/severityGradeRight', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@multiselectSeverityGradeRight');  
        Route::post('consolidatedPersonalRiskCriterion', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@multiselectConsolidatedPersonalRiskCriterion');
        Route::post('branchOffice', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@multiselectBranchOffice');
        Route::post('respiratory/regional', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@multiselectRegional');
        Route::post('respiratory/deal', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@multiselectDeal');
        Route::post('respiratory/interpretation', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@multiselectInterpretation');
        Route::post('bm_musculoskeletalCompany', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@multiselectCompany');
        Route::post('bm_musculoskeletalPacient', 'PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisController@multiselectPacient');
        Route::post('bm_respiratoryPacient', 'PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisController@multiselectPacient');
        Route::post('departaments', 'General\MultiSelectRadioController@departamentsMultiselect');
        Route::post('municipalities', 'General\MultiSelectRadioController@municipalitiesMultiselect');
        Route::post('regionals', 'Administrative\Regionals\EmployeeRegionalController@multiselect');
        Route::post('headquarters', 'Administrative\Headquarters\EmployeeHeadquarterController@multiselect');  
        Route::post('sexs', 'General\MultiSelectRadioController@sexs');    
        Route::post('typesDocument', 'General\MultiSelectRadioController@typesDocumentContract');     
        Route::post('levelRisk', 'General\MultiSelectRadioController@levelRiskInspections');     
        Route::post('days', 'General\MultiSelectRadioController@days');  
        Route::post('processes', 'Administrative\Processes\EmployeeProcessController@multiselect');
        Route::post('macroprocess', 'IndustrialSecure\RiskMatrix\MacroprocessController@multiselect');
        Route::post('positions', 'Administrative\Positions\EmployeePositionController@multiselect');
        Route::post('businesses', 'Administrative\Businesses\EmployeeBusinessController@multiselect');
        Route::post('eps', 'General\ApplicationController@multiselectEps');
        Route::post('afp', 'General\ApplicationController@multiselectAfp');
        Route::post('arl', 'General\ApplicationController@multiselectArl');
        Route::post('cie10', 'PreventiveOccupationalMedicine\Reinstatements\Cie10Controller@multiselect');
        Route::post('restrictions', 'PreventiveOccupationalMedicine\Reinstatements\RestrictionController@multiselect');
        Route::post('diseaseOrigin', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectDiseaseOrigin');
        Route::post('nextFollowDays', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectNextFollowDays');
        Route::post('multiselectBar', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@multiselectBar');
        Route::post('multiselectBarEvaluations', 'LegalAspects\Contracs\EvaluationContractController@multiselectBar');
        Route::post('multiselectBarInspection', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@multiselectBar');
        Route::post('multiselectBarAccident', 'IndustrialSecure\AccidentsWork\AccidentsWorkReportController@multiselectBar');
        Route::post('multiselectBarReports', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@multiselectBar');
        Route::post('multiselectBarLegalMatrix', 'LegalAspects\LegalMatrix\LawReportController@multiselectBar');
        Route::post('multiselectBarPercentage', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@multiselectBarPercentage');
        Route::post('dmActivities', 'IndustrialSecure\Activities\ActivityController@multiselect');
        Route::post('rmSubprocess', 'IndustrialSecure\RiskMatrix\SubProcessController@multiselect');
        Route::post('rmRisk', 'IndustrialSecure\RiskMatrix\RiskController@multiselect');
        Route::post('rmControlsDecrease', 'General\MultiSelectRadioController@rmControlsDecrease');
        Route::post('rmNature', 'General\MultiSelectRadioController@rmNature');
        Route::post('rmCoverage', 'General\MultiSelectRadioController@rmCoverage');
        Route::post('rmDocumentation', 'General\MultiSelectRadioController@rmDocumentation');
        Route::post('dmDangers', 'IndustrialSecure\Dangers\DangerController@multiselect');
        Route::post('dmDangerMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController@multiselect');
        Route::post('dmGeneratedDangers', 'General\MultiSelectRadioController@dmGeneratedDangers');
        Route::post('tagsAdministrativeControls', 'IndustrialSecure\Tags\TagController@multiselectAdministrativeControls');
        Route::post('tagsAddFields/{id}', 'IndustrialSecure\Tags\TagController@multiselectAddFields');
        Route::post('tagsEngineeringControls', 'IndustrialSecure\Tags\TagController@multiselectEngineeringControls');
        Route::post('tagsHistoryChange', 'IndustrialSecure\Tags\TagController@multiselectHistoryChange');
        Route::post('tagsEpp', 'IndustrialSecure\Tags\TagController@multiselectEpp');
        Route::post('tagsPossibleConsequencesDanger', 'IndustrialSecure\Tags\TagController@multiselectPossibleConsequencesDanger');
        Route::post('tagsWarningSignage', 'IndustrialSecure\Tags\TagController@multiselectWarningSignage');
        Route::post('tagsTypeProcess', 'General\TagController@multiselectTypeProcess');
        Route::post('tagsTypeProcessRiskMatrix', 'General\TagController@multiselectTypeProcessRiskMatrx');
        Route::post('tagsSubstitution', 'IndustrialSecure\Tags\TagController@multiselectSubstitution');
        Route::post('tagsParticipants', 'IndustrialSecure\Tags\TagController@multiselectParticipants');
        Route::post('tagsRmParticipants', 'IndustrialSecure\RiskMatrix\Tags\ParticipantsController@multiselect');
        Route::post('tagsRmCategories', 'IndustrialSecure\RiskMatrix\Tags\CategoryRiskController@multiselect');
        Route::post('tagsRmRiskCausesControls', 'IndustrialSecure\RiskMatrix\Tags\CauseControlsController@multiselect');
        Route::post('tagsDangerDescription', 'IndustrialSecure\Tags\TagController@multiselectDangerDescription');
        Route::post('yearDangerMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController@multiselectYear');
        Route::post('tagsRoles', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectRolesParticipants');
        Route::post('actionPlanStates/{all?}', 'General\MultiSelectRadioController@actionPlanStates');
        Route::post('actionPlanModules', 'Administrative\ActionPlans\ActionPlanController@actionPlanModules');
        Route::post('contractors', 'LegalAspects\Contracs\ContractLesseeController@multiselect');
        Route::post('contractStandar', 'LegalAspects\Contracs\ContractLesseeController@multiselectStandard');
        Route::post('ctRoles', 'General\MultiSelectRadioController@ctRoles');
        Route::post('ctContractClassifications', 'General\MultiSelectRadioController@ctContractClassifications'); 
        Route::post('ctkindsRisks', 'General\MultiSelectRadioController@ctkindsRisks'); 
        Route::post('siNo', 'General\MultiSelectRadioController@siNoSelect');
        Route::post('siNoFiltro', 'General\ApplicationController@siNo');
        Route::post('companies', 'General\ApplicationController@multiselectCompanies');
        Route::post('companiesGroupSpecific', 'General\ApplicationController@multiselectCompaniesGroupSpecific');
        Route::post('companiesGroup', 'General\ApplicationController@multiselectCompaniesGroup');
        Route::post('reincYears', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectYears');
        Route::post('reincSveAssociateds', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectSveAssociateds');
        Route::post('reincMedicalCertificates', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectMedicalCertificates');
        Route::post('reincRelocatedTypes', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectRelocatedTypes');
        Route::post('dmReportMultiselect', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@multiselect');
        Route::post('rmReportMultiselect', 'IndustrialSecure\RiskMatrix\RiskMatrixReportHistoryController@multiselect');
        Route::post('dmReportMonths', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@multiselect');
        Route::post('reportDinamic/years', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@multiselectYears');
        Route::post('reportDinamic/months', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@multiselectMounts');
        Route::post('qualificationMasiveInspection', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@multiselectQualification');
        Route::post('tagsTypeEpp', 'IndustrialSecure\EPP\ElementController@multiselectTypes');
        Route::post('tagsMarkEpp', 'IndustrialSecure\EPP\ElementController@multiselectMarks');
        Route::post('classElement', 'IndustrialSecure\EPP\ElementController@multiselectClassElement');
        Route::post('tagsStandarApplyEpp', 'IndustrialSecure\EPP\ElementController@multiselectApplicableStandard');
        Route::post('tagsReincReinstatementcondition', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectReinstatementcondition');
        Route::post('tagsReincInformantRole', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectInformantRole');
        Route::post('tagsReincMotive', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectMotiveClose');
        Route::post('tagsReason', 'IndustrialSecure\EPP\IncomeController@multiselectReason');

        Route::prefix('evaluations')->group(function () {
          Route::post('evaluations', 'LegalAspects\Contracs\EvaluationController@multiselectEvaluations');
          Route::post('objectives', 'LegalAspects\Contracs\EvaluationController@multiselectObjectives');
          Route::post('subobjectives', 'LegalAspects\Contracs\EvaluationController@multiselectSubobjectives');
          Route::post('items', 'LegalAspects\Contracs\EvaluationController@multiselectItems');
          Route::post('qualificationTypes', 'LegalAspects\Contracs\TypeRatingController@multiselect');
          Route::post('years', 'LegalAspects\Contracs\EvaluationContractController@multiselectYears');
          Route::post('months', 'LegalAspects\Contracs\EvaluationContractController@multiselectMounts');
        });

        Route::post('inform/years', 'LegalAspects\Contracs\InformContractController@multiselectYears');
        Route::post('inform/month', 'LegalAspects\Contracs\InformContractController@multiselectMonth');

        Route::prefix('absenteeism')->group(function () {
          Route::post('talends', 'PreventiveOccupationalMedicine\Absenteeism\TalendController@multiselect');
        });

        Route::prefix('contracts')->group(function () {
          Route::post('sectionCategoryItems', 'LegalAspects\Contracs\SectionCategoryItemController@multiselect');
          Route::post('highRisk', 'LegalAspects\Contracs\ContractLesseeController@multiselectHighRisk');
          Route::post('usersResponsibles', 'LegalAspects\Contracs\ContractLesseeController@multiselectUsers');          
          Route::post('ctActivities', 'LegalAspects\Contracs\ContractActivityController@multiselect');
          Route::post('ctActivitiesContracts', 'LegalAspects\Contracs\ContractEmployeeController@multiselect');
          Route::post('ctTrainingTypeQuestions', 'LegalAspects\Contracs\ContractTrainingController@multiselectTypeQuestion');          
          Route::post('statesFile', 'General\MultiSelectRadioController@ctFileStates');
        });

        Route::prefix('industrialSecurity')->group(function () {
          Route::post('conditions', 'IndustrialSecure\DangerousConditions\Reports\ReportController@multiselectConditions');
          Route::post('conditionTypes', 'IndustrialSecure\DangerousConditions\Reports\ReportController@multiselectConditionTypes');
          Route::post('rates', 'General\MultiSelectRadioController@phRates');
          Route::post('inspections', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@multiselectInspection');
          Route::post('inspectionType', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@multiselectTypes');
        });
        Route::post('themes', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@multiselectThemes');

        Route::prefix('legalMatrix')->group(function () {
          Route::post('interests', 'LegalAspects\LegalMatrix\InterestController@multiselect');
          Route::post('interestsCompany', 'LegalAspects\LegalMatrix\InterestController@multiselectCompany');
          Route::post('interestsSystem', 'LegalAspects\LegalMatrix\InterestController@multiselectSystem');
          Route::post('years', 'LegalAspects\LegalMatrix\LawController@lmYears');
          Route::post('responsibles', 'LegalAspects\LegalMatrix\LawController@lmLawResponsibles');
          Route::post('riskAspects', 'LegalAspects\LegalMatrix\RiskAspectController@multiselect');
          Route::post('sstRisks', 'LegalAspects\LegalMatrix\SstRiskController@multiselect');
          Route::post('entities', 'LegalAspects\LegalMatrix\EntityController@multiselect');
          Route::post('entitiesCompany', 'LegalAspects\LegalMatrix\EntityController@multiselectCompany');
          Route::post('typesCompany', 'LegalAspects\LegalMatrix\LawTypeController@multiselectCompany');
          Route::post('entitiesSystem', 'LegalAspects\LegalMatrix\EntityController@multiselectSystem');
          Route::post('typesSystem', 'LegalAspects\LegalMatrix\LawTypeController@multiselectSystem');
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

        Route::post('ctInformReportMultiselect', 'LegalAspects\Contracs\InformReportController@multiselect');
        Route::post('ctInformReportMultiselectThemes', 'LegalAspects\Contracs\InformReportController@multiselectThemes');
        Route::post('ctInformReportMultiselectItems', 'LegalAspects\Contracs\InformReportController@multiselectItems');

        Route::prefix('system')->group(function () {
          Route::post('labels', 'System\Labels\LabelController@multiselect');
          Route::post('usersCompany', 'System\Companies\CompanyController@multiselectUsers');
          Route::post('rolesCompany', 'System\Companies\CompanyController@multiselectRoles');
        });

        Route::post('eppElements', 'IndustrialSecure\EPP\ElementController@multiselect');
        Route::post('eppLocations', 'IndustrialSecure\EPP\LocationController@multiselect');

        Route::prefix('accidents')->group(function () {
          Route::post('identifications', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectIdentification');
          Route::post('names', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectName');
          Route::post('razonSocial', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectSocialReason');
          Route::post('activityEconomic', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectActivityEconomic');
          Route::post('cargo', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectCargo');
          Route::post('agents', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectAgents');
          Route::post('mechanisms', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectMechanisms');
          Route::post('siNo', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@multiselectSiNo');
        });
    });

    Route::prefix('radios')->group(function () {
      Route::post('dmTypeActivities', 'General\MultiSelectRadioController@dmTypeActivities');
      Route::post('siNo', 'General\MultiSelectRadioController@siNo');
      Route::post('conf/locationLevelForm', 'Administrative\Configurations\ConfigurationController@radioLocationLevels');
      Route::post('ctTypesEvaluation', 'General\MultiSelectRadioController@ctTypesEvaluation');

      Route::post('agents', 'General\MultiSelectRadioController@agents');
      Route::post('sites', 'General\MultiSelectRadioController@sites');
      Route::post('mechanisms', 'General\MultiSelectRadioController@mechanisms');
      Route::post('partsBody', 'General\MultiSelectRadioController@partsbody');
      Route::post('lesionTypes', 'General\MultiSelectRadioController@lesiontypes');
      
      Route::prefix('legalMatrix')->group(function () {
        Route::post('interestsSystem', 'LegalAspects\LegalMatrix\InterestController@radioSystem');
      });
    });

    //Administrativo
    Route::prefix('administration')->group(function () {
      Route::post('users/filtersConfig', 'Administrative\Users\UserController@filtersConfig');
			Route::post('users/data', 'Administrative\Users\UserController@data');
      Route::post('users/export', 'Administrative\Users\UserController@export');
      Route::post('users/import', 'Administrative\Users\UserController@import');
      Route::post('users/changePassword', 'Administrative\Users\UserController@changePassword');
      Route::get('users/myDefaultModule', 'Administrative\Users\UserController@myDefaultModule');
      Route::post('users/defaultModule', 'Administrative\Users\UserController@defaultModule');
      Route::get('users/firm/view', 'Administrative\Users\UserController@showFirm');
      Route::post('users/firm', 'Administrative\Users\UserController@storeFirm');
      Route::post('users/addUserOtherCompany', 'Administrative\Users\UserController@addUserOtherCompany');
			Route::ApiResource('users', 'Administrative\Users\UserController');
			Route::post('userActivities', 'Administrative\Users\UserController@userActivities');

			Route::post('role/data', 'Administrative\Roles\RoleController@data');
			Route::ApiResource('role', 'Administrative\Roles\RoleController');

			Route::post('position/data', 'Administrative\Positions\EmployeePositionController@data');
			Route::ApiResource('position', 'Administrative\Positions\EmployeePositionController');
			Route::post('position/import', 'Administrative\Positions\EmployeePositionController@import');
      Route::post('position/export', 'Administrative\Positions\EmployeePositionController@export');

			Route::post('regional/data', 'Administrative\Regionals\EmployeeRegionalController@data');
			Route::ApiResource('regional', 'Administrative\Regionals\EmployeeRegionalController');

			Route::post('regional/import', 'Administrative\Regionals\EmployeeRegionalController@import');

			Route::post('business/data', 'Administrative\Businesses\EmployeeBusinessController@data');
			Route::ApiResource('business', 'Administrative\Businesses\EmployeeBusinessController');

			Route::post('headquarter/data', 'Administrative\Headquarters\EmployeeHeadquarterController@data');
			Route::ApiResource('headquarter', 'Administrative\Headquarters\EmployeeHeadquarterController');

			Route::post('area/data', 'Administrative\Areas\EmployeeAreaController@data');
			Route::ApiResource('area', 'Administrative\Areas\EmployeeAreaController');

			Route::post('process/data', 'Administrative\Processes\EmployeeProcessController@data');
			Route::ApiResource('process', 'Administrative\Processes\EmployeeProcessController');

      Route::post('employee/import', 'Administrative\Employees\EmployeesController@import');
      Route::post('employee/importInactive', 'Administrative\Employees\EmployeesController@importInactive');
			Route::post('employee/data', 'Administrative\Employees\EmployeesController@data');
			Route::ApiResource('employee', 'Administrative\Employees\EmployeesController');

      Route::post('employee/switchStatus/{employee}', 'Administrative\Employees\EmployeesController@toggleState');

      Route::post('configuration', 'Administrative\Configurations\ConfigurationController@store');
      Route::get('configuration/view', 'Administrative\Configurations\ConfigurationController@show');
					

      Route::prefix('configurations')->group(function () {
        Route::post('locationLevelForms/getConfModule', 'Administrative\Configurations\LocationLevelFormController@getConfModule');
        Route::post('locationLevelForms/getConfUser', 'Administrative\Configurations\LocationLevelFormController@getConfUser');
				Route::post('locationLevelForms/data', 'Administrative\Configurations\LocationLevelFormController@data');
				Route::ApiResource('locationLevelForms', 'Administrative\Configurations\LocationLevelFormController');
      });

      Route::post('actionplan/data', 'Administrative\ActionPlans\ActionPlanController@data');
      Route::post('actionplan/export', 'Administrative\ActionPlans\ActionPlanController@export');
      Route::delete('actionplan/deleteActivity/{id}', 'Administrative\ActionPlans\ActionPlanController@destroy');
      Route::ApiResource('actionplan', 'Administrative\ActionPlans\ActionPlanController');
      Route::post('actionplan/saveTracing', 'Administrative\ActionPlans\ActionPlanController@saveTracing');
      Route::post('actionplan/getTracings', 'Administrative\ActionPlans\ActionPlanController@getTracings');
      Route::post('actionplan/report', 'Administrative\ActionPlans\ActionPlanController@report');
      Route::post('actionplan/reportPie', 'Administrative\ActionPlans\ActionPlanController@reportPie');

      Route::get('actionPlan/download/{file}', 'Administrative\ActionPlans\ActionPlanController@download');

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
      Route::post('dangersMatrix/getfieldsadd', 'IndustrialSecure\DangerMatrix\DangerMatrixController@getFieldAdd');
      Route::post('dangersMatrix/addFields', 'IndustrialSecure\DangerMatrix\DangerMatrixController@saveFields');
      Route::post('dangersMatrix/getfields', 'IndustrialSecure\DangerMatrix\DangerMatrixController@getAdditionalFiels');
      Route::post('dangersMatrix/report', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@report');
      Route::post('dangersMatrix/reportDangerTable', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportDangerTable');
      Route::post('dangersMatrix/history/reportDangerTable', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@reportDangerTable');
      Route::get('dangersMatrix/reportDetail/{id}', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportDetail');
      Route::get('dangersMatrix/reportDetailHistory/{id}', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@reportDetail');
      Route::post('dangersMatrix/reportExport ', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportExport');
      Route::post('dangersMatrix/reportHistory', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@report');
      Route::post('dangersMatrix/reportHistoryExport ', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@reportExport');
      Route::get('dangersMatrix/download/{dangersMatrix}', 'IndustrialSecure\DangerMatrix\DangerMatrixController@download');
      Route::post('dangersMatrix/import', 'IndustrialSecure\DangerMatrix\DangerMatrixController@import');
      Route::ApiResource('dangersMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController');

      Route::post('dangersMatrixHistory/data', 'IndustrialSecure\DangerMatrix\DangerMatrixHistoryController@data');

      Route::post('dangersMatrix/historyQualification/data', 'IndustrialSecure\DangerMatrix\DangerMatrixController@getLogQualificationChange');

      Route::post('dangersMatrix/searchKeyword/data', 'IndustrialSecure\DangerMatrix\DangerMatrixController@searchKeyword');

//tags

      Route::post('dangersMatrix/tagAdministrativeControls/searchKeyword/data', 'IndustrialSecure\Tags\AdministrativeControlsController@sharedTag');
      Route::post('dangersMatrix/engineeringControls/searchKeyword/data', 'IndustrialSecure\Tags\EngineeringControlsController@sharedTag');
      Route::post('dangersMatrix/epp/searchKeyword/data', 'IndustrialSecure\Tags\EppController@sharedTag');
      Route::post('dangersMatrix/warningSignage/searchKeyword/data', 'IndustrialSecure\Tags\WarningSignageController@sharedTag');
      Route::post('dangersMatrix/substitution/searchKeyword/data', 'IndustrialSecure\Tags\SubstitutionController@sharedTag');
      Route::post('dangersMatrix/possibleConsequencesDanger/searchKeyword/data', 'IndustrialSecure\Tags\PossibleConsequencesDangerController@sharedTag');
      Route::post('dangersMatrix/participants/searchKeyword/data', 'IndustrialSecure\Tags\ParticipantsController@sharedTag');
      Route::post('dangersMatrix/dangerDescription/searchKeyword/data', 'IndustrialSecure\Tags\DangerDescriptionController@sharedTag');

////////

      Route::post('risksMatrix/data', 'IndustrialSecure\RiskMatrix\RiskMatrixController@data');
      Route::post('risksMatrix/getEvaluationControls', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getEvaluationControl');
      Route::post('risksMatrix/getTextHelp', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getTextHelp');
      Route::post('risksMatrix/getMitigation', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getMitigation');
      Route::post('risksMatrix/getImpacts', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getImpacts');
      Route::ApiResource('risksMatrix', 'IndustrialSecure\RiskMatrix\RiskMatrixController');
      Route::post('risksMatrix/report', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportInherent');
      Route::post('risksMatrix/reportRiskInherentTable', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportRiskInherentTable');
      Route::post('risksMatrix/reportResidual', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportResidual');
      Route::post('risksMatrix/reportRiskResidualTable', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportRiskResidualTable');
      Route::post('risksMatrix/reportTableResidual', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportTableResidual');
      Route::get('risksMatrix/download/{risksMatrix}', 'IndustrialSecure\RiskMatrix\RiskMatrixController@download');
      Route::post('risksMatrix/import', 'IndustrialSecure\RiskMatrix\RiskMatrixController@import');


      Route::post('risksMatrix/reportExport', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportExporteExcel');
      Route::post('risksMatrix/reportHistoryExport ', 'IndustrialSecure\RiskMatrix\RiskMatrixReportHistoryController@reportExportExcel');

      Route::post('risksMatrix/reportExportPdf', 'IndustrialSecure\RiskMatrix\RiskMatrixReportController@reportExportPdf');
      Route::post('risksMatrix/reportHistoryExportPdf ', 'IndustrialSecure\RiskMatrix\RiskMatrixReportHistoryController@reportExportPdf');


      Route::post('risksMatrix/reportHistory', 'IndustrialSecure\RiskMatrix\RiskMatrixReportHistoryController@reportInherent');
      Route::post('risksMatrix/reportHistoryResidual', 'IndustrialSecure\RiskMatrix\RiskMatrixReportHistoryController@reportResidual');
      Route::post('risksMatrix/reportHistoryTableResidual', 'IndustrialSecure\RiskMatrix\RiskMatrixReportHistoryController@reportTableResidual');

      Route::post('subProcess/data', 'IndustrialSecure\RiskMatrix\SubProcessController@data');
      Route::ApiResource('subProcess', 'IndustrialSecure\RiskMatrix\SubProcessController');

      Route::post('risk/data', 'IndustrialSecure\RiskMatrix\RiskController@data');
      Route::ApiResource('risk', 'IndustrialSecure\RiskMatrix\RiskController');


      Route::post('risksMatrix/macroprocess/data', 'IndustrialSecure\RiskMatrix\MacroprocessController@data');

      Route::post('risksMatrix/getAbrevRegional', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getAbrevRegional');
      Route::post('risksMatrix/getAbrevHeadquarter', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getAbrevHeadquarter');
      Route::post('risksMatrix/getAbrevProcess', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getAbrevProcess');
      Route::post('risksMatrix/getAbrevArea', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getAbrevArea');
      Route::post('risksMatrix/getAbrevMacro', 'IndustrialSecure\RiskMatrix\RiskMatrixController@getAbrevMacro');

      Route::prefix('risksMatrix')->group(function () {
        Route::ApiResource('macroprocess', 'IndustrialSecure\RiskMatrix\MacroprocessController');
      });

      Route::prefix('dangerousConditions')->group(function () {
        Route::get('incentive/download', 'IndustrialSecure\DangerousConditions\IncentiveController@download');
        Route::get('incentive/view', 'IndustrialSecure\DangerousConditions\IncentiveController@show');
        Route::post('incentive', 'IndustrialSecure\DangerousConditions\IncentiveController@store');
        Route::post('inspection/switchStatus/{inspection}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@toggleState');
        Route::post('inspection/data', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@data');
        Route::post('inspection/reportDinamic', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@reportDinamic');
        Route::ApiResource('inspection', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController');
        Route::post('inspection/import', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@import');
        Route::post('inspection/saveConfigurationMasive', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@storeQualificationOption');
        Route::post('inspection/getConfigurationMasive', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@getQualificationOption');

        Route::get('inspection/downloadPdf/{id}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@downloadPdf');

        Route::post('inspection/requestFirm/data', 'IndustrialSecure\DangerousConditions\Inspections\InspectionRequestFirmController@data');

        Route::post('inspection/requestFirm/view/{id}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionRequestFirmController@showInspection');

        Route::post('inspection/requestFirm/saveFirm', 'IndustrialSecure\DangerousConditions\Inspections\InspectionRequestFirmController@saveFirm');
        
        Route::get('inspection/qualification/downloadImage/{id}/{column}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@downloadImage');
        Route::post('inspection/qualification/saveImage', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@saveImage');
        Route::post('inspection/qualification/saveQualification', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@saveQualification');
        Route::post('inspection/qualification/data', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@data');
        Route::ApiResource('inspection/qualification', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController');
        Route::post('inspection/export', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@export');
        Route::post('inspection/getFiltersUsers', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@getFiltersUsers');
        Route::post('inspection/exportQualify', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@exportQualify');
        Route::post('inspection/report', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@data');
        Route::post('inspection/reportType2', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@dataType2');
        Route::post('inspection/report/getTotals', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@getTotals');
        Route::post('inspection/report/getTotalsType2', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@getTotalsType2');
        Route::post('inspection/exportReport', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@export');
      
        Route::ApiResource('report', 'IndustrialSecure\DangerousConditions\Reports\ReportController');
        Route::post('report/export', 'IndustrialSecure\DangerousConditions\Reports\ReportController@export');
        Route::post('report/data', 'IndustrialSecure\DangerousConditions\Reports\ReportController@data');
        Route::post('report/saveImage', 'IndustrialSecure\DangerousConditions\Reports\ReportController@saveImage');
        Route::post('report/saveQualification', 'IndustrialSecure\DangerousConditions\Reports\ReportController@saveQualification');
        Route::get('report/downloadImage/{id}/{column}', 'IndustrialSecure\DangerousConditions\Reports\ReportController@downloadImage');
        Route::post('report/informs', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@data');
        Route::post('report/conditionHeadquarter', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@locationWithCondition');
      });

      Route::get('document/download/{document}', 'IndustrialSecure\Documents\DocumentController@download');
      Route::post('document/data', 'IndustrialSecure\Documents\DocumentController@data');
      Route::ApiResource('document', 'IndustrialSecure\Documents\DocumentController');

      Route::ApiResource('accidents', 'IndustrialSecure\AccidentsWork\AccidentsWorkController');
      Route::post('accidents/data', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@data');
      Route::get('accidents/downloadFile/{file}', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@download');

      Route::get('accidents/downloadPdf/{accident}', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@downloadPdf');

      Route::post('accidents/export', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@export');


      Route::post('accidents/reportLine', 'IndustrialSecure\AccidentsWork\AccidentsWorkReportController@reportLineNumberAccidents');

      Route::post('accidents/reportDinamic', 'IndustrialSecure\AccidentsWork\AccidentsWorkReportController@getInformDataDinamic');

      Route::post('accidents/saveCauses', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@saveCauses');

      Route::post('accidents/getCauses', 'IndustrialSecure\AccidentsWork\AccidentsWorkController@getCauses');

      Route::post('configuration', 'IndustrialSecure\DangerousConditions\ConfigurationController@store');
      Route::get('configuration/view', 'IndustrialSecure\DangerousConditions\ConfigurationController@show');

      Route::prefix('epp')->group(function () {
        Route::ApiResource('element', 'IndustrialSecure\EPP\ElementController');
        Route::post('element/data', 'IndustrialSecure\EPP\ElementController@data');
        Route::post('element/reportBalance', 'IndustrialSecure\EPP\ElementController@reportBalance');
        Route::post('element/reportEmployee', 'IndustrialSecure\EPP\ElementController@reportEmployee');
        Route::post('element/reportTotal', 'IndustrialSecure\EPP\ElementController@reportTotal');
        Route::post('element/reportEmployeeTotals', 'IndustrialSecure\EPP\ElementController@reportEmployeeTotals');
        Route::post('element/reportStockMinimun', 'IndustrialSecure\EPP\ElementController@reportBalanceStockMinimun');        
        Route::get('element/download/{element}', 'IndustrialSecure\EPP\ElementController@downloadImage');
        Route::post('element/import', 'IndustrialSecure\EPP\ElementController@import');
        Route::post('element/importStockMinimun', 'IndustrialSecure\EPP\ElementController@importStockMinimun');
        Route::post('element/import/balanceInicial', 'IndustrialSecure\EPP\ElementController@importBalanceInicial');
        Route::ApiResource('location', 'IndustrialSecure\EPP\LocationController');
        Route::post('location/data', 'IndustrialSecure\EPP\LocationController@data');
        Route::post('location/import', 'IndustrialSecure\EPP\LocationController@import');

        Route::post('transaction/data', 'IndustrialSecure\EPP\TransactionController@data');
        Route::post('transaction/delivery/export', 'IndustrialSecure\EPP\TransactionController@export');
        Route::ApiResource('transaction', 'IndustrialSecure\EPP\TransactionController');
        Route::get('transaction/employeeInfo/{id}', 'IndustrialSecure\EPP\TransactionController@employeeInfo');

        Route::post('transaction/eppElementsLocations', 'IndustrialSecure\EPP\TransactionController@elementsLocation');

        Route::post('transaction/elementInfo/', 'IndustrialSecure\EPP\TransactionController@elementInfo');

        Route::post('transaction/hashSelected/', 'IndustrialSecure\EPP\TransactionController@hashSelected');

        Route::post('transaction/deletedTemporal/', 'IndustrialSecure\EPP\TransactionController@deletedTemporal');

        Route::get('transaction/download/file/{file}', 'IndustrialSecure\EPP\TransactionController@download');

        Route::get('transaction/downloadPdf/{id}', 'IndustrialSecure\EPP\TransactionController@downloadPdf');

        Route::post('transaction/returnDelivery/{transaction}', 'IndustrialSecure\EPP\TransactionController@returnDelivery');

        Route::post('transactionReturns/data', 'IndustrialSecure\EPP\TransactionReturnsController@data');
        //Route::ApiResource('transactions', 'IndustrialSecure\EPP\TransactionReturnsController');
        Route::get('transaction/employeeReturns/{id}', 'IndustrialSecure\EPP\TransactionReturnsController@employeeInfo');        
        Route::post('transaction/returns/eppElementsLocations', 'IndustrialSecure\EPP\TransactionReturnsController@elementsLocation');
        Route::post('transaction/wastes/data', 'IndustrialSecure\EPP\TransactionReturnsController@dataWastes');

        Route::post('income/data', 'IndustrialSecure\EPP\IncomeController@data');
        Route::ApiResource('income', 'IndustrialSecure\EPP\IncomeController');
        Route::post('income/elementInfo/', 'IndustrialSecure\EPP\IncomeController@elementInfo');

        Route::post('exit/data', 'IndustrialSecure\EPP\ExitController@data');
        Route::ApiResource('exit', 'IndustrialSecure\EPP\ExitController');
        Route::post('exit/elementInfo/', 'IndustrialSecure\EPP\ExitController@elementInfo');

        Route::post('exit/eppElementsLocations', 'IndustrialSecure\EPP\ExitController@elementsLocation');

        Route::post('configuration', 'IndustrialSecure\EPP\ConfigurationController@store');

        Route::get('configuration/view', 'IndustrialSecure\EPP\ConfigurationController@show');

        Route::post('transfer/data', 'IndustrialSecure\EPP\TransferController@data');
        Route::ApiResource('transfer', 'IndustrialSecure\EPP\TransferController');
        Route::post('transfer/elementInfo/', 'IndustrialSecure\EPP\TransferController@elementInfo');

        Route::post('transfer/eppElementsLocations', 'IndustrialSecure\EPP\TransferController@elementsLocation');


        Route::post('reception/data', 'IndustrialSecure\EPP\ReceptionController@data');
        Route::ApiResource('reception', 'IndustrialSecure\EPP\ReceptionController');
      });

      Route::prefix('tags')->group(function () {
        Route::post('administrativeControls/data', 'IndustrialSecure\Tags\AdministrativeControlsController@data');
        Route::ApiResource('administrativeControls', 'IndustrialSecure\Tags\AdministrativeControlsController');
        Route::post('engineeringControls/data', 'IndustrialSecure\Tags\EngineeringControlsController@data');
        Route::ApiResource('engineeringControls', 'IndustrialSecure\Tags\EngineeringControlsController');
        Route::post('epp/data', 'IndustrialSecure\Tags\EppController@data');
        Route::ApiResource('epp', 'IndustrialSecure\Tags\EppController');
        Route::post('possibleConsequencesDanger/data', 'IndustrialSecure\Tags\PossibleConsequencesDangerController@data');
        Route::ApiResource('possibleConsequencesDanger', 'IndustrialSecure\Tags\PossibleConsequencesDangerController');
        Route::post('warningSignage/data', 'IndustrialSecure\Tags\WarningSignageController@data');
        Route::ApiResource('warningSignage', 'IndustrialSecure\Tags\WarningSignageController');
        Route::post('substitution/data', 'IndustrialSecure\Tags\SubstitutionController@data');
        Route::ApiResource('substitution', 'IndustrialSecure\Tags\SubstitutionController');
        Route::post('participants/data', 'IndustrialSecure\Tags\ParticipantsController@data');
        Route::ApiResource('participants', 'IndustrialSecure\Tags\ParticipantsController');
        Route::post('dangerDescription/data', 'IndustrialSecure\Tags\DangerDescriptionController@data');
        Route::ApiResource('dangerDescription', 'IndustrialSecure\Tags\DangerDescriptionController');
      });
		});
		
		//Aspectos Legales
		Route::prefix('legalAspects')->group(function () {
      Route::post('configuration', 'LegalAspects\LegalMatrix\ConfigurationController@store');
      Route::post('contracts/data', 'LegalAspects\Contracs\ContractLesseeController@data');
      Route::get('contracts/getInformation', 'LegalAspects\Contracs\ContractLesseeController@getInformation');
      Route::get('contracts/downloadListCheckPdf/{id}', 'LegalAspects\Contracs\ContractLesseeController@downloadPdf');
      Route::post('contracts/getListCheckItems', 'LegalAspects\Contracs\ContractLesseeController@getListCheckItems');
      Route::post('contracts/getValidationQualificarion', 'LegalAspects\Contracs\ContractLesseeController@verifyValidateQualificationListCheck');
      Route::post('contracts/aproveQualification', 'LegalAspects\Contracs\ContractLesseeController@aproveQualification');
      Route::post('contracts/desaproveQualification', 'LegalAspects\Contracs\ContractLesseeController@desaproveQualification');
      Route::post('contracts/getItemValidation', 'LegalAspects\Contracs\ContractLesseeController@getListCheckItemsValidations');
      Route::post('contracts/saveValidations', 'LegalAspects\Contracs\ContractLesseeController@saveItemsStandard');
      Route::get('contracts/qualifications', 'LegalAspects\Contracs\ContractLesseeController@qualifications');
      Route::post('contracts/saveQualificationItems', 'LegalAspects\Contracs\ContractLesseeController@saveQualificationItems');
      Route::post('contractsListCheckHistory/data', 'LegalAspects\Contracs\ListCheckHistoryController@data');
      Route::post('contracts/retrySendMail/{contract}', 'LegalAspects\Contracs\ContractLesseeController@retrySendMail');
      Route::post('contracts/reactiveUser/{contract}', 'LegalAspects\Contracs\ContractLesseeController@reactiveUser');
      Route::post('contracts/listCheckCopy', 'LegalAspects\Contracs\ContractLesseeController@listCheckCopy');
      Route::post('contracts/export', 'LegalAspects\Contracs\ContractLesseeController@export');
      Route::post('contracts/import', 'LegalAspects\Contracs\ContractLesseeController@import');
      Route::ApiResource('contracts', 'LegalAspects\Contracs\ContractLesseeController');
      Route::post('contracts/saveDocuments', 'LegalAspects\Contracs\ContractLesseeController@saveDocuments');
      Route::post('contracts/getDocuments', 'LegalAspects\Contracs\ContractLesseeController@getDocuments');

      Route::post('contracts/getInformationActivities', 'LegalAspects\Contracs\ContractLesseeController@getInformationActivities');
      Route::post('contracts/saveMasiveActivities', 'LegalAspects\Contracs\ContractLesseeController@saveMasiveActivities');


      Route::post('contracts/getInformationResponsibles', 'LegalAspects\Contracs\ContractLesseeController@getInformationResponsibles');
      Route::post('contracts/saveMasiveResponsibles', 'LegalAspects\Contracs\ContractLesseeController@saveMasiveResponsibles');

      Route::post('contracts/configuration', 'LegalAspects\Contracs\ConfigurationController@store');
      Route::get('contracts/configuration/view', 'LegalAspects\Contracs\ConfigurationController@show');

      Route::ApiResource('listCheck', 'LegalAspects\Contracs\ListCheckQualificationController');
      Route::post('listCheck/data', 'LegalAspects\Contracs\ListCheckQualificationController@data');
      Route::post('listCheck/getListCheckItems', 'LegalAspects\Contracs\ListCheckQualificationController@getListCheckItems');
      Route::post('listCheck/saveQualificationItems', 'LegalAspects\Contracs\ListCheckQualificationController@saveQualificationItems');
      Route::post('listCheck/listCheckCopy', 'LegalAspects\Contracs\ListCheckQualificationController@listCheckItemsClone');
      Route::get('listCheck/downloadPdf/{listCheck}', 'LegalAspects\Contracs\ListCheckQualificationController@downloadPdf');
      Route::post('listCheck/switchStatus/{trainingContract}', 'LegalAspects\Contracs\ListCheckQualificationController@toggleState');
      Route::post('listCheck/verifyRequiredFile', 'LegalAspects\Contracs\ListCheckQualificationController@verifyRequiredFile');
      Route::post('listCheck/getValidationQualification', 'LegalAspects\Contracs\ListCheckQualificationController@verifyValidateQualificationListCheck');

      Route::post('fileUpload/data', 'LegalAspects\Contracs\FileUploadController@data');
      Route::get('fileUpload/download/{fileUpload}', 'LegalAspects\Contracs\FileUploadController@download');
      Route::post('fileUpload/getFilesItem', 'LegalAspects\Contracs\FileUploadController@getFilesItem');
      Route::ApiResource('fileUpload', 'LegalAspects\Contracs\FileUploadController');
      Route::post('fileUpload/report', 'LegalAspects\Contracs\FileUploadController@dataReport');

      Route::post('typeRating/data', 'LegalAspects\Contracs\TypeRatingController@data');
      Route::post('typeRating/AllTypesRating', 'LegalAspects\Contracs\TypeRatingController@getAllTypesRating');
      Route::ApiResource('typeRating', 'LegalAspects\Contracs\TypeRatingController');

      Route::post('evaluation/data', 'LegalAspects\Contracs\EvaluationController@data');
      Route::post('evaluation/export', 'LegalAspects\Contracs\EvaluationController@export');
      Route::ApiResource('evaluation', 'LegalAspects\Contracs\EvaluationController');
      Route::post('evaluation/block', 'LegalAspects\Contracs\EvaluationController@inEdit');

      Route::get('evaluationContract/downloadFile/{evaluationFile}', 'LegalAspects\Contracs\EvaluationContractController@downloadFile');
      Route::post('evaluationContract/data', 'LegalAspects\Contracs\EvaluationContractController@data');
      Route::get('evaluationContract/download/{evaluationContract}', 'LegalAspects\Contracs\EvaluationContractController@download');
      Route::get('evaluationContract/downloadPdf/{evaluationContract}', 'LegalAspects\Contracs\EvaluationContractController@downloadPdf');
      Route::get('evaluationContract/getData/{evaluationContract}', 'LegalAspects\Contracs\EvaluationContractController@getData');  
      Route::post('evaluationContract/report', 'LegalAspects\Contracs\EvaluationContractController@report');
      Route::post('evaluationContract/reportDinamic', 'LegalAspects\Contracs\EvaluationContractController@reportDinamicBar');
      Route::post('evaluationContract/exportReport', 'LegalAspects\Contracs\EvaluationContractController@exportReport');
      Route::post('evaluationContract/getTotales', 'LegalAspects\Contracs\EvaluationContractController@getTotales');
      Route::ApiResource('evaluationContract', 'LegalAspects\Contracs\EvaluationContractController');
      Route::post('evaluationContract/sendNotification/{contract}', 'LegalAspects\Contracs\EvaluationContractController@sendNotification');
      Route::post('evaluationContractHistory/data', 'LegalAspects\Contracs\EvaluationContractHistoryController@data');
      Route::post('activityContract/data', 'LegalAspects\Contracs\ContractActivityController@data');
      Route::ApiResource('activityContract', 'LegalAspects\Contracs\ContractActivityController');
      Route::get('trainingContract/download/{file}', 'LegalAspects\Contracs\ContractTrainingController@download');
      Route::post('trainingContract/data', 'LegalAspects\Contracs\ContractTrainingController@data');
      Route::ApiResource('trainingContract', 'LegalAspects\Contracs\ContractTrainingController');
      Route::post('trainingContract/switchStatus/{trainingContract}', 'LegalAspects\Contracs\ContractTrainingController@toggleState');
      Route::post('trainingContract/sendNotification/{trainingContract}', 'LegalAspects\Contracs\ContractTrainingController@sendNotification');
      Route::post('employeeContract/files', 'LegalAspects\Contracs\ContractEmployeeController@getFilesForm');
      Route::post('employeeContract/filesAprobe', 'LegalAspects\Contracs\FileUploadController@aproveFile');
      Route::post('employeeContract/data', 'LegalAspects\Contracs\ContractEmployeeController@data');
      Route::post('employeeContract/dataContract', 'LegalAspects\Contracs\ContractEmployeeController@dataContract');
      Route::ApiResource('employeeContract', 'LegalAspects\Contracs\ContractEmployeeController');
      Route::post('employeeContract/import', 'LegalAspects\Contracs\ContractEmployeeController@import');
      Route::post('listCheck/report', 'LegalAspects\Contracs\ListCheckReportController@data');
      Route::post('employeeDocument/report', 'LegalAspects\Contracs\ListCheckReportController@employeeDocument');
      Route::post('globalDocument/report', 'LegalAspects\Contracs\ListCheckReportController@globalDocument');      
      Route::post('trainigEmployeeDetails/report', 'LegalAspects\Contracs\ListCheckReportController@trainingEmployeeDetails');
      Route::post('trainigEmployeeConsolidated/report', 'LegalAspects\Contracs\ListCheckReportController@trainigEmployeeConsolidated');

      Route::post('inform/data', 'LegalAspects\Contracs\InformController@data');
      Route::ApiResource('inform', 'LegalAspects\Contracs\InformController');

      Route::ApiResource('informContract', 'LegalAspects\Contracs\InformContractController');
      Route::post('informContract/data', 'LegalAspects\Contracs\InformContractController@data');
      Route::get('informContract/getData/{informContract}', 'LegalAspects\Contracs\InformContractController@getData');
      Route::get('informContract/downloadFile/{informContractItemFile}', 'LegalAspects\Contracs\InformContractController@downloadFile');

      Route::get('informContract/downloadPdf/{informContract}', 'LegalAspects\Contracs\InformContractController@downloadPdf');

      Route::post('informContract/periodExist', 'LegalAspects\Contracs\InformContractController@periodExist');

      Route::post('informContract/historyItemQualification', 'LegalAspects\Contracs\InformContractController@historyItemQualification');

      Route::post('informContract/reportTableTotales', 'LegalAspects\Contracs\InformReportController@reportTableTotales');

      Route::post('informContract/reportLineItemQualification', 'LegalAspects\Contracs\InformReportController@reportLineItemQualification');

      Route::post('informContract/reportTablePorcentage', 'LegalAspects\Contracs\InformReportController@reportTableTotalesPorcentage');

      Route::post('informContract/reportLineItemPorcentege', 'LegalAspects\Contracs\InformReportController@reportLineItemPorcentage');

      Route::post('informContract/reportTablePorcentageGlobal', 'LegalAspects\Contracs\InformReportController@reportTableTotalesContractsPorcentage');

      Route::post('informContract/detailContractGlobal', 'LegalAspects\Contracs\InformReportController@detailContractGlobal');


      Route::post('configuration', 'LegalAspects\LegalMatrix\ConfigurationController@store');
      Route::get('configuration/view', 'LegalAspects\LegalMatrix\ConfigurationController@show');

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

        Route::post('type/data', 'LegalAspects\LegalMatrix\LawTypeController@data');
        Route::ApiResource('type', 'LegalAspects\LegalMatrix\LawTypeController');

        Route::post('systemApply/data', 'LegalAspects\LegalMatrix\SystemApplyController@data');
        Route::ApiResource('systemApply', 'LegalAspects\LegalMatrix\SystemApplyController');

        Route::get('law/downloadArticleQualify/{articleFulfillment}', 'LegalAspects\LegalMatrix\LawController@downloadArticleQualify');
        Route::get('law/download/{law}', 'LegalAspects\LegalMatrix\LawController@download');
        Route::get('law/showFile/{law}', 'LegalAspects\LegalMatrix\LawController@showFile');
        Route::get('law/qualify/{law}', 'LegalAspects\LegalMatrix\LawController@getArticlesQualification');
        Route::post('law/saveArticlesQualification', 'LegalAspects\LegalMatrix\LawController@saveArticlesQualification');
        Route::post('law/saveArticlesQualificationAlls', 'LegalAspects\LegalMatrix\LawController@saveArticlesQualificationAlls');
        Route::post('law/data', 'LegalAspects\LegalMatrix\LawController@data');
        Route::post('law/report', 'LegalAspects\LegalMatrix\LawReportController@data');
        Route::post('law/report/export', 'LegalAspects\LegalMatrix\LawReportController@export');
        Route::ApiResource('law', 'LegalAspects\LegalMatrix\LawController');
        Route::post('import', 'LegalAspects\LegalMatrix\LawController@import');

        Route::post('articleHistory/data', 'LegalAspects\LegalMatrix\ArticleHistoryController@data');
        Route::post('articleFulfillmentHistory/data', 'LegalAspects\LegalMatrix\ArticleFulfillmentHistoryController@data');
      });

      Route::get('document/download/{document}', 'LegalAspects\Documents\DocumentController@download');
      Route::post('document/data', 'LegalAspects\Documents\DocumentController@data');
      Route::ApiResource('document', 'LegalAspects\Documents\DocumentController');
    });
    
    //Sistema
    Route::prefix('system')->group(function () {

      Route::post('license/history/data', 'System\Licenses\LicenseHistoryController@data');
      Route::post('license/data', 'System\Licenses\LicenseController@data');
      Route::post('license/dataReasignation', 'System\Licenses\LicenseController@dataReasignacion');
      Route::get('license/getReasignar/{id}', 'System\Licenses\LicenseController@showReasignar');
      Route::post('license/reasignar', 'System\Licenses\LicenseController@saveReasignar');
      Route::post('license/recalculateEndedat', 'System\Licenses\LicenseController@updateEndedAt');
      Route::ApiResource('license', 'System\Licenses\LicenseController');
      Route::post('license/export', 'System\Licenses\LicenseController@export');
      Route::post('license/report', 'System\Licenses\LicenseController@report');
      Route::post('license/exportReport', 'System\Licenses\LicenseController@exportReport');

      Route::post('license/configuration', 'System\Licenses\ConfigurationController@store');
      Route::get('license/configuration/view', 'System\Licenses\ConfigurationController@show');

      Route::post('newsletterSend/data', 'System\NewsletterSend\NewsletterSendController@data');
      Route::ApiResource('newsletterSend', 'System\NewsletterSend\NewsletterSendController');
      Route::post('newsletterSend/switchStatus/{newsletter}', 'System\NewsletterSend\NewsletterSendController@toggleState');

      Route::get('newsletterSend/downloadImage/{id}', 'System\NewsletterSend\NewsletterSendController@download');

      Route::put('newsletterSend/program/{newsletter}', 'System\NewsletterSend\NewsletterSendController@programSend');
      Route::post('newsletterSend/saveRoles/', 'System\NewsletterSend\NewsletterSendController@saveRoles');
      Route::get('newsletterSend/configuration/view', 'System\NewsletterSend\NewsletterSendController@configurationView');
      Route::post('newsletterSend/data/opens', 'System\NewsletterSend\NewsletterSendController@reportOpensEmails');
      Route::post('newsletterSend/sendMailManual/{newsletter}', 'System\NewsletterSend\NewsletterSendController@sendMailManual');

      Route::post('logMail/data', 'System\LogMails\LogMailController@data');
      Route::ApiResource('logMail', 'System\LogMails\LogMailController');

      Route::post('label/data', 'System\Labels\LabelController@data');
      Route::ApiResource('label', 'System\Labels\LabelController');   

      Route::post('company/data', 'System\Companies\CompanyController@data');
      Route::ApiResource('company', 'System\Companies\CompanyController');  
      Route::post('company/switchStatus/{company}', 'System\Companies\CompanyController@toggleState');
      Route::post('companyGroup/data', 'System\CompanyGroup\CompanyController@data');
      Route::ApiResource('companyGroup', 'System\CompanyGroup\CompanyController');  
      Route::post('companyGroup/switchStatus/{company}', 'System\CompanyGroup\CompanyController@toggleState');
      Route::post('customermonitoring/dataReinstatements', 'System\CustomerMonitoring\CustomerMonitoringController@dataReinstatements');
      Route::post('customermonitoring/dataDangerousConditions', 'System\CustomerMonitoring\CustomerMonitoringController@dataDangerousConditions');
      Route::post('customermonitoring/dataAutomaticsSend', 'System\CustomerMonitoring\CustomerMonitoringController@dataAutomaticsSend');
      Route::post('customermonitoring/dataAbsenteeism', 'System\CustomerMonitoring\CustomerMonitoringController@dataAbsenteeism');
      Route::post('customermonitoring/dataDangerMatrix', 'System\CustomerMonitoring\CustomerMonitoringController@dataDangerMatrix');
      Route::post('customermonitoring/dataRiskMatrix', 'System\CustomerMonitoring\CustomerMonitoringController@dataRiskMatrix');
      Route::post('customermonitoring/dataContract', 'System\CustomerMonitoring\CustomerMonitoringController@dataContract');
      Route::post('customermonitoring/dataLegalMatrix', 'System\CustomerMonitoring\CustomerMonitoringController@dataLegalMatrix');
      Route::post('customermonitoring/dataEpp', 'System\CustomerMonitoring\CustomerMonitoringController@dataEpp');
      Route::ApiResource('notification', 'System\CustomerMonitoring\CustomerMonitoringController');      

      Route::post('usersCompanies/data', 'System\UsersCompanies\UserCompanyController@data');
      Route::post('usersCompanies/export', 'System\UsersCompanies\UserCompanyController@export');
    });


    //Return view for spa
    Route::get('/{any}', 'General\ApplicationController@index')->where('any', '.*');
});