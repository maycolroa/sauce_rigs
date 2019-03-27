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
				import('@/views/LegalAspects/evaluations/evaluate')
			}
		]
	}
];
