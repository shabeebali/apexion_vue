import Login from '../views/backend/auth/LoginComponent.vue'
import SettingsIndex from '../views/backend/settings/IndexComponent.vue'
import SettingsUsersList from '../views/backend/settings/UsersListComponent.vue'
import SettingsUsersIndex from '../views/backend/settings/UsersIndexComponent.vue'
import SettingsUsersAdd from '../views/backend/settings/UsersAddComponent.vue'
import SettingsUsersEdit from '../views/backend/settings/UsersEditComponent.vue'
import SettingsUsersRolesList from '../views/backend/settings/UserRolesListComponent.vue'
import SettingsUserRoleIndex from '../views/backend/settings/UserRoleIndexComponent.vue'
import SettingsUserRoleAdd from '../views/backend/settings/UserRoleAddComponent.vue'
import SettingsUserRoleEdit from '../views/backend/settings/UserRoleEditComponent.vue'
import SettingsGeneral from '../views/backend/settings/GeneralComponent.vue'
import DashboardIndex from '../views/backend/dashboard/IndexComponent.vue'

import ProductsIndex from '../views/backend/product/ProductsIndexComponent.vue'
import ProductsList from '../views/backend/product/ProductsListComponent.vue'
import ProductsView from '../views/backend/product/ProductsViewComponent.vue'
import ProductsPendingList from '../views/backend/product/ProductsPendingListComponent.vue'
import ProductsTallyList from '../views/backend/product/ProductsTallyListComponent.vue'
import ProductsAdd from '../views/backend/product/ProductsAddComponent.vue'
import ProductsEdit from '../views/backend/product/ProductsEditComponent.vue'
import ProductsImport from '../views/backend/product/ProductsImportComponent.vue'
import ProductsSettings from '../views/backend/product/ProductsSettingsComponent.vue'
import ProductsCategoriesIndex from '../views/backend/taxonomy/CategoriesIndexComponent.vue'
import ProductsCategoriesList from '../views/backend/taxonomy/CategoriesListComponent.vue'
import ProductsCategoriesAdd from '../views/backend/taxonomy/CategoriesAddComponent.vue'
import ProductsCategoriesImport from '../views/backend/taxonomy/CategoriesImportComponent.vue'

import CustomerIndex from '../views/backend/customer/CustomerIndexComponent.vue'
import CustomerList from '../views/backend/customer/CustomerListComponent.vue'
import CustomerAdd from '../views/backend/customer/CustomerAddComponent.vue'
import CustomerEdit from '../views/backend/customer/CustomerEditComponent.vue'
import CustomerImport from '../views/backend/customer/CustomerImportComponent.vue'

import SaleIndex from '../views/backend/sale/SaleIndexComponent.vue'
import OrderIndex from '../views/backend/sale/OrderIndexComponent.vue'
import OrderList from '../views/backend/sale/OrderListComponent.vue'
import OrderAdd from '../views/backend/sale/OrderAddComponent.vue'
import OrderEdit from '../views/backend/sale/OrderEditComponent.vue'
export const routes=[

    {
        path:'/',
        name:'Dashboard',
        component:DashboardIndex,
    },
    {
        path:'/login',
        name:'Login',
        component:Login
    },
    {
        path:'/settings',
        name:'Settings',
        component:SettingsIndex,
        children:[
            {
                path:'users',
                name:'Users',
                component:SettingsUsersIndex,
                children:[
                    {
                        'path':'list',
                        component:SettingsUsersList,
                    },
                    {
                        path:'add',
                        component:SettingsUsersAdd,
                    },
                    {
                        path:'edit/:id',
                        component:SettingsUsersEdit,
                    },
                    {
                        path:'roles',
                        component:SettingsUserRoleIndex,
                        children:[
                            {
                                path:'list',
                                component:SettingsUsersRolesList,
                            },
                            {
                                path:'add',
                                component:SettingsUserRoleAdd,
                            },
                            {
                                path:'edit/:id',
                                component:SettingsUserRoleEdit,
                            }
                        ]
                    }, 
                ],               
            },
            {
                path:'general',
                component:SettingsGeneral,
            }
            
        ]
    },
    {
        path:'/inventory',
        name:'Inventory',
        component:ProductsIndex,
        children:[
            {
                path:'products',
                name:'Products',
                component:ProductsIndex,
                children:[
                    {
                        path:'/',
                        component:ProductsList,
                    },
                    {
                        path:'pending',
                        component:ProductsPendingList,
                    },
                    {
                        path:'tally',
                        component:ProductsTallyList,
                    },
                    {
                        path:'add/:redirect?',
                        component:ProductsAdd,
                    },
                    {
                        path:'edit/:id/:redirect?',
                        component:ProductsEdit,
                    },
                    {
                        path:'view/:id',
                        component:ProductsView,
                    },
                    {
                        path:'import',
                        component:ProductsImport,
                    },
                    
                ]
            },
            {
                path:'categories',
                component:ProductsCategoriesIndex,
                children:[
                    {
                        path:'/',
                        component:ProductsCategoriesList,
                    },
                    {
                        path:'import',
                        component:ProductsCategoriesImport,
                    },
                ]
            },
            {
                path:'settings',
                component:ProductsSettings,
            },
        ],
    },
    {
        path:'/customers',
        component:CustomerIndex,
        children:[
            {
                path:'/',
                component:CustomerList,
            },
            {
                path:'add',
                component:CustomerAdd,
            },
            {
                path:'edit/:id',
                component:CustomerEdit,
            },
            {
                path:'import',
                component:CustomerImport,
            },
        ],
    },
    {
        path:'/sale',
        component:SaleIndex,
        children:[
            {
                path:'orders',
                component:OrderIndex,
                children:[
                    {
                        path:'/',
                        component:OrderList,
                    },
                    {
                        path:'add',
                        component:OrderAdd,
                    },
                    {
                        path:'edit/:id',
                        component:OrderEdit,
                    },
                ]
            },
        ],
    },
];
