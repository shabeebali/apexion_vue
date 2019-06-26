<template>
    <div>
        <v-app id="settings-view">
            <nav-drawer v-bind:items="items" v-bind:sidebar="sidebar"></nav-drawer>
                <router-view></router-view>
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
            items:'',
        }
    },
    mounted(){
        axios.get('settings/get_menu').then((response)=>{
            this.items = response.data.items
        })
    },
    components:{
        'nav-drawer': NavDrawer,
    }

}
</script>
