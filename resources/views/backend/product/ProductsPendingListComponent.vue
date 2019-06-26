<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <list-component
                listHeadline="Pending Products"
                listClass="products-list"
                :baseRoute="baseRoute"
                :filterables="filterables"
                :listSettingsRoute="listSettingsRoute"
                :triggerUpdate="triggerUpdate"
                v-on:triggered-update="triggerUpdate=false"
                v-on:open-add-dialog="redirectAdd"
                v-on:open-edit-dialog="editItem"
                ></list-component>
            </v-container>
        </v-content>
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
                baseRoute:'/products?pending=1&',
                listSettingsRoute:'/products/list_settings',
                filterables:[],
                addDialog:false,
                triggerUpdate:false,
                editDialog:false,
                editId:0,
                breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Products',
                        disabled:false,
                        to:'/products/list'
                    },
                    {
                        text:'Pending',
                        disabled:true,
                        to:'/products/pending'
                    },  
                ]
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
            })
        },
        methods:{
            editItem($event){
                this.$router.push('edit/'+$event)
            },
            redirectAdd(){
                this.$router.push('add')
            }
        }
    }
</script>
