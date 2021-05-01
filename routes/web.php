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

Route::prefix('training')->group(function () {
  Route::get('{training}/{token}', 'LegalAspects\Contracs\TrainingEmployeeController@index');
  Route::post('response', 'LegalAspects\Contracs\TrainingEmployeeController@saveTraining');
  Route::get('download/file/{id}', 'LegalAspects\Contracs\TrainingEmployeeController@download');
});

Route::middleware(['auth'])->group(function () { 
    Route::get('appWithModules', 'General\ApplicationController@appsWhithModules');
    Route::get('getCompanies', 'General\ApplicationController@getCompanies');
    Route::post('changeCompany', 'General\ApplicationController@changeCompany');
    Route::post('vuetableCustomColumns', 'General\ApplicationController@vuetableCustomColumns');
    Route::post('setStateFilters', 'General\ApplicationController@setStateFilters');
    Route::post('getStateFilters', 'General\ApplicationController@getStateFilters');    
    Route::post('userActivity', 'General\ApplicationController@userActivity');
    Route::get('get_terms_conditions', 'General\ApplicationController@getTermsConditionsUsers');
    Route::post('accept_terms_conditions', 'General\ApplicationController@accepTermsConditionsUsers');

    Route::prefix('configurableForm')->group(function () {
      Route::post('formModel', 'General\ConfigurableFormControlle@formModel');
      Route::post('selectOptions', 'General\ConfigurableFormControlle@selectOptions');
    });

    Route::get('templates/audiometryimport','PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryController@downloadTemplateImport');
    Route::get('templates/employeeimport','Administrative\Employees\EmployeesController@downloadTemplateImport');    
    Route::get('templates/dangermatriximport','IndustrialSecure\DangerMatrix\DangerMatrixController@downloadTemplateImport');
    Route::get('templates/contractimport','LegalAspects\Contracs\ContractLesseeController@downloadTemplateImport');
    Route::get('templates/legalmatriximport','LegalAspects\LegalMatrix\LawController@downloadTemplateImport');    
    Route::get('templates/usersimport','Administrative\Users\UserController@downloadTemplateImport');    
    Route::get('templates/contractemployeeimport','LegalAspects\Contracs\ContractEmployeeController@downloadTemplateImport');

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

          Route::post('check/export', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@export');
          Route::get('check/generateLetter', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@generateLetter')->name('checks.generateLetter');
          Route::get('check/generateTracing', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@generateTracing')->name('checks.generateTracing');
          Route::get('check/downloadOriginFile/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadOriginFile');
          Route::get('check/downloadPclFile/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadPclFile');
          Route::post('check/data', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@data');
          Route::ApiResource('check', 'PreventiveOccupationalMedicine\Reinstatements\CheckController');
          Route::post('check/informs', 'PreventiveOccupationalMedicine\Reinstatements\CheckInformController@data');
          Route::put('check/switchStatus/{check}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@toggleState');
          Route::post('check/tracingOthers', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@tracingOthers');
          Route::get('check/downloadFile/{file}', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@downloadFile');

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
        });
    });
    
    Route::prefix('selects')->group(function () {
        Route::post('employees', 'Administrative\Employees\EmployeesController@multiselect');
        Route::post('employeesDeal', 'Administrative\Employees\EmployeesController@multiselectDeal');  
        Route::post('employeesNames', 'Administrative\Employees\EmployeesController@multiselectNames');
        Route::post('employeesIdentifications', 'Administrative\Employees\EmployeesController@multiselectIdentifications');
        Route::post('users', 'Administrative\Users\UserController@multiselect');
        Route::post('usersActionPlan', 'Administrative\Users\UserController@multiselectUsersActionPlan');
        Route::post('usersOtherCompany', 'Administrative\Users\UserController@multiselectUsers');
        Route::post('usersAutomaticSend', 'Administrative\Users\UserController@multiselectUsersAutomaticSend');
        Route::post('responsiblesFilter', 'Administrative\ActionPlans\ActionPlanController@multiselectResponsiblesFilter');  
        Route::post('multiselect', 'General\ApplicationController@multiselect');
        Route::post('roles', 'Administrative\Roles\RoleController@multiselect');
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
        Route::post('regionals', 'Administrative\Regionals\EmployeeRegionalController@multiselect');
        Route::post('headquarters', 'Administrative\Headquarters\EmployeeHeadquarterController@multiselect');  
        Route::post('sexs', 'General\MultiSelectRadioController@sexs');    
        Route::post('typesDocument', 'General\MultiSelectRadioController@typesDocumentContract');        
        Route::post('days', 'General\MultiSelectRadioController@days');  
        Route::post('processes', 'Administrative\Processes\EmployeeProcessController@multiselect');
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
        Route::post('multiselectBarReports', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@multiselectBar');
        Route::post('multiselectBarLegalMatrix', 'LegalAspects\LegalMatrix\LawReportController@multiselectBar');
        Route::post('multiselectBarPercentage', 'PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryInformController@multiselectBarPercentage');
        Route::post('dmActivities', 'IndustrialSecure\Activities\ActivityController@multiselect');
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
        Route::post('tagsSubstitution', 'IndustrialSecure\Tags\TagController@multiselectSubstitution');
        Route::post('tagsParticipants', 'IndustrialSecure\Tags\TagController@multiselectSubstitution');
        Route::post('tagsRmParticipants', 'IndustrialSecure\RiskMatrix\Tags\ParticipantsController@multiselect');
        Route::post('tagsDangerDescription', 'IndustrialSecure\Tags\TagController@multiselectDangerDescription');
        Route::post('actionPlanStates/{all?}', 'General\MultiSelectRadioController@actionPlanStates');
        Route::post('actionPlanModules', 'Administrative\ActionPlans\ActionPlanController@actionPlanModules');
        Route::post('contractors', 'LegalAspects\Contracs\ContractLesseeController@multiselect');
        Route::post('contractStandar', 'LegalAspects\Contracs\ContractLesseeController@multiselectStandard');
        Route::post('ctRoles', 'General\MultiSelectRadioController@ctRoles');
        Route::post('ctContractClassifications', 'General\MultiSelectRadioController@ctContractClassifications'); 
        Route::post('ctkindsRisks', 'General\MultiSelectRadioController@ctkindsRisks'); 
        Route::post('siNo', 'General\MultiSelectRadioController@siNoSelect');
        Route::post('companies', 'General\ApplicationController@multiselectCompanies');
        Route::post('reincYears', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectYears');
        Route::post('reincSveAssociateds', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectSveAssociateds');
        Route::post('reincMedicalCertificates', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectMedicalCertificates');
        Route::post('reincRelocatedTypes', 'PreventiveOccupationalMedicine\Reinstatements\CheckController@multiselectRelocatedTypes');
        Route::post('dmReportMultiselect', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@multiselect');
        Route::post('dmReportMonths', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@multiselect');
        Route::post('reportDinamic/years', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@multiselectYears');
          Route::post('reportDinamic/months', 'IndustrialSecure\DangerousConditions\Reports\ReportInformController@multiselectMounts');

        Route::prefix('evaluations')->group(function () {
          Route::post('evaluations', 'LegalAspects\Contracs\EvaluationController@multiselectEvaluations');
          Route::post('objectives', 'LegalAspects\Contracs\EvaluationController@multiselectObjectives');
          Route::post('subobjectives', 'LegalAspects\Contracs\EvaluationController@multiselectSubobjectives');
          Route::post('items', 'LegalAspects\Contracs\EvaluationController@multiselectItems');
          Route::post('qualificationTypes', 'LegalAspects\Contracs\TypeRatingController@multiselect');
          Route::post('years', 'LegalAspects\Contracs\EvaluationContractController@multiselectYears');
          Route::post('months', 'LegalAspects\Contracs\EvaluationContractController@multiselectMounts');
        });

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
          Route::post('entitiesSystem', 'LegalAspects\LegalMatrix\EntityController@multiselectSystem');
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
          Route::post('usersCompany', 'System\Companies\CompanyController@multiselectUsers');
          Route::post('rolesCompany', 'System\Companies\CompanyController@multiselectRoles');
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
      Route::post('users/filtersConfig', 'Administrative\Users\UserController@filtersConfig');
			Route::post('users/data', 'Administrative\Users\UserController@data');
      Route::post('users/export', 'Administrative\Users\UserController@export');
      Route::post('users/import', 'Administrative\Users\UserController@import');
      Route::post('users/changePassword', 'Administrative\Users\UserController@changePassword');
      Route::get('users/myDefaultModule', 'Administrative\Users\UserController@myDefaultModule');
      Route::post('users/defaultModule', 'Administrative\Users\UserController@defaultModule');
      Route::post('users/addUserOtherCompany', 'Administrative\Users\UserController@addUserOtherCompany');
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

      Route::post('employee/import', 'Administrative\Employees\EmployeesController@import');
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
      Route::delete('actionplan/deleteActivity/{id}', 'Administrative\ActionPlans\ActionPlanController@destroy');
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
      Route::post('dangersMatrix/getfieldsadd', 'IndustrialSecure\DangerMatrix\DangerMatrixController@getFieldAdd');
      Route::post('dangersMatrix/addFields', 'IndustrialSecure\DangerMatrix\DangerMatrixController@saveFields');
      Route::post('dangersMatrix/getfields', 'IndustrialSecure\DangerMatrix\DangerMatrixController@getAdditionalFiels');
      Route::post('dangersMatrix/report', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@report');
      Route::post('dangersMatrix/reportDangerTable', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportDangerTable');
      Route::post('dangersMatrix/reportExport ', 'IndustrialSecure\DangerMatrix\DangerMatrixReportController@reportExport');
      Route::post('dangersMatrix/reportHistory', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@report');
      Route::post('dangersMatrix/reportHistoryExport ', 'IndustrialSecure\DangerMatrix\DangerMatrixReportHistoryController@reportExport');
      Route::get('dangersMatrix/download/{dangersMatrix}', 'IndustrialSecure\DangerMatrix\DangerMatrixController@download');
      Route::post('dangersMatrix/import', 'IndustrialSecure\DangerMatrix\DangerMatrixController@import');
      Route::ApiResource('dangersMatrix', 'IndustrialSecure\DangerMatrix\DangerMatrixController');

      Route::post('dangersMatrixHistory/data', 'IndustrialSecure\DangerMatrix\DangerMatrixHistoryController@data');

      Route::post('risksMatrix/data', 'IndustrialSecure\RiskMatrix\RiskMatrixController@data');
      Route::ApiResource('risksMatrix', 'IndustrialSecure\RiskMatrix\RiskMatrixController');

      Route::post('subProcess/data', 'IndustrialSecure\RiskMatrix\SubProcessController@data');
      Route::ApiResource('subProcess', 'IndustrialSecure\RiskMatrix\SubProcessController');

      Route::post('risk/data', 'IndustrialSecure\RiskMatrix\RiskController@data');
      Route::ApiResource('risk', 'IndustrialSecure\RiskMatrix\RiskController');

      Route::prefix('dangerousConditions')->group(function () {
        Route::get('incentive/download', 'IndustrialSecure\DangerousConditions\IncentiveController@download');
        Route::get('incentive/view', 'IndustrialSecure\DangerousConditions\IncentiveController@show');
        Route::post('incentive', 'IndustrialSecure\DangerousConditions\IncentiveController@store');
        Route::post('inspection/switchStatus/{inspection}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@toggleState');
        Route::post('inspection/data', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@data');
        Route::post('inspection/reportDinamic', 'IndustrialSecure\DangerousConditions\Inspections\InspectionReportController@reportDinamic');
        Route::ApiResource('inspection', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController');

        Route::get('inspection/downloadPdf/{id}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@downloadPdf');
        
        Route::get('inspection/qualification/downloadImage/{id}/{column}', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@downloadImage');
        Route::post('inspection/qualification/saveImage', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@saveImage');
        Route::post('inspection/qualification/saveQualification', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@saveQualification');
        Route::post('inspection/qualification/data', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController@data');
        Route::ApiResource('inspection/qualification', 'IndustrialSecure\DangerousConditions\Inspections\InspectionQualificationController');
        Route::post('inspection/export', 'IndustrialSecure\DangerousConditions\Inspections\InspectionController@export');
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

      Route::prefix('tags')->group(function () {
        Route::post('administrativeControls/data', 'IndustrialSecure\Tags\AdministrativeControlsController@data');
        Route::ApiResource('administrativeControls', 'IndustrialSecure\Tags\AdministrativeControlsController')->only('destroy');
        Route::post('engineeringControls/data', 'IndustrialSecure\Tags\EngineeringControlsController@data');
        Route::ApiResource('engineeringControls', 'IndustrialSecure\Tags\EngineeringControlsController')->only('destroy');
        Route::post('epp/data', 'IndustrialSecure\Tags\EppController@data');
        Route::ApiResource('epp', 'IndustrialSecure\Tags\EppController')->only('destroy');
        Route::post('possibleConsequencesDanger/data', 'IndustrialSecure\Tags\PossibleConsequencesDangerController@data');
        Route::ApiResource('possibleConsequencesDanger', 'IndustrialSecure\Tags\PossibleConsequencesDangerController')->only('destroy');
        Route::post('warningSignage/data', 'IndustrialSecure\Tags\WarningSignageController@data');
        Route::ApiResource('warningSignage', 'IndustrialSecure\Tags\WarningSignageController')->only('destroy');
        Route::post('substitution/data', 'IndustrialSecure\Tags\SubstitutionController@data');
        Route::ApiResource('substitution', 'IndustrialSecure\Tags\SubstitutionController')->only('destroy');
        Route::post('participants/data', 'IndustrialSecure\Tags\ParticipantsController@data');
        Route::ApiResource('participants', 'IndustrialSecure\Tags\ParticipantsController')->only('destroy');
        Route::post('dangerDescription/data', 'IndustrialSecure\Tags\DangerDescriptionController@data');
        Route::ApiResource('dangerDescription', 'IndustrialSecure\Tags\DangerDescriptionController')->only('destroy');
      });
		});
		
		//Aspectos Legales
		Route::prefix('legalAspects')->group(function () {
      Route::post('contracts/data', 'LegalAspects\Contracs\ContractLesseeController@data');
      Route::get('contracts/getInformation', 'LegalAspects\Contracs\ContractLesseeController@getInformation');
      Route::post('contracts/getListCheckItems', 'LegalAspects\Contracs\ContractLesseeController@getListCheckItems');
      Route::post('contracts/getItemValidation', 'LegalAspects\Contracs\ContractLesseeController@getListCheckItemsValidations');
      Route::post('contracts/saveValidations', 'LegalAspects\Contracs\ContractLesseeController@saveItemsStandard');
      Route::get('contracts/qualifications', 'LegalAspects\Contracs\ContractLesseeController@qualifications');
      Route::post('contracts/saveQualificationItems', 'LegalAspects\Contracs\ContractLesseeController@saveQualificationItems');
      Route::post('contractsListCheckHistory/data', 'LegalAspects\Contracs\ListCheckHistoryController@data');
      Route::post('contracts/retrySendMail/{contract}', 'LegalAspects\Contracs\ContractLesseeController@retrySendMail');
      Route::post('contracts/listCheckCopy', 'LegalAspects\Contracs\ContractLesseeController@listCheckCopy');
      Route::post('contracts/export', 'LegalAspects\Contracs\ContractLesseeController@export');
      Route::post('contracts/import', 'LegalAspects\Contracs\ContractLesseeController@import');
      Route::ApiResource('contracts', 'LegalAspects\Contracs\ContractLesseeController');
      Route::post('contracts/saveDocuments', 'LegalAspects\Contracs\ContractLesseeController@saveDocuments');
      Route::post('contracts/getDocuments', 'LegalAspects\Contracs\ContractLesseeController@getDocuments');

      Route::ApiResource('listCheck', 'LegalAspects\Contracs\ListCheckQualificationController');
      Route::post('listCheck/data', 'LegalAspects\Contracs\ListCheckQualificationController@data');
      Route::post('listCheck/getListCheckItems', 'LegalAspects\Contracs\ListCheckQualificationController@getListCheckItems');
      Route::post('listCheck/saveQualificationItems', 'LegalAspects\Contracs\ListCheckQualificationController@saveQualificationItems');
      Route::post('listCheck/listCheckCopy', 'LegalAspects\Contracs\ListCheckQualificationController@listCheckItemsClone');
      Route::get('listCheck/downloadPdf/{listCheck}', 'LegalAspects\Contracs\ListCheckQualificationController@downloadPdf');
      Route::post('listCheck/switchStatus/{trainingContract}', 'LegalAspects\Contracs\ListCheckQualificationController@toggleState');

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
      Route::post('employeeContract/data', 'LegalAspects\Contracs\ContractEmployeeController@data');
      Route::ApiResource('employeeContract', 'LegalAspects\Contracs\ContractEmployeeController');
      Route::post('employeeContract/import', 'LegalAspects\Contracs\ContractEmployeeController@import');
      Route::post('listCheck/report', 'LegalAspects\Contracs\ListCheckReportController@data');
      Route::post('employeeDocument/report', 'LegalAspects\Contracs\ListCheckReportController@employeeDocument');
      Route::post('globalDocument/report', 'LegalAspects\Contracs\ListCheckReportController@globalDocument');      
      Route::post('trainigEmployeeDetails/report', 'LegalAspects\Contracs\ListCheckReportController@trainingEmployeeDetails');
      Route::post('trainigEmployeeConsolidated/report', 'LegalAspects\Contracs\ListCheckReportController@trainigEmployeeConsolidated');

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
        Route::post('law/saveArticlesQualificationAlls', 'LegalAspects\LegalMatrix\LawController@saveArticlesQualificationAlls');
        Route::post('law/data', 'LegalAspects\LegalMatrix\LawController@data');
        Route::post('law/report', 'LegalAspects\LegalMatrix\LawReportController@data');
        Route::post('law/report/export', 'LegalAspects\LegalMatrix\LawReportController@export');
        Route::ApiResource('law', 'LegalAspects\LegalMatrix\LawController');
        Route::post('import', 'LegalAspects\LegalMatrix\LawController@import');

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

      Route::post('company/data', 'System\Companies\CompanyController@data');
      Route::ApiResource('company', 'System\Companies\CompanyController');  
      Route::post('company/switchStatus/{company}', 'System\Companies\CompanyController@toggleState');
      Route::post('customermonitoring/dataReinstatements', 'System\CustomerMonitoring\CustomerMonitoringController@dataReinstatements');
      Route::post('customermonitoring/dataDangerousConditions', 'System\CustomerMonitoring\CustomerMonitoringController@dataDangerousConditions');
      Route::post('customermonitoring/dataAutomaticsSend', 'System\CustomerMonitoring\CustomerMonitoringController@dataAutomaticsSend');
      Route::post('customermonitoring/dataAbsenteeism', 'System\CustomerMonitoring\CustomerMonitoringController@dataAbsenteeism');
      Route::post('customermonitoring/dataDangerMatrix', 'System\CustomerMonitoring\CustomerMonitoringController@dataDangerMatrix');
      Route::post('customermonitoring/dataContract', 'System\CustomerMonitoring\CustomerMonitoringController@dataContract');
      Route::post('customermonitoring/dataLegalMatrix', 'System\CustomerMonitoring\CustomerMonitoringController@dataLegalMatrix');
      Route::ApiResource('notification', 'System\CustomerMonitoring\CustomerMonitoringController');      

      Route::post('usersCompanies/data', 'System\UsersCompanies\UserCompanyController@data');
      Route::post('usersCompanies/export', 'System\UsersCompanies\UserCompanyController@export');
    });


    //Return view for spa
    Route::get('/{any}', 'General\ApplicationController@index')->where('any', '.*');
});