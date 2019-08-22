<template>
    <div style="height:100%">
        <main-header v-if="this.$router.currentRoute.name !== 'Login'" :topMenuUrl="topMenuUrl"></main-header>
        <router-view v-on:top-menu-url="updateTopMenu"></router-view>
    </div>
</template>
<script>
import MainHeader from './MainHeaderComponent.vue'
export default{
    data:function(){
        return{
            topMenuUrl:'',
        }
    },
    components:{
        'main-header': MainHeader,
    },
    created:function(){
    },
    methods:{
        updateTopMenu(url){
            this.topMenuUrl = url
        }
    }
}
</script>
