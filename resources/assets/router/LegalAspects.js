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
			...middleware({ 'check-permission': 'contracts_view_list_standards' }, [
				{
					name: 'legalaspects-contracts-view-list-check',
					path: 'contracts/view-list-check/:id',
					component: () => import('@/views/LegalAspects/contracts/contract/listCheckItems')
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
			...middleware({ 'check-permission': 'laws_qualify' }, [
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
		]
	}
];
