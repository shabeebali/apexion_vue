<template>
    <div>
        <v-app id="products-view">
            <router-view v-on:snackbar="snackBar"></router-view>
            <v-snackbar v-model="snackbar" :timeout="timeout" :color="color" bottom right>
                {{ text }}
            <v-btn dark text @click="snackbar = false">Close</v-btn>
            </v-snackbar>
        </v-app>
    </div>
</template>
<script>
export default{
    data(){
        return{
            page_title:'Products',
            snackbar:false,
            text:'',
            timeout:2000,
            color:'',
        }
    },
    components:{
    },
    mounted(){
        this.$emit('top-menu-url','products/get_menu')
    },
    methods:{
        snackBar(txt,color){
            this.snackbar = true
            this.text = txt
            this.color = color
        }
    }
}
</script>
