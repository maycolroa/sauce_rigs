import LayoutModules from "@/views/layoutModules";
import Home from "@/views/LegalAspects/home";
import { middleware } from 'vue-router-middleware'

export default [
	{
		path: "/legalaspects",
		component: LayoutModules,
		children: [
			{
				path: "",
				name: "legalaspects",
				component: Home
			},
			{
				name: 'legalaspects-contracts',
				path: 'contracts',
				component: () => import('@/views/LegalAspects/contracts/index')
			},
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
					name: 'legalaspects-contractor',
					path: 'contractor',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
					name: 'legalaspects-contracts-configurations',
					path: 'contractor/configurations',
					component: () =>
					import('@/views/LegalAspects/contracts/indexConfiguration')
				}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'legalaspects-contractor-create',
					path: 'contractor/create',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_u' }, [
				{
					name: 'legalaspects-contractor-edit',
					path: 'contractor/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
					name: 'legalaspects-contractor-view',
					path: 'contractor/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_myInformation' }, [
				{
					name: 'legalaspects-contracts-information',
					path: 'contracts/information',
					component: () => import('@/views/LegalAspects/contracts/contract/information')
				}
			]),
			...middleware({ 'check-permission': 'contracts_view_list_standards' }, [
				{
					name: 'legalaspects-contracts-list-check-items',
					path: 'contracts/list-check-items',
					component: () => import('@/views/LegalAspects/contracts/contract/listCheckItems')
				}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'legalaspects-contractor-list-check-validation',
					path: 'contracts/list-check-validation',
					component: () => import('@/views/LegalAspects/contracts/contractor/listCheckValidation')
				}
			]),
			...middleware({ 'check-permission': 'contracts_view_list_standards' }, [
				{
					name: 'legalaspects-contracts-view-list-check',
					path: 'contracts/view-list-check/:id',
					component: () => import('@/views/LegalAspects/contracts/contract/listCheckItems')
				}
			]),
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
					name: 'legalaspects-contracts-report',
					path: 'report/contracts',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/reportStandar')
				}
			]),
			...middleware({ 'check-permission': 'contracts_typesQualification_r' }, [
				{
					name: 'legalaspects-typesrating',
					path: 'typesrating',
					component: () =>
					import('@/views/LegalAspects/contracts/typesrating/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_typesQualification_c' }, [
				{
					name: 'legalaspects-typesrating-create',
					path: 'typesrating/create',
					component: () =>
					import('@/views/LegalAspects/contracts/typesrating/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_typesQualification_u' }, [
				{
					name: 'legalaspects-typesrating-edit',
					path: 'typesrating/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/typesrating/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_typesQualification_r' }, [
				{
					name: 'legalaspects-typesrating-view',
					path: 'typesrating/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/typesrating/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_r' }, [
				{
					name: 'legalaspects-evaluations',
					path: 'evaluations',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluations/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_c' }, [
				{
					name: 'legalaspects-evaluations-create',
					path: 'evaluations/create',
					component: () => import('@/views/LegalAspects/contracts/evaluations/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_c' }, [
				{
					name: 'legalaspects-evaluations-clone',
					path: 'evaluations/clone',
					component: () => import('@/views/LegalAspects/contracts/evaluations/clone')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_u' }, [
				{
					name: 'legalaspects-evaluations-edit',
					path: 'evaluations/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluations/edit')
				},
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_r' }, [
				{
					name: 'legalaspects-evaluations-view',
					path: 'evaluations/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluations/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_perform_evaluation' }, [
				{
					name: 'legalaspects-evaluations-evaluate',
					path: 'evaluations/evaluate/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_perform_evaluation' }, [
				{
					name: 'legalaspects-evaluations-contracts-clone',
					path: 'evaluations/contracts/clone',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/clone')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_view_evaluations_made' }, [
				{
					name: 'legalaspects-evaluations-lessee',
					path: 'evaluations/contracts',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/index')
				},
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_view_evaluations_made' }, [
				{
					name: 'legalaspects-evaluations-contracts',
					path: 'evaluations/contracts/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_view_evaluations_made' }, [
				{
					name: 'legalaspects-evaluations-contracts-view',
					path: 'evaluations/contracts/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_edit_evaluations_made' }, [
				{
					name: 'legalaspects-evaluations-contracts-edit',
					path: 'evaluations/contracts/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_evaluations_report_view' }, [
				{
					name: 'legalaspects-evaluations-report',
					path: 'evaluations/report',
					component: () =>
					import('@/views/LegalAspects/contracts/evaluationContracts/report')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_r' }, [
				{
					name: 'legalaspects-upload-files',
					path: 'upload-files',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_r' }, [
				{
					name: 'legalaspects-menu-upload-files',
					path: 'upload-files',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/indexMenu')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_c' }, [
				{
					name: 'legalaspects-upload-files-create',
					path: 'upload-files/create',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_u' }, [
				{
					name: 'legalaspects-upload-files-edit',
					path: 'upload-files/edit/:id',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_r' }, [
				{
					name: 'legalaspects-upload-files-view',
					path: 'upload-files/view/:id',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_r' }, [
				{
					name: 'legalaspects-upload-files-report',
					path: 'upload-files/report',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/report')
				}
			]),
			...middleware({ 'check-permission': 'contracts_uploadFiles_r' }, [
				{
					name: 'legalaspects-upload-files-merge',
					path: 'upload-files/filesMerge',
					component: () => import('@/views/LegalAspects/contracts/uploadFiles/fileMerge')
				}
			]),
			...middleware({ 'check-permission': 'contracts_activities_r' }, [
				{
					name: 'legalaspects-contracts-activities',
					path: 'activities',
					component: () =>
					import('@/views/LegalAspects/contracts/activities/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_activities_c' }, [
				{
					name: 'legalaspects-contracts-activities-create',
					path: 'activities/create',
					component: () =>
					import('@/views/LegalAspects/contracts/activities/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_activities_u' }, [
				{
					name: 'legalaspects-contracts-activities-edit',
					path: 'activities/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/activities/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_activities_r' }, [
				{
					name: 'legalaspects-contracts-activities-view',
					path: 'activities/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/activities/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_employee_r' }, [
				{
					name: 'legalaspects-contracts-employees',
					path: 'employees',
					component: () =>
					import('@/views/LegalAspects/contracts/employees/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
					name: 'legalaspects-contracts-employees-view-contract',
					path: 'employees/view/contract/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/employees/indexContract')
				}
			]),
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
					name: 'legalaspects-contracts-employees-view-contract-view',
					path: 'employees/view/contract/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/employees/viewContract')
				}
			]),
			...middleware({ 'check-permission': 'contracts_employee_c' }, [
				{
					name: 'legalaspects-contracts-employees-create',
					path: 'employees/create',
					component: () =>
					import('@/views/LegalAspects/contracts/employees/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_employee_u' }, [
				{
					name: 'legalaspects-contracts-employees-edit',
					path: 'employees/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/employees/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_employee_r' }, [
				{
					name: 'legalaspects-contracts-employees-view',
					path: 'employees/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/employees/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_employee_u' }, [
				{
				  name: 'legalaspects-contracts-employees-switchStatus',
				  path: 'legalaspects/contracts/employees/switchStatus/:id',
				  component: () =>
					import('@/views/LegalAspects/contracts/employees/switchStatus')
				}
			  ]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'legalaspects-contracts-documents',
					path: 'contracts/documents',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/documentsRequest')
				}
			]),
			...middleware({ 'check-permission': 'contracts_training_r' }, [
				{
					name: 'legalaspects-contracts-trainings',
					path: 'trainings',
					component: () =>
					import('@/views/LegalAspects/contracts/trainings/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_training_r' }, [
				{
					name: 'legalaspects-contracts-trainings-virtual',
					path: 'trainings/virtual',
					component: () =>
					import('@/views/LegalAspects/contracts/trainings/virtual/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_training_c' }, [
				{
					name: 'legalaspects-contracts-trainings-virtual-create',
					path: 'trainings/virtual/create',
					component: () =>
					import('@/views/LegalAspects/contracts/trainings/virtual/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_training_u' }, [
				{
					name: 'legalaspects-contracts-trainings-virtual-edit',
					path: 'trainings/virtual/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/trainings/virtual/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_training_r' }, [
				{
					name: 'legalaspects-contracts-trainings-virtual-view',
					path: 'trainings/virtual/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/trainings/virtual/view')
				}
			]),
			{
				name: 'legalaspects-legalmatrix',
				path: 'legalmatrix',
				component: () => import('@/views/LegalAspects/legalMatrix/index')
			},
			...middleware({ 'check-permission': 'interests_r' }, [
				{
					name: 'legalaspects-lm-interest',
					path: 'lm/interests',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/index')
				}, 
			]),
			...middleware({ 'check-permission': 'interests_c' }, [
				{
					name: 'legalaspects-lm-interest-create',
					path: 'lm/interests/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/create')
				},
			]),
			...middleware({ 'check-permission': 'interests_u' }, [
				{
					name: 'legalaspects-lm-interest-edit',
					path: 'lm/interests/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/edit')
				},
			]),
			...middleware({ 'check-permission': 'interests_r' }, [
				{
					name: 'legalaspects-lm-interest-view',
					path: 'lm/interests/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/view')
				},
			]),
			...middleware({ 'check-permission': 'interestsCustom_r' }, [
				{
					name: 'legalaspects-lm-interest-company',
					path: 'lm/interestsCompany',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/indexCompany')
				}, 
			]),
			...middleware({ 'check-permission': 'interestsCustom_c' }, [
				{
					name: 'legalaspects-lm-interest-company-create',
					path: 'lm/interestsCompany/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/createCompany')
				},
			]),
			...middleware({ 'check-permission': 'interestsCustom_u' }, [
				{
					name: 'legalaspects-lm-interest-company-edit',
					path: 'lm/interestsCompany/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/edit')
				},
			]),
			...middleware({ 'check-permission': 'interestsCustom_r' }, [
				{
					name: 'legalaspects-lm-interest-company-view',
					path: 'lm/interestsCompany/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/view')
				},
			]),
			...middleware({ 'check-permission': 'interests_config' }, [
				{
					name: 'legalaspects-lm-interest-myinterests',
					path: 'lm/interests/myinterests',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/interests/myInterests')
				},
			]),
			...middleware({ 'check-permission': 'risksAspects_r' }, [
				{
					name: 'legalaspects-lm-riskaspect',
					path: 'lm/riskaspects',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/riskAspects/index')
				}, 
			]),
			...middleware({ 'check-permission': 'risksAspects_c' }, [
				{
					name: 'legalaspects-lm-riskaspect-create',
					path: 'lm/riskaspects/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/riskAspects/create')
				},
			]),
			...middleware({ 'check-permission': 'risksAspects_u' }, [
				{
					name: 'legalaspects-lm-riskaspect-edit',
					path: 'lm/riskaspects/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/riskAspects/edit')
				},
			]),
			...middleware({ 'check-permission': 'risksAspects_r' }, [
				{
					name: 'legalaspects-lm-riskaspect-view',
					path: 'lm/riskaspects/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/riskAspects/view')
				},
			]),
			...middleware({ 'check-permission': 'sstRisks_r' }, [
				{
					name: 'legalaspects-lm-sstrisk',
					path: 'lm/sstrisks',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/sstRisk/index')
				}, 
			]),
			...middleware({ 'check-permission': 'sstRisks_c' }, [
				{
					name: 'legalaspects-lm-sstrisk-create',
					path: 'lm/sstrisks/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/sstRisk/create')
				},
			]),
			...middleware({ 'check-permission': 'sstRisks_u' }, [
				{
					name: 'legalaspects-lm-sstrisk-edit',
					path: 'lm/sstrisks/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/sstRisk/edit')
				},
			]),
			...middleware({ 'check-permission': 'sstRisks_r' }, [
				{
					name: 'legalaspects-lm-sstrisk-view',
					path: 'lm/sstrisks/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/sstRisk/view')
				},
			]),
			...middleware({ 'check-permission': 'entities_r' }, [
				{
					name: 'legalaspects-lm-entity',
					path: 'lm/entities',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/index')
				}, 
			]),
			...middleware({ 'check-permission': 'entities_c' }, [
				{
					name: 'legalaspects-lm-entity-create',
					path: 'lm/entities/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/create')
				},
			]),
			...middleware({ 'check-permission': 'entities_u' }, [
				{
					name: 'legalaspects-lm-entity-edit',
					path: 'lm/entities/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/edit')
				},
			]),
			...middleware({ 'check-permission': 'entities_r' }, [
				{
					name: 'legalaspects-lm-entity-view',
					path: 'lm/entities/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/view')
				},
			]),
			...middleware({ 'check-permission': 'entitiesCustom_r' }, [
				{
					name: 'legalaspects-lm-entity-company',
					path: 'lm/entityCompany',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/indexCompany')
				}, 
			]),
			...middleware({ 'check-permission': 'entitiesCustom_c' }, [
				{
					name: 'legalaspects-lm-entity-company-create',
					path: 'lm/entityCompany/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/createCompany')
				},
			]),
			...middleware({ 'check-permission': 'entitiesCustom_u' }, [
				{
					name: 'legalaspects-lm-entity-company-edit',
					path: 'lm/entityCompany/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/edit')
				},
			]),
			...middleware({ 'check-permission': 'entitiesCustom_r' }, [
				{
					name: 'legalaspects-lm-entity-company-view',
					path: 'lm/entityCompany/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/entities/view')
				}
			]),
			...middleware({ 'check-permission': 'laws_r' }, [
				{
					name: 'legalaspects-lm-law',
					path: 'lm/laws',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/index')
				}, 
			]),
			...middleware({ 'check-permission': 'laws_c' }, [
				{
					name: 'legalaspects-lm-law-create',
					path: 'lm/laws/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/create')
				},
			]),
			...middleware({ 'check-permission': 'laws_u' }, [
				{
					name: 'legalaspects-lm-law-edit',
					path: 'lm/laws/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/edit')
				},
			]),
			...middleware({ 'check-permission': 'laws_r' }, [
				{
					name: 'legalaspects-lm-law-view',
					path: 'lm/laws/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/view')
				},
			]),
			...middleware({ 'check-permission': 'lawsCustom_r' }, [
				{
					name: 'legalaspects-lm-law-company',
					path: 'lm/lawsCompany',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/indexCompany')
				}, 
			]),
			...middleware({ 'check-permission': 'lawsCustom_c' }, [
				{
					name: 'legalaspects-lm-law-company-create',
					path: 'lm/lawsCompany/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/createCompany')
				},
			]),
			...middleware({ 'check-permission': 'lawsCustom_u' }, [
				{
					name: 'legalaspects-lm-law-company-edit',
					path: 'lm/lawsCompany/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/editCompany')
				},
			]),
			...middleware({ 'check-permission': 'lawsCustom_r' }, [
				{
					name: 'legalaspects-lm-law-company-view',
					path: 'lm/lawsCompany/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/view')
				},
			]),
			...middleware({ 'check-permission': ['laws_qualify', 'laws_qualify_view'] }, [
				{
					name: 'legalaspects-lm-law-qualify',
					path: 'lm/lawsQualify',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/indexQualify')
				},
				{
					name: 'legalaspects-lm-law-qualify-view',
					path: 'lm/lawsQualify/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/laws/viewQualify')
				}
			]),
			...middleware({ 'check-permission': 'systemApply_r' }, [
				{
					name: 'legalaspects-lm-system-apply',
					path: 'lm/systemApply',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/index')
				}, 
			]),
			...middleware({ 'check-permission': 'systemApply_c' }, [
				{
					name: 'legalaspects-lm-system-apply-create',
					path: 'lm/systemApply/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/create')
				},
			]),
			...middleware({ 'check-permission': 'systemApply_u' }, [
				{
					name: 'legalaspects-lm-system-apply-edit',
					path: 'lm/systemApply/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/edit')
				},
			]),
			...middleware({ 'check-permission': 'systemApply_r' }, [
				{
					name: 'legalaspects-lm-system-apply-view',
					path: 'lm/systemApply/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/view')
				},
			]),
			...middleware({ 'check-permission': 'systemApplyCustom_r' }, [
				{
					name: 'legalaspects-lm-system-apply-company',
					path: 'lm/systemApplyCompany',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/indexCompany')
				}, 
			]),
			...middleware({ 'check-permission': 'systemApplyCustom_c' }, [
				{
					name: 'legalaspects-lm-system-apply-company-create',
					path: 'lm/systemApplyCompany/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/createCompany')
				},
			]),
			...middleware({ 'check-permission': 'systemApplyCustom_u' }, [
				{
					name: 'legalaspects-lm-system-apply-company-edit',
					path: 'lm/systemApplyCompany/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/edit')
				},
			]),
			...middleware({ 'check-permission': 'systemApplyCustom_r' }, [
				{
					name: 'legalaspects-lm-system-apply-company-view',
					path: 'lm/systemApplyCompany/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/systemApply/view')
				}
			]),
			...middleware({ 'check-permission': 'laws_report_r' }, [
				{
					name: 'legalaspects-lm-law-report',
					path: 'lm/laws/report',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/reports/index')
				}
			]),
			...middleware({ 'check-permission': 'laws_qualify' }, [
				{
					name: 'legalaspects-lm-law-configuration',
					path: 'lm/laws/configuration',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/configurations/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_list_standards_qualification_r' }, [
				{
					name: 'legalaspects-list-check-qualification',
					path: 'listcheck',
					component: () =>
					import('@/views/LegalAspects/contracts/listCheck/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_list_standards_qualification_c' }, [
				{
					name: 'legalaspects-list-check-qualification-create',
					path: 'listcheck/create',
					component: () =>
					import('@/views/LegalAspects/contracts/listCheck/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_list_standards_qualification_u' }, [
				{
					name: 'legalaspects-list-check-qualification-edit',
					path: 'listcheck/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/listCheck/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_list_standards_qualification_r' }, [
				{
					name: 'legalaspects-list-check-qualification-view',
					path: 'listcheck/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/listCheck/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_list_standards_qualification_r' }, [
				{
					name: 'legalaspects-contracts-view-list-check-qualification',
					path: 'listcheck/view-list-check/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/listCheck/listCheckItems')
				}
			]),
			...middleware({ 'check-permission': 'contracts_list_standards_qualification_c' }, [
				{
					name: 'legalaspects-list-check-qualification-clone',
					path: 'listcheck/clone',
					component: () =>
					import('@/views/LegalAspects/contracts/listCheck/cloneListCheckItems')
				}
			]),	
			...middleware({ 'check-permission': 'typesCustom_r' }, [
				{
					name: 'legalaspects-lm-type-company',
					path: 'lm/typeCompany',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/types/indexCompany')
				}, 
			]),
			...middleware({ 'check-permission': 'typesCustom_c' }, [
				{
					name: 'legalaspects-lm-type-company-create',
					path: 'lm/typeCompany/create',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/types/createCompany')
				},
			]),
			...middleware({ 'check-permission': 'typesCustom_u' }, [
				{
					name: 'legalaspects-lm-type-company-edit',
					path: 'lm/typeCompany/edit/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/types/edit')
				},
			]),
			...middleware({ 'check-permission': 'typesCustom_r' }, [
				{
					name: 'legalaspects-lm-type-company-view',
					path: 'lm/typeCompany/view/:id',
					component: () =>
					import('@/views/LegalAspects/legalMatrix/types/view')
				}
			]),				
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'legalaspects-contractor-masive-activities',
					path: 'contracts/masiveActivities',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/masiveActivities')
				}
			]),				
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'legalaspects-contractor-masive-responsibles',
					path: 'contracts/masiveResponsibles',
					component: () =>
					import('@/views/LegalAspects/contracts/contractor/masiveResponsibles')
				}
			]),			
			...middleware({ 'check-permission': 'contracts_informs_r' }, [
				{
					name: 'legalaspects-informs',
					path: 'contract/informs',
					component: () =>
					import('@/views/LegalAspects/contracts/informsQuantitative/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_c' }, [
				{
					name: 'legalaspects-informs-create',
					path: 'contract/informs/create',
					component: () => import('@/views/LegalAspects/contracts/informsQuantitative/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_c' }, [
				{
					name: 'legalaspects-informs-clone',
					path: 'contract/informs/clone',
					component: () => import('@/views/LegalAspects/contracts/informsQuantitative/clone')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_u' }, [
				{
					name: 'legalaspects-informs-edit',
					path: 'contract/informs/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informsQuantitative/edit')
				},
			]),
			...middleware({ 'check-permission': 'contracts_informs_r' }, [
				{
					name: 'legalaspects-informs-view',
					path: 'contract/informs/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informsQuantitative/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_perform_informs' }, [
				{
					name: 'legalaspects-informs-evaluate',
					path: 'informs/evaluate/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informContract/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_perform_informs' }, [
				{
					name: 'legalaspects-informs-contracts-clone',
					path: 'informs/contracts/clone',
					component: () =>
					import('@/views/LegalAspects/contracts/informContract/clone')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_view_informs_made' }, [
				{
					name: 'legalaspects-informs-contracts',
					path: 'informs/contracts/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informContract/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_view_informs_made' }, [
				{
					name: 'legalaspects-informs-contracts-view',
					path: 'informs/contracts/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informContract/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_edit_informs_made' }, [
				{
					name: 'legalaspects-informs-contracts-edit',
					path: 'informs/contracts/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informContract/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_informs_report_view' }, [
				{
					name: 'legalaspects-informs-report',
					path: 'informs/report/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/informContract/report')
				}
			]),
			...middleware({ 'check-permission': ['laws_qualify_view', 'contracts_r', 'contracts_typesQualification_r', 'contracts_evaluations_r', 'interestsCustom_r', 'contracts_evaluations_report_view', 'lawsCustom_r'] }, [
				{
					name: 'legalaspects-documentslegals',
					path: 'documentslegals',
					component: () => import('@/views/LegalAspects/documents/index')
				}
			]),
			...middleware({ 'check-permission': ['laws_qualify_view', 'contracts_r', 'contracts_typesQualification_r', 'contracts_evaluations_r', 'interestsCustom_r', 'contracts_evaluations_report_view', 'lawsCustom_r'] }, [
				{
					name: 'legalaspects-documentslegals-create',
					path: 'documentslegals/create',
					component: () => import('@/views/LegalAspects/documents/create')
				}
			]),
			...middleware({ 'check-permission': ['laws_qualify_view', 'contracts_r', 'contracts_typesQualification_r', 'contracts_evaluations_r', 'interestsCustom_r', 'contracts_evaluations_report_view', 'lawsCustom_r'] }, [
				{
					name: 'legalaspects-documentslegals-edit',
					path: 'documentslegals/edit/:id',
					component: () => import('@/views/LegalAspects/documents/edit')
				}
			]),
			...middleware({ 'check-permission': ['laws_qualify_view', 'contracts_r', 'contracts_typesQualification_r', 'contracts_evaluations_r', 'interestsCustom_r', 'contracts_evaluations_report_view', 'lawsCustom_r'] }, [
				{
					name: 'legalaspects-documentslegals-view',
					path: 'documentslegals/view/:id',
					component: () => import('@/views/LegalAspects/documents/view')
				}
			]),
			,
			...middleware({ 'check-permission': 'contracts_r' }, [
						{
				name: 'contracts-customHelpers',
				path: 'contracts/customHelpers',
				component: () =>
					import('@/views/LegalAspects/contracts/helpers/index')
				}
					]),
			...middleware({ 'check-permission': 'contracts_r' }, [
				{
				name: 'contracts-customHelpers-view',
				path: 'contracts/customHelpers/view/:id',
				component: () =>
					import('@/views/LegalAspects/contracts/helpers/view')
						}
			]),
			...middleware({ 'check-permission': 'lawsCustom_r' }, [
						{
				name: 'legalMatrix-customHelpers',
				path: 'legalMatrix/customHelpers',
				component: () =>
					import('@/views/LegalAspects/legalMatrix/helpers/index')
				}
			]),
			...middleware({ 'check-permission': 'lawsCustom_r' }, [
				{
				name: 'legalMatrix-customHelpers-view',
				path: 'legalMatrix/customHelpers/view/:id',
				component: () =>
					import('@/views/LegalAspects/legalMatrix/helpers/view')
						}
			]),
			...middleware({ 'check-permission': 'contracts_training_r' }, [
				{
				name: 'legalaspects-contracts-trainings-virtual-employees',
				path: 'trainings/employee/:id',
				component: () =>
					import('@/views/LegalAspects/contracts/trainings/virtual/indexEmployee')
						}
			]),
			...middleware({ 'check-permission': 'contracts_training_r' }, [
				{
				name: 'legalaspects-training-employee-view',
				path: 'trainings/employee/view/:id',
				component: () =>
					import('@/views/LegalAspects/contracts/trainings/virtual/viewEmployee')
						}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'contract-send-notification',
					path: 'sendNotification',
					component: () =>
					import('@/views/LegalAspects/contracts/sendNotification/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'contract-send-notification-create',
					path: 'sendNotification/create',
					component: () =>
					import('@/views/LegalAspects/contracts/sendNotification/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'contract-send-notification-edit',
					path: 'sendNotification/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/sendNotification/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'contract-send-notification-view',
					path: 'sendNotification/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/sendNotification/view')
				}
			]),
			...middleware({ 'check-permission': 'contracts_c' }, [
				{
					name: 'contract-send-notification-program',
					path: 'sendNotification/program/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/sendNotification/switchStatus')
				}
			]),
			...middleware({ 'check-permission': 'contracts_proyects_r' }, [
				{
					name: 'legalaspects-contracts-proyects',
					path: 'proyects',
					component: () =>
					import('@/views/LegalAspects/contracts/proyects/index')
				}
			]),
			...middleware({ 'check-permission': 'contracts_proyects_c' }, [
				{
					name: 'legalaspects-contracts-proyects-create',
					path: 'proyects/create',
					component: () =>
					import('@/views/LegalAspects/contracts/proyects/create')
				}
			]),
			...middleware({ 'check-permission': 'contracts_proyects_u' }, [
				{
					name: 'legalaspects-contracts-proyects-edit',
					path: 'proyects/edit/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/proyects/edit')
				}
			]),
			...middleware({ 'check-permission': 'contracts_proyects_r' }, [
				{
					name: 'legalaspects-contracts-proyects-view',
					path: 'proyects/view/:id',
					component: () =>
					import('@/views/LegalAspects/contracts/proyects/view')
				}
			]),
		]
	}
];
