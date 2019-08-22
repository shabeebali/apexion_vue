<template>
    <div>
        <v-app id="customers-view">
            <router-view></router-view>
        </v-app>
    </div>
</template>
<script>
export default{
    props:['sidebarToggle'],
    data(){
        return{
            page_title:'Customers',
        }
    },
    mounted(){
        this.$emit('top-menu-url','customers/get_menu')
    },

}
</script>
