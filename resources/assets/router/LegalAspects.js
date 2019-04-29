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
			}
		]
	}
];
