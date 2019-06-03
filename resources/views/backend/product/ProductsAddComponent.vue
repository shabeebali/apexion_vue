<template>
    <v-content style="">
        <v-container fluid>
            <v-toolbar color="white" tabs>
                <v-toolbar-title>Add Product</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="primary">Add</v-btn>
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
                                    <v-text-field label="Stock" v-model="formdata.stock.value" type="text" :error-messages="formdata.stock.error"></v-text-field>
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
                                    <v-select :label="item.label" :items="item.options" :error-messages="item.error"></v-select>
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
                        </v-card>
                    </v-tab-item>
                    <v-tab-item >
                        <v-card class="pa-4" flat>
                            <v-layout row wrap>
                                <v-flex lg6 md6 xs12>
                                    <v-layout column wrap>
                                        <v-flex px-2 v-for="(item,i) in pc_category_types" :key="i">
                                            <v-select :label="item.label" v-model="item.value" :items="item.options" :error-messages="item.error"></v-select>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>
                                <v-flex lg6 md6 xs12>
                                    <v-layout row wrap align-center fill-height>
                                        <v-flex px-4 >
                                            <v-text-field box readonly label="Product Code" type="text"></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>
                            </v-layout>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item >
                        <v-card flat>
                            <v-card-text>{{ text }}3</v-card-text>
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
                  Please corect the erros in the form
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
            formFields:[],
            addRoute:'',
            formdata:{
                'name':{
                    'value':'','error':''
                },
                'weight':{
                    'value':'0','error':''
                },
                'stock':{
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
            pricelist:null,
            tab: null,
            tabHeads: {'General':{'error':false}, 'Product Code':{'error':false}, 'Pricelist':{'error':false}},
            text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
        }
    },
    mounted(){
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
            }
            else{
                alert('Something went wrong!!')
                console.log(response);
            }
        })
    },
    watch:{
        formdata:{
            handler(){
                Object.keys(this.formdata).forEach((key)=>{
                    if('error' in this.formdata[key]){
                        if(this.formdata[key]['error'].length > 0){
                            this.tabHeads.General.error=true
                            return false
                        }
                    }
                })
            },
            deep:true
        },
        pc_category_types:{
            handler(){
                Object.keys(this.pc_category_types).forEach((key)=>{
                    if('error' in this.pc_category_types[key]){
                        if(this.pc_category_types[key]['error'].length > 0){
                            this.tabHeads['Product Code'].error = true
                        }
                    }
                })
            },
            deep:true
        }
    },
    components:{
    },
    methods:{
        add(){
            this.loadingDialog=true
            var fD = new FormData()
            Object.keys(this.formdata).forEach((key)=>{
                fD.append(key,this.formdata[key]['value'])
            })
            Object.keys(this.pc_category_types).forEach((key)=>{
                fD.append(key,this.pc_category_types[key]['value'])
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
                        else if(this.pc_category_types[key] !== undefined){
                            this.pc_category_types[key]['error'] = response.data.errors[key]
                        }
                        else{

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
        }
    }
}
</script>
