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
			}
		]
	}
];
