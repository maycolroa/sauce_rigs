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
				name: 'legalaspects-contracts-create',
				path: 'contracts/create',
				component: () => import('@/views/LegalAspects/contracts/contractor/create')
			},
			{
				name: 'legalaspects-contracts-complete-information',
				path: 'contracts/complete-information/:id',
				component: () => import('@/views/LegalAspects/contracts/contract/completeInformation')
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
				import('@/views/LegalAspects/typesrating/index')
			}, 
			{
				name: 'legalaspects-typesrating-create',
				path: 'typesrating/create',
				component: () =>
				import('@/views/LegalAspects/typesrating/create')
			},
			{
				name: 'legalaspects-typesrating-edit',
				path: 'typesrating/edit/:id',
				component: () =>
				import('@/views/LegalAspects/typesrating/edit')
			},
			{
				name: 'legalaspects-typesrating-view',
				path: 'typesrating/view/:id',
				component: () =>
				import('@/views/LegalAspects/typesrating/view')
			},
			{
				name: 'legalaspects-evaluations',
				path: 'evaluations',
				component: () =>
				import('@/views/LegalAspects/evaluations/index')
			}, 
			{
				name: 'legalaspects-evaluations-create',
				path: 'evaluations/create',
				component: () => import('@/views/LegalAspects/evaluations/create')
			},
			{
				name: 'legalaspects-evaluations-edit',
				path: 'evaluations/edit/:id',
				component: () =>
				import('@/views/LegalAspects/evaluations/edit')
			},
			{
				name: 'legalaspects-evaluations-view',
				path: 'evaluations/view/:id',
				component: () =>
				import('@/views/LegalAspects/evaluations/view')
			},
			{
				name: 'legalaspects-evaluations-evaluate',
				path: 'evaluations/evaluate/:id',
				component: () =>
				import('@/views/LegalAspects/evaluationContracts/create')
			},
			{
				name: 'legalaspects-evaluations-contracts',
				path: 'evaluations/contracts/:id',
				component: () =>
				import('@/views/LegalAspects/evaluationContracts/index')
			},
			{
				name: 'legalaspects-evaluations-contracts-view',
				path: 'evaluations/contracts/view/:id',
				component: () =>
				import('@/views/LegalAspects/evaluationContracts/view')
			},
			{
				name: 'legalaspects-evaluations-contracts-edit',
				path: 'evaluations/contracts/edit/:id',
				component: () =>
				import('@/views/LegalAspects/evaluationContracts/edit')
			},
			{
				name: 'legalaspects-evaluations-report',
				path: 'evaluations/report',
				component: () =>
				import('@/views/LegalAspects/evaluationContracts/report')
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
