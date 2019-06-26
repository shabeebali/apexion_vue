<template>
    <div>
        <v-layout  row mb-2>
            <v-subheader class="headline">Roles</v-subheader>
            <v-spacer></v-spacer>
            <v-btn class="primary" @click.stop="addModel=true">Add</v-btn>
        </v-layout>
        <data-table :route="listRoute"></data-table>
        <add-component
        :formFields="addFormFields"
        :config="addFormConfig"
        :addModel="addModel"
        v-on:close-model="addModel=false"
        v-on:get-list="getList"></add-component>
    </div>
</template>
<script>
import DataTableComponent from '../../../../js/components/DataTableComponent.vue'
import AddComponent from '../../../../js/components/AddComponent.vue'
export default{
    components:{
        'data-table':DataTableComponent,
        'add-component':AddComponent,
    },
    data(){
        return{
            tableItems:[],
            tableHeaders:[],
            listRoute:'/acl/roles',
            addModel:false,
            addValid:true,
            addFormFields:[],
            addFormConfig:[],
        }
    },
    created(){
        this.getList();
        axios.get('/acl/role/addconfig').then((response)=>{
            if(response.status == 200){
                this.addFormFields = response.data.formFields;
                this.addFormConfig = response.data.config;
            }
            else{
                console.log(response.status);
            }
        }).catch((error)=>{
            console.log(error);
        });
    },
    methods:{
        getList(){
            axios.get('/acl/roles').then((response)=>{
                if(response.status == 200){
                    this.tableItems = response.data.roles;
                    this.tableHeaders = response.data.headers;
                }
                else{
                    console.log('Something went wrong at settings/acl/RolesComponent '+response.status);
                }
            }).catch((error)=>{
                console.log('Something went wrong at settings/acl/RolesComponent '+error);
            });
        }
    }
}
</script>
