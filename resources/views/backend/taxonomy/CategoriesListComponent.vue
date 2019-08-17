<template>
    <div>
        <v-content>
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <div v-if="loader" >
                    <pulse-loader style="top:50%;left:50%;position:absolute;"></pulse-loader>
                </div>
                <div v-else>
                    <list-component
                    listHeadline="Categories"
                    listClass="categories-list"
                    :baseRoute="baseRoute"
                    :filterables="filterables"
                    :listSettingsRoute="listSettingsRoute"
                    :triggerUpdate="triggerUpdate"
                    v-on:triggered-update="triggerUpdate=false"
                    v-on:open-add-dialog="addDialog=true"
                    v-on:open-edit-dialog="editItem"
                    v-on:delete-item="deleteItem"
                    importable exportable></list-component>
                    <add-component :dialog="addDialog" v-on:close-add-dialog="triggerUpdate = true;addDialog=false"></add-component>
                    <edit-component :id="editId" :dialog="editDialog" v-on:close-edit-dialog="triggerUpdate = true; editDialog = false; editId = 0">
                    </edit-component>
                </div>
            </v-container>
        </v-content>
    </div>
</template>
<script>
    import ListComponent from '../../../js/components/ListComponent.vue'
    import AddComponent from './CategoriesAddComponent.vue'
    import EditComponent from './CategoriesEditComponent.vue'
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue'
    export default{
        components:{
            'list-component':ListComponent,
            'add-component':AddComponent,
            'edit-component':EditComponent,
            'pulse-loader':PulseLoader,
        },
        data(){
            return{
                loader : true,
                baseRoute:'/products/categories?',
                listSettingsRoute:'/products/list_settings',
                filterables:[],
                addDialog:false,
                triggerUpdate:false,
                addFlag:true,
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
                        text:'Categories',
                        disabled:true,
                        to:'/products/categories/list'
                    },  
                ]
            }
        },
        mounted(){
            this.loader = true
            axios.get('/products/categories/filterables').then((response)=>{
                if(response.status == 200)
                {
                    this.filterables = response.data.filterables
                    this.loader = false
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
            deleteItem($event){
                this()
            }
        }
    }
</script>
