import SettingsIndex from '../views/backend/settings/IndexComponent.vue'
import SettingsUsers from '../views/backend/settings/UsersComponent.vue'

import SettingsAcl from '../views/backend/settings/acl/IndexComponent.vue'
import SettingsAclPermissions from '../views/backend/settings/acl/PermissionsComponent.vue'
import SettingsAclRoles from '../views/backend/settings/acl/RolesComponent.vue'
import DashboardIndex from '../views/backend/dashboard/IndexComponent.vue'

import ProductsIndex from '../views/backend/product/ProductsIndexComponent.vue'
import ProductsList from '../views/backend/product/ProductsListComponent.vue'
import ProductsAdd from '../views/backend/product/ProductsAddComponent.vue'
import ProductsSettings from '../views/backend/product/ProductsSettingsComponent.vue'
import ProductsCategoriesList from '../views/backend/taxonomy/CategoriesListComponent.vue'
import ProductsCategoriesAdd from '../views/backend/taxonomy/CategoriesAddComponent.vue'
export const routes=[
    {
        path:'/',
        name:'Dashboard',
        component:DashboardIndex,
    },
    {
        path:'/settings',
        name:'Settings',
        component:SettingsIndex,
        children:[
            {
                path:'acl',
                name:'ACL',
                component:SettingsAcl,
                children:[
                    {
                        path:'permissions',
                        name:'Permissions',
                        component:SettingsAclPermissions,
                    },
                    {
                        path:'roles',
                        name:'Roles',
                        component:SettingsAclRoles,
                    }
                ]
            },
            {
                path:'users',
                name:'Users',
                component:SettingsUsers,
            }
        ]
    },
    {
        path:'/products',
        name:'Products',
        component:ProductsIndex,
        children:[
            {
                path:'list',
                name:'Products List',
                component:ProductsList,
            },
            {
                path:'add',
                name:'Products Add',
                component:ProductsAdd,
            },
            {
                path:'settings',
                name:'Products Settings',
                component:ProductsSettings,
            },
            {
                path:'categories',
                name:'Products Categories',
                component:ProductsCategoriesList,
            },
            {
                path:'categories/add',
                name:'Product Category Add',
                component:ProductsCategoriesAdd,
            },
        ]
    },
];
