<template>
    <div>
        <list-component
        listHeadline="Products"
        listClass="products-list"
        :baseRoute="baseRoute"
        :filterables="filterables"
        :listSettingsRoute="listSettingsRoute"
        :triggerUpdate="triggerUpdate"
        v-on:triggered-update="triggerUpdate=false"
        v-on:open-add-dialog="redirectAdd"
        v-on:open-edit-dialog="editItem"
        :addFlag="addFlag"></list-component>
    </div>
</template>
<script>
    import ListComponent from '../../../js/components/ListComponent.vue'
    export default{
        components:{
            'list-component':ListComponent,
        },
        data(){
            return{
                baseRoute:'/products?',
                listSettingsRoute:'/products/list_settings',
                filterables:[],
                addDialog:false,
                triggerUpdate:false,
                addFlag:true,
                editDialog:false,
                editId:0,
            }
        },
        mounted(){
            axios.get('/products/filterables').then((response)=>{
                if(response.status == 200)
                {
                    this.filterables = response.data.filterables
                }
                else{
                    alert('Something went wrong!!!')
                    console.log(response)
                }
            });
        },
        methods:{
            editItem($event){
                this.editId=$event
                this.editDialog = true
            },
            redirectAdd(){
                this.$router.push('add')
            }
        }
    }
</script>
