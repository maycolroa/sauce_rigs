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
