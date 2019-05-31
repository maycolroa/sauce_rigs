import LayoutModules from "@/views/layoutModules";
import Home from "@/views/home";

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
			{
				name: 'legalaspects-contractor',
				path: 'contractor',
				component: () =>
				import('@/views/LegalAspects/contracts/contractor/index')
			}, 
			{
				name: 'legalaspects-contractor-create',
				path: 'contractor/create',
				component: () =>
				import('@/views/LegalAspects/contracts/contractor/create')
			},
			{
				name: 'legalaspects-contractor-edit',
				path: 'contractor/edit/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/contractor/edit')
			},
			{
				name: 'legalaspects-contractor-view',
				path: 'contractor/view/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/contractor/view')
			},
			{
				name: 'legalaspects-contracts-information',
				path: 'contracts/information',
				component: () => import('@/views/LegalAspects/contracts/contract/information')
			},
			{
				name: 'legalaspects-contracts-list-check-items',
				path: 'contracts/list-check-items',
				component: () => import('@/views/LegalAspects/contracts/contract/listCheckItems')
			},
			{
				name: 'legalaspects-contracts-view-list-check',
				path: 'contracts/view-list-check/:id',
				component: () => import('@/views/LegalAspects/contracts/contract/listCheckItems')
			},
			{
				name: 'legalaspects-typesrating',
				path: 'typesrating',
				component: () =>
				import('@/views/LegalAspects/contracts/typesrating/index')
			}, 
			{
				name: 'legalaspects-typesrating-create',
				path: 'typesrating/create',
				component: () =>
				import('@/views/LegalAspects/contracts/typesrating/create')
			},
			{
				name: 'legalaspects-typesrating-edit',
				path: 'typesrating/edit/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/typesrating/edit')
			},
			{
				name: 'legalaspects-typesrating-view',
				path: 'typesrating/view/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/typesrating/view')
			},
			{
				name: 'legalaspects-evaluations',
				path: 'evaluations',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluations/index')
			}, 
			{
				name: 'legalaspects-evaluations-create',
				path: 'evaluations/create',
				component: () => import('@/views/LegalAspects/contracts/evaluations/create')
			},
			{
				name: 'legalaspects-evaluations-edit',
				path: 'evaluations/edit/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluations/edit')
			},
			{
				name: 'legalaspects-evaluations-view',
				path: 'evaluations/view/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluations/view')
			},
			{
				name: 'legalaspects-evaluations-evaluate',
				path: 'evaluations/evaluate/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluationContracts/create')
			},
			{
				name: 'legalaspects-evaluations-lessee',
				path: 'evaluations/contracts',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluationContracts/index')
			},
			{
				name: 'legalaspects-evaluations-contracts',
				path: 'evaluations/contracts/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluationContracts/index')
			},
			{
				name: 'legalaspects-evaluations-contracts-view',
				path: 'evaluations/contracts/view/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluationContracts/view')
			},
			{
				name: 'legalaspects-evaluations-contracts-edit',
				path: 'evaluations/contracts/edit/:id',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluationContracts/edit')
			},
			{
				name: 'legalaspects-evaluations-report',
				path: 'evaluations/report',
				component: () =>
				import('@/views/LegalAspects/contracts/evaluationContracts/report')
			},
			{
				name: 'legalaspects-upload-files',
				path: 'upload-files',
				component: () => import('@/views/LegalAspects/contracts/uploadFiles/index')
			},
			{
				name: 'legalaspects-upload-files-create',
				path: 'upload-files/create',
				component: () => import('@/views/LegalAspects/contracts/uploadFiles/create')
			},
			{
				name: 'legalaspects-upload-files-edit',
				path: 'upload-files/edit/:id',
				component: () => import('@/views/LegalAspects/contracts/uploadFiles/edit')
			},
			{
				name: 'legalaspects-upload-files-view',
				path: 'upload-files/view/:id',
				component: () => import('@/views/LegalAspects/contracts/uploadFiles/view')
			},
			{
				name: 'legalaspects-legalmatrix',
				path: 'legalmatrix',
				component: () => import('@/views/LegalAspects/legalMatrix/index')
			},
			{
				name: 'legalaspects-lm-interest',
				path: 'lm/interests',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/interests/index')
			}, 
			{
				name: 'legalaspects-lm-interest-create',
				path: 'lm/interests/create',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/interests/create')
			},
			{
				name: 'legalaspects-lm-interest-edit',
				path: 'lm/interests/edit/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/interests/edit')
			},
			{
				name: 'legalaspects-lm-interest-view',
				path: 'lm/interests/view/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/interests/view')
			},
			{
				name: 'legalaspects-lm-riskaspect',
				path: 'lm/riskaspects',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/riskAspects/index')
			}, 
			{
				name: 'legalaspects-lm-riskaspect-create',
				path: 'lm/riskaspects/create',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/riskAspects/create')
			},
			{
				name: 'legalaspects-lm-riskaspect-edit',
				path: 'lm/riskaspects/edit/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/riskAspects/edit')
			},
			{
				name: 'legalaspects-lm-riskaspect-view',
				path: 'lm/riskaspects/view/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/riskAspects/view')
			},
			{
				name: 'legalaspects-lm-sstrisk',
				path: 'lm/sstrisks',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/sstRisk/index')
			}, 
			{
				name: 'legalaspects-lm-sstrisk-create',
				path: 'lm/sstrisks/create',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/sstRisk/create')
			},
			{
				name: 'legalaspects-lm-sstrisk-edit',
				path: 'lm/sstrisks/edit/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/sstRisk/edit')
			},
			{
				name: 'legalaspects-lm-sstrisk-view',
				path: 'lm/sstrisks/view/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/sstRisk/view')
			},
			{
				name: 'legalaspects-lm-entity',
				path: 'lm/entities',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/entities/index')
			}, 
			{
				name: 'legalaspects-lm-entity-create',
				path: 'lm/entities/create',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/entities/create')
			},
			{
				name: 'legalaspects-lm-entity-edit',
				path: 'lm/entities/edit/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/entities/edit')
			},
			{
				name: 'legalaspects-lm-entity-view',
				path: 'lm/entities/view/:id',
				component: () =>
				import('@/views/LegalAspects/legalMatrix/entities/view')
			},
		]
	}
];
