<template>
    <div>
        <v-content class="product_settings">
            <v-container fluid>
                <v-expansion-panel>
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div>Category Types</div>
                        </template>
                        <v-card class="pa-2">
                            <v-btn absolute dark fab bottom small right color="pink" class="mt-2" @click="category_type_add_dialog=true">
                              <v-icon>add</v-icon>
                            </v-btn>
                            <v-data-table hide-actions :loading="loading.category_type_list" :headers="category_type_list.headers" :items="category_type_list.items" light>
                                <template v-slot:items="props">
                                    <template v-for="(it,key) in props.item" v-if="key != 'id'">
                                        <td v-if="it.actions" class="justify-end layout">
                                            <v-btn icon v-if="it.edit" @click="editCategoryTypeItem(it)">
                                                <v-icon small >edit</v-icon>
                                            </v-btn>
                                            <v-btn icon v-if="it.delete" @click="deleteItem(it)">
                                                <v-icon small>delete</v-icon>
                                            </v-btn>
                                        </td>
                                        <td v-else>{{it}}</td>
                                    </template>
                                </template>
                                <template v-slot:no-data>
                                    <v-alert :value="true" color="info" icon="warning">
                                        No data available. Please Add.
                                    </v-alert>
                                </template>
                            </v-data-table>
                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-container>
        </v-content>
        <category-type-add :dialog="category_type_add_dialog" v-on:close-category-type-add-dialog="updateCategoryTypeList"></category-type-add>
        <category-type-edit :dialog="category_type_edit_dialog" :id="category_type_edit_id" :formdata="category_type_edit_formdata" v-on:close-category-type-edit-dialog="updateCategoryTypeList"></category-type-edit>
    </div>
</template>
<style>
    .product_settings .v-expansion-panel__header{
        background-color: #ddd;
    }
</style>
<script>
import CategoryTypeAddComponent from '../taxonomy/CategoryTypeAddComponent.vue'
import CategoryTypeEditComponent from '../taxonomy/CategoryTypeEditComponent.vue'

export default{
    components:{
        'category-type-add':CategoryTypeAddComponent,
        'category-type-edit':CategoryTypeEditComponent
    },
    data(){
        return{
            category_type_list:[],
            category_type_add_dialog:false,
            category_type_edit_dialog:false,
            category_type_edit_id:'',
            loading:{
                'category_type_list':false,
            },
            category_type_edit_formdata:{
                'name':{
                    'value':'',
                    'error':'',
                },
                'code_length':{
                    'value':'1'
                },
                'code_type':{
                    'value':'alpha'
                },
                'autogen':{
                    'value':0
                },
                'in_pc':{
                    'value':0
                }
            },
        }
    },
    mounted(){
        this.loading.category_type_list = true
        axios.get('products/category_type/list').then((response)=>{
            if(response.status = 200){
                this.category_type_list = response.data.list
            }
            else{
                console.log(response)
            }
        })
        this.loading.category_type_list = false
    },
    methods:{
        updateCategoryTypeList(){
            this.category_type_add_dialog = false
            this.category_type_edit_dialog = false
            this.loading.category_type_list = true
            axios.get('products/category_type/list').then((response)=>{
                if(response.status = 200){
                    this.category_type_list = response.data.list
                }
                else{
                    console.log(response)
                }
            })
            this.loading.category_type_list = false
        },
        editCategoryTypeItem(item){
            axios.get('products/category_type/edit/'+item.id).then((response)=>{
                if(response.status==200){
                    Object.keys(response.data.data).forEach((key)=>{
                        this.category_type_edit_formdata[key]['value'] = response.data.data[key].toString();
                    })
                    //console.log(this.category_type_edit_formdata);
                }
                else{
                    console.log(response)
                }
            })
            this.category_type_edit_id = item.id
            this.category_type_edit_dialog = true
        },
        saveItem(data){
            var fD = new FormData();
            var fd_str = '';
            data.items.forEach((item)=>{
                fD.append(item.name,item.value)
            });
            axios.post(data.route,fD).then((response)=>{
                if(response.status == 200){
                    if(response.data.message == 'success'){
                        this.dialog = false
                        this.dialog_data = []
                    }
                    else{
                        this.dialog_data = this.response.dialog_data
                    }
                }
                else{
                    console.log(response)
                }
            })
        }
    }
}
</script>
