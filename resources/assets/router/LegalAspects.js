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
				component: () => import('@/views/LegalAspects/contractLessee/index')
			},
			{
				name: 'legalaspects-contracts-create',
				path: 'contracts/create',
				component: () => import('@/views/LegalAspects/contractLessee/create')
			}
		]
	}
];
