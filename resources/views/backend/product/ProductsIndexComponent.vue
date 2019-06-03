<template>
    <div>
        <v-app id="products-view">
            <nav-drawer v-bind:items="items" v-bind:sidebar="sidebar" v-on:close-sidebar="toggleSidebar"></nav-drawer>
            <router-view></router-view>
        </v-app>
    </div>
</template>
<script>
import NavDrawer from '../../../js/components/NavDrawerComponent.vue';
export default{
    props:['sidebarToggle'],
    data(){
        return{
            page_title:'Products',
            sidebar:false,
            items:[
                {
                    'text':'Products',
                    'route':'/products/list',
                    'icon':'list_alt',
                },
                {
                    'text':'Categories',
                    'route':'/products/categories',
                    'icon':'category',
                },
                {
                    'text':'Settings',
                    'route':'/products/settings',
                    'icon':'settings',
                },
                {
                    'divider': true,
                },
            ]
        }
    },
    components:{
        'nav-drawer': NavDrawer,
    },
    watch:{
        sidebarToggle:{
            handler(){
                this.sidebar = !this.sidebar
            }
        }
    },
    methods:{
        toggleSidebar(){
            this.sidebar = !this.sidebar
        }
    }

}
</script>
