<template>
    <div>
        <v-app id="customers-view">
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
            page_title:'Customers',
            sidebar:false,
            items:''
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
    mounted(){
        axios.get('customers/get_menu').then((response)=>{
            this.items = response.data.items
        })
    },
    methods:{
        toggleSidebar(){
            this.sidebar = !this.sidebar
        }
    }

}
</script>
