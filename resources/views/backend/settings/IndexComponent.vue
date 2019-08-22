<template>
    <div>
        <v-app id="settings-view">
            <router-view></router-view>
        </v-app>
    </div>
</template>
<script>
export default{
    data(){
        return{
            page_title:'Settings',
        }
    },
    mounted(){
        this.$emit('top-menu-url','settings/get_menu')
    },

}
</script>
