<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" tabs>
                <v-toolbar-title>Add Product</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="add">Add</v-btn>
                <template v-slot:extension>
                    <v-tabs v-model="tab" color="white" grow >
                        <v-tabs-slider color="yellow"></v-tabs-slider>
                        <v-tab v-for="(head,key) in tabHeads" :key="key">
                            <v-badge right v-if="head.error" color="white">
                                <template v-slot:badge>
                                    <v-icon color="red" small>error</v-icon>
                                </template>
                                <span>{{ key }}</span>
                            </v-badge>
                            <span v-else>{{ key }}</span>
                        </v-tab>
                    </v-tabs>
                </template>
            </v-toolbar>
            <v-card>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <v-card class="pa-4" flat>
                            <v-layout row wrap>
                                <v-flex xs12 px-2>
                                    <v-text-field label="Name" v-model="formdata.name.value" :error-messages="formdata.name.error"></v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex lg3 md6 xs12 px-2>
                                    <v-text-field label="HSN Code" v-model="formdata.hsn.value" type="text"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2>
                                    <v-text-field label="Weight" v-model="formdata.weight.value" type="text" :error-messages="formdata.weight.error"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2>
                                    <v-select label="GST" :items="formdata.gst.options" v-model="formdata.gst.value" ></v-select>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2>
                                        <v-text-field label="MRP" v-model="formdata.mrp.value" type="text" :error-messages="formdata.mrp.error"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2>
                                    <v-text-field label="Landing Price" v-model="formdata.landing_price.value" type="text" :error-messages="formdata.landing_price.error"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2>
                                    <v-text-field label="General Selling Price" v-model="formdata.general_selling_price.value" type="text" :error-messages="formdata.general_selling_price.error"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2>
                                    <v-text-field label="General Selling Price (Dealer)" v-model="formdata.general_selling_dealer.value" type="text" :error-messages="formdata.general_selling_dealer.error"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md6 xs12 px-2 v-for="(item,i)  in npc_category_types" :key="i">
                                    <v-select :label="item.label" :items="item.options" v-model="item.value" :error-messages="item.error"></v-select>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs12 px-2>
                                    <v-textarea label="Remarks" v-model="formdata.remarks.value"></v-textarea>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex xs12 px-2>
                                    <v-textarea label="Description" v-model="formdata.description.value"></v-textarea>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-if="pendingFlag">
                                <v-flex xs12 px-2>
                                    <v-switch v-model="formdata.pending.value" true-value="1" false-value="0" label="Approved?"></v-switch>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-if="tallFlag">
                                <v-flex xs12 px-2>
                                    <v-switch v-model="formdata.tally.value" true-value="1" false-value="0" label="Synced with Tally?"></v-switch>
                                </v-flex>
                            </v-layout>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item >
                        <v-card class="pa-4" flat>
                            <v-layout row wrap>
                                <v-flex lg6 md6 xs12>
                                    <v-layout column wrap>
                                        <v-flex px-2 v-for="(item,i) in pc_category_types" :key="i">
                                            <v-combobox :label="item.label" v-model="item.value" :items="item.options" :error-messages="item.error"></v-combobox>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>
                            </v-layout>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item >
                        <v-card class="pa-4" flat v-if="Object.keys(pricelist).length > 0">
                            <v-layout row wrap v-for="(p,index) in pricelist" :key="index">
                                <v-flex lg3 md4 sm4 xs6 px-2>
                                    <v-text-field :label="p.name+' price margin (%)'" v-model="pricelist[index].value" 
                                    @input="updatePricelist(index)"></v-text-field>
                                </v-flex>
                                <v-flex lg3 md4 sm4 xs6 px-2>
                                    <v-text-field label="price" 
                                    :value="calculated_price(pricelist[index].value)"
                                    readonly 
                                    :error-messages="p.error"></v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item >
                        <v-card class="pa-4" flat v-if="Object.keys(warehouse).length > 0">
                            <v-layout row wrap v-for="(p,index) in warehouse" :key="index">
                                <v-flex lg3 md4 sm4 xs6 px-2>
                                    <v-text-field :label="'Warehouse: '+p.name" :error-messages="p.error" v-model="warehouse[index].value"></v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-card>
                    </v-tab-item>
                </v-tabs-items>
                <v-layout px-3>
                    <v-flex xs12 class="text-xs-right">
                        <v-btn color="primary" @click="add">Add</v-btn>
                    </v-flex>
                </v-layout>
            </v-card>
            <v-dialog v-model="loadingDialog" hide-overlay persistent width="300">
              <v-card color="teal" dark>
                <v-card-text>
                  Please stand by
                  <v-progress-linear
                    indeterminate
                    color="white"
                    class="mb-0"
                  ></v-progress-linear>
                </v-card-text>
              </v-card>
            </v-dialog>
            <v-dialog v-model="errorDialog" hide-overlay persistent width="300">
              <v-card color="red" dark>
                <v-card-text>
                  Please correct the errors in the form
                </v-card-text>
              </v-card>
            </v-dialog>
        </v-container>
    </v-content>
</template>
<script>
export default{
    data(){
        return{
            loadingDialog : false,
            errorDialog:false,
            pendingFlag:false,
            tallFlag:false,
            formFields:[],
            addRoute:'',
            formdata:{
                'name':{
                    'value':'','error':''
                },
                'weight':{
                    'value':'0','error':''
                },
                'mrp':{
                    'value':'0','error':''
                },
                'hsn':{
                    'value':'','error':''
                },
                'landing_price':{
                    'value':'0','error':''
                },
                'general_selling_price':{
                    'value':'0','error':''
                },
                'general_selling_dealer':{
                    'value':'0','error':''
                },
                'remarks':{
                    'value':'','error':''
                },
                'description':{
                    'value':'','error':''
                },
                'pending':{
                    'value':0,'error':''
                },
                'tally':{
                    'value':0,'error':''
                },
                'gst':{
                    'value':'12',
                    'options':[
                        {'text':'12%','value':'12'},
                        {'text':'18%','value':'18'},
                        {'text':'5%','value':'5'},
                    ]
                }
            },
            pc_category_types:[],
            npc_category_types:[],
            pricelist:[],
            warehouse:[],
            tab: null,
            tabHeads: {
                'General':{
                    'error':false
                },
                'Product Code':{
                    'error':false
                },
                'Pricelist':{
                    'error':false
                },
                'Stock':{
                    'error':false
                }
            },
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
                        text:'Add',
                        disabled:true,
                        to:'/products/add'
                    },  
                ]
        }
    },
    mounted(){
        this.loadingDialog = true
        axios.get('products/add').then((response)=>{
            if(response.status == 200){
                if(response.data.npc_category_types !== undefined){
                    this.npc_category_types = response.data.npc_category_types
                }
                if(response.data.pc_category_types !== undefined){
                    this.pc_category_types = response.data.pc_category_types
                }
                if(response.data.pricelist !== undefined){
                    this.pricelist = response.data.pricelist
                }
                if(response.data.warehouse !== undefined){
                    this.warehouse = response.data.warehouse
                }
                this.pendingFlag = response.data.pendingflag
                this.tallFlag = response.data.tallyflag
                this.loadingDialog = false
            }
            else{
                alert('Something went wrong!!')
                console.log(response);
            }
        })
    },
    computed:{
        mrp(){
            return this.formdata.mrp.value
        },
        landing_price(){
            return this.formdata.landing_price.value
        },
        gst(){
            return this.formdata.gst.value
        }
    },
    watch:{
        mrp(){
            this.updatePricelist()
        },
        landing_price(){
            this.updatePricelist()
        },
        gst(){
            this.updatePricelist()
        }
    },
    methods:{
        add(){
            this.loadingDialog=true
            var fD = new FormData()
            Object.keys(this.formdata).forEach((key)=>{
                fD.append(key,this.formdata[key]['value'])
            })
            Object.keys(this.pc_category_types).forEach((key)=>{
                if(this.pc_category_types[key]['value'].value !== undefined){
                    fD.append(key,this.pc_category_types[key]['value'].value) //combobox
                }
                else{
                    fD.append(key,'')
                }
            })
            Object.keys(this.npc_category_types).forEach((key)=>{
                if(this.npc_category_types[key]['value'] !== undefined){
                    fD.append(key,this.npc_category_types[key]['value']) //select
                }
                else{
                    fD.append(key,'')
                }
            })
            Object.keys(this.pricelist).forEach((key)=>{
                if(this.pricelist[key]['value'] !== undefined){
                    fD.append(key,this.pricelist[key]['value']) //select
                }
                else{
                    fD.append(key,'')
                }
            })
            Object.keys(this.warehouse).forEach((key)=>{
                if(this.warehouse[key]['value'] !== undefined){
                    fD.append(key,this.warehouse[key]['value']) //select
                }
                else{
                    fD.append(key,'')
                }
            })
            axios.post('products/add',fD).then((response)=>{

                if(response.status == 200 && response.data.message == 'success'){
                    this.loadingDialog = false,
                    this.$router.push('/products/list')
                }
                else if(response.status == 200 && response.data.message == 'failed'){
                    this.clearError()
                    this.loadingDialog = false
                    this.errorDialog = true
                    Object.keys(response.data.errors).forEach((key)=>{
                        if(this.formdata[key] !== undefined){
                            this.formdata[key]['error'] = response.data.errors[key]
                        }
                        if(this.pc_category_types[key] !== undefined){
                            this.pc_category_types[key]['error'] = response.data.errors[key]
                        }
                        if(this.npc_category_types[key] !== undefined){
                            this.npc_category_types[key]['error'] = response.data.errors[key]
                        }
                        if(this.warehouse[key] !== undefined){
                            this.warehouse[key]['error'] = response.data.errors[key]
                        }
                    })
                    Object.keys(this.formdata).forEach((key)=>{
                        if('error' in this.formdata[key]){
                            if(this.formdata[key]['error'].length > 0){
                                this.tabHeads.General.error=true
                                return false
                            }
                        }
                    })
                    Object.keys(this.pc_category_types).forEach((key)=>{
                        if('error' in this.pc_category_types[key]){
                            if(this.pc_category_types[key]['error'].length > 0){
                                this.tabHeads['Product Code'].error = true
                            }
                        }
                    })
                    Object.keys(this.npc_category_types).forEach((key)=>{
                        if('error' in this.npc_category_types[key]){
                            if(this.npc_category_types[key]['error'].length > 0){
                                this.tabHeads['General'].error = true
                            }
                        }
                    })
                    Object.keys(this.warehouse).forEach((key)=>{
                        if('error' in this.warehouse[key]){
                            if(this.warehouse[key]['error'].length > 0){
                                this.tabHeads['Stock'].error = true
                            }
                        }
                    })
                    setTimeout(()=>{
                        this.errorDialog = false
                    },1000)
                }
            })
        },
        clearError(){
            Object.keys(this.tabHeads).forEach((key)=>{
                this.tabHeads[key].error = false
            })
            Object.keys(this.formdata).forEach((key)=>{
                this.formdata[key]['error'] = ''
            })
            Object.keys(this.pc_category_types).forEach((key)=>{
                this.pc_category_types[key]['error'] = ''
            })
            Object.keys(this.npc_category_types).forEach((key)=>{
                this.npc_category_types[key]['error'] = ''
            })
            Object.keys(this.warehouse).forEach((key)=>{
                this.warehouse[key]['error'] = ''
            })
        },
        calculated_price(el){
            return ((parseFloat(this.formdata.landing_price.value)*((parseFloat(el)/100)+1))*((parseFloat(this.formdata.gst.value)/100)+1)).toFixed(2)
        },
        updatePricelist(index = null){
            if(index !== null){
                this.pricelist[index].error = ''
                if(this.calculated_price(this.pricelist[index].value) > parseFloat(this.formdata.mrp.value)){
                    this.pricelist[index].error = 'Warning: This price exceeded MRP'
                }
            }
            else{
                Object.keys(this.pricelist).forEach((index)=>{
                    this.pricelist[index].error = ''
                    if(this.calculated_price(this.pricelist[index].value) > parseFloat(this.formdata.mrp.value)){
                        this.pricelist[index].error = 'Warning: This price exceeded MRP'
                    }
                })
            }
        }
    }
}
</script>
