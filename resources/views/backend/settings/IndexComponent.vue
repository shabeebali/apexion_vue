<template>
    <div>
        <v-app id="settings-view">
            <nav-drawer v-bind:items="items" v-bind:sidebar="sidebar"></nav-drawer>
            <v-content style="">
                <v-container fluid>
                    <router-view></router-view>
                </v-container>
            </v-content>
        </v-app>
    </div>
</template>
<script>
import NavDrawer from '../../../js/components/NavDrawerComponent.vue';
export default{
    data(){
        return{
            page_title:'Settings',
            sidebar:true,
            items:[
                {
                    'text':'Users',
                    'route':'/settings/users',
                    'icon':'U',
                },
                {
                    'divider': true,
                },
                {
                    'heading': 'ACL'
                },
                {
                    'text':'Permissions',
                    'route':'/settings/acl/permissions',
                    'icon':'P',
                },
                {
                    'text':'Roles',
                    'route':'/settings/acl/roles',
                    'icon':'R',
                },
                {
                    'divider': true,
                },
            ]
        }
    },
    components:{
        'nav-drawer': NavDrawer,
    }

}
</script>
