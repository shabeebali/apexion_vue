<template>
    <div>
        <v-content class="product_settings">
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <v-expansion-panel class="mb-2">
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
                <v-expansion-panel class="mb-2">
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div>Code Order</div>
                        </template>
                        <v-card class="pa-2">
                            <v-list>
                                <draggable v-model="codeOrderArray" @start="drag=true" @end="drag=false">
                                   <v-list-tile style="border:1px solid #ddd; cursor:move;" v-for="element in codeOrderArray" :key="element.id">{{element.name}}</v-list-tile>
                                </draggable>
                            </v-list>
                            <v-card-actions>
                                <v-btn @click.stop="saveCodeOrder" color="primary">Save</v-btn>
                                <v-btn @click.stop="resetCodeOrder">Reset</v-btn>
                            </v-card-actions>

                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel class="mb-2">
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div>PriceLists</div>
                        </template>
                        <v-card class="pa-2">
                            <v-btn absolute dark fab bottom small right color="pink" class="mt-2" @click="pricelist_add_dialog=true">
                              <v-icon>add</v-icon>
                            </v-btn>
                            <v-data-table hide-actions :loading="loading.pricelist_list" :headers="pricelist.headers" :items="pricelist.items" light>
                                <template v-slot:items="props">
                                    <template v-for="(it,key) in props.item" v-if="key != 'id'">
                                        <td v-if="it.actions" class="justify-end layout">
                                            <v-btn icon v-if="it.edit" @click="editPricelistItem(it)">
                                                <v-icon small >edit</v-icon>
                                            </v-btn>
                                            <v-btn icon v-if="it.delete" @click="deletePricelistItem(it)">
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
                <v-expansion-panel class="mb-2">
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div>Warehouses</div>
                        </template>
                        <v-card class="pa-2">
                            <v-btn absolute dark fab bottom small right color="pink" class="mt-2" @click="warehouse_add_dialog=true">
                              <v-icon>add</v-icon>
                            </v-btn>
                            <v-data-table hide-actions :loading="loading.warehouse" :headers="warehouse.headers" :items="warehouse.items" light>
                                <template v-slot:items="props">
                                    <template v-for="(it,key) in props.item" v-if="key != 'id'">
                                        <td v-if="it.actions" class="justify-end layout">
                                            <v-btn icon v-if="it.edit" @click="editWarehouse(it)">
                                                <v-icon small >edit</v-icon>
                                            </v-btn>
                                            <v-btn icon v-if="it.delete" @click="deleteWarehouse(it)">
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
        <pricelist-add :dialog="pricelist_add_dialog" v-on:close-pricelist-add-dialog="updatePricelist"></pricelist-add>
        <pricelist-edit :dialog="pricelist_edit_dialog" :id="pricelist_edit_id" :formdata="pricelist_edit_formdata" v-on:close-pricelist-edit-dialog="updatePricelist"></pricelist-edit>
        <warehouse-add :dialog="warehouse_add_dialog" v-on:close-warehouse-add-dialog="updateWarehouseList"></warehouse-add>
        <warehouse-edit :dialog="warehouse_edit_dialog" :id="warehouse_edit_id" :formdata="warehouse_edit_formdata" v-on:close-warehouse-edit-dialog="updateWarehouseList"></warehouse-edit>
        <v-dialog v-model="loadingDialog" hide-overlay persistent width="300">
            <v-card color="teal" dark>
                <v-card-text>
                    Please stand by
                    <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
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
import PricelistAddComponent from '../product/PricelistAddComponent.vue'
import PricelistEditComponent from '../product/PricelistEditComponent.vue'
import WarehouseAdd from '../product/WarehouseAddComponent.vue'
import WarehouseEdit from '../product/WarehouseEditComponent.vue'
import draggable from 'vuedraggable'
export default{
    components:{
        'category-type-add':CategoryTypeAddComponent,
        'category-type-edit':CategoryTypeEditComponent,
        'pricelist-add':PricelistAddComponent,
        'pricelist-edit':PricelistEditComponent,
        'warehouse-add':WarehouseAdd,
        'warehouse-edit':WarehouseEdit,
        'draggable':draggable,
    },
    data(){
        return{
            warehouse:[],
            warehouse_add_dialog:false,
            warehouse_edit_dialog:false,
            warehouse_edit_id:'',
            warehouse_edit_formdata:{
                'name':{
                    'value':'',
                    'error':'',
                },
            },
            category_type_list:[],
            category_type_add_dialog:false,
            category_type_edit_dialog:false,
            category_type_edit_id:'',
            loading:{
                'category_type_list':false,
                'pricelist_list':false,
                'warehouse':false,
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
            pricelist_edit_formdata:{
                'name':{
                    'value':'',
                    'error':'',
                },
            },
            loadingDialog:false,
            codeOrderArray:[],
            pricelist:[],
            pricelist_add_dialog:false,
            pricelist_edit_dialog:false,
            pricelist_edit_id:'',
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
                        text:'Settings',
                        disabled:true,
                        to:'/products/settings'
                    },  
                ]
        }
    },
    mounted(){
        this.loading.category_type_list = true
        axios.get('products/category_type/list').then((response)=>{
            this.category_type_list = response.data.list
        })
        this.loading.category_type_list = false
        axios.get('products/code_order').then((response)=>{
            this.codeOrderArray = response.data.data
        })
        axios.get('products/pricelist').then((response)=>{
            this.pricelist = response.data.list
        })
        axios.get('products/warehouses').then((response)=>{
            this.warehouse = response.data.list
        })
    },
    methods:{
        updateCategoryTypeList(){
            this.category_type_add_dialog = false
            this.category_type_edit_dialog = false
            this.loading.category_type_list = true
            axios.get('products/category_type/list').then((response)=>{
                this.category_type_list = response.data.list
            })
            this.loading.category_type_list = false
            this.updateCodeOrderList()
        },
        updateWarehouseList(){
            this.warehouse_add_dialog = false
            this.warehouse_edit_dialog = false
            this.loading.warehouse = true
            axios.get('products/warehouses').then((response)=>{
                this.warehouse = response.data.list
            })
            this.loading.warehouse = false
        },
        updateCodeOrderList(){
            axios.get('products/code_order').then((response)=>{
                this.codeOrderArray = response.data.data
            })
        },
        editCategoryTypeItem(item){
            axios.get('products/category_type/edit/'+item.id).then((response)=>{
                Object.keys(response.data.data).forEach((key)=>{
                    this.category_type_edit_formdata[key]['value'] = response.data.data[key].toString();
                })
                //console.log(this.category_type_edit_formdata);
                this.category_type_edit_id = item.id
                this.category_type_edit_dialog = true
            })
        },
        updatePricelist(){
            this.pricelist_add_dialog = false
            this.pricelist_edit_dialog = false
            this.loading.pricelist_list = true
            axios.get('products/pricelist').then((response)=>{
                this.pricelist = response.data.list
            })
            this.loading.pricelist_list = false
        },
        editPricelistItem(item){
            axios.get('products/pricelist/edit/'+item.id).then((response)=>{
                this.pricelist_edit_formdata = response.data.formdata
                this.pricelist_edit_id = item.id
                this.pricelist_edit_dialog = true
            })
        },
        deletePricelistItem(item){
            this.confirmDialog('Do you really want to delete this item?').then(res => {
                if(res == true){
                    this.loadingDialog = true
                    var fD = new FormData()
                    fD.append('id',item.id)
                    axios.post(window.axios.defaults.baseURL+item.delete_route,fD).then((response)=>{
                        if(response.status == 200){
                            this.loadingDialog = false
                            this.updatePricelist()

                        }
                        else{
                            this.loadingDialog = false
                            alert('Something went wrong!!');
                        }
                    })
                }
            })
        },
        deleteItem(item){
            this.confirmDialog('Do you really want to delete this item?').then(res => {
                if(res == true){
                    this.loadingDialog = true
                    var fD = new FormData()
                    fD.append('id',item.id)
                    axios.post(window.axios.defaults.baseURL+item.delete_route,fD).then((response)=>{
                        if(response.status == 200){
                            this.loadingDialog = false
                            this.updateCategoryTypeList()

                        }
                        else{
                            this.loadingDialog = false
                            alert('Something went wrong!!');
                        }
                    })
                }
            })
        },
        saveCodeOrder(){
            this.loadingDialog = true
            var fD = new FormData()
            var ids = new Array()
            Object.keys(this.codeOrderArray).forEach((key)=>{
                ids.push(this.codeOrderArray[key].id)
            })
            fD.append('ids',ids)
            axios.post('products/code_order',fD).then((response)=>{
                if(response.status == 200){
                    this.loadingDialog = false
                }
            })
        },
        resetCodeOrder(){
            this.codeOrderArray.sort(function(a,b){
                return parseInt(a.order)-parseInt(b.order)
            })
        }
    }
}
</script>
