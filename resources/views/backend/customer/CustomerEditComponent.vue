<template>
    <v-content style="">
        <div>
            <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
        </div>
        <v-container fluid class="pt-0">
            <v-toolbar color="white" tabs>
                <v-toolbar-title>Edit Customer: {{formdata.name.value}}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="update">Update</v-btn>
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
                                <v-flex xs12 px-2>
                                    <v-text-field label="Email" v-model="formdata.email.value" type="text" :error-messages="formdata.email.error"></v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap v-for="(phones,index) in formdata.phones" :key="index">
                                <v-flex xs12 px-2>
                                    <v-text-field :label="'Phone '+parseInt(index+1)" v-model="formdata.phones[index].value":error-messages="formdata.phones[index].error" :append-icon="formdata.phones.length > 1 ? 'remove_circle' : ''" @click:append="formdata.phones.splice(index,1)"></v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap >
                                <v-flex xs12 px-2>
                                    <v-btn @click="formdata.phones.push({'value':'','error':''})">Add Phone</v-btn>
                                </v-flex>
                            </v-layout>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item >
                        <v-card class="pa-4" flat>
                            <template v-for="(address,index) in formdata.addresses">
                                <v-card class="pa-4 mb-3">
                                    <v-layout row wrap>
                                        <v-flex xs12 px-2>
                                            <v-text-field label="Tag" :error-messages="formdata.addresses[index].tag.error" v-model="formdata.addresses[index].tag.value"
                                            :append-icon="formdata.addresses.length > 1 ? 'remove_circle' : ''" @click:append="formdata.addresses.splice(index,1)"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 px-2>
                                            <v-text-field label="Line 1" :error-messages="formdata.addresses[index].line1.error" v-model="formdata.addresses[index].line1.value"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 px-2>
                                            <v-text-field label="Line 2" :error-messages="formdata.addresses[index].line2.error" v-model="formdata.addresses[index].line2.value"></v-text-field>
                                        </v-flex>
                                        <v-flex lg3 md4 sm6 xs12 px-2>
                                            <v-text-field label="Pin Code" :error-messages="formdata.addresses[index].pin.error" v-model="formdata.addresses[index].pin.value"></v-text-field>
                                        </v-flex>
                                        <v-flex lg3 md4 sm6 xs12 px-2>
                                            <v-text-field label="State" :error-messages="formdata.addresses[index].state.error" v-model="formdata.addresses[index].state.value"></v-text-field>
                                        </v-flex>
                                        <v-flex lg3 md4 sm6 xs12 px-2>
                                            <v-text-field label="Country" :error-messages="formdata.addresses[index].country.error" v-model="formdata.addresses[index].country.value"></v-text-field>
                                        </v-flex>
                                        <v-flex lg3 md4 sm6 xs12 px-2>
                                            <v-text-field label="Telphone" :error-messages="formdata.addresses[index].tel.error" v-model="formdata.addresses[index].tel.value"></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                </v-card>
                                <v-divider class="mb-3"></v-divider>
                            </template>
                            <v-btn @click="addAddress">Add Address</v-btn>
                        </v-card>
                    </v-tab-item>
                </v-tabs-items>
                <v-layout px-3>
                    <v-flex xs12 class="text-xs-right">
                        <v-btn color="primary" @click="update">Update</v-btn>
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
            formFields:[],
            addRoute:'',
            formdata:{
                'name':{
                    'value':'','error':''
                },
                'email':{
                    'value':'','error':''
                },
                'phones':[
                    {
                        'value':'', 'error':'',
                    },
                ],
                'addresses':[
                    {
                        'tag':{
                            'value':'','error':''
                        },
                        'line1':{
                            'value':'','error':''
                        },
                        'line2':{
                            'value':'','error':''
                        },
                        'pin':{
                            'value':'','error':''
                        },
                        'state':{
                            'value':'','error':''
                        },
                        'country':{
                            'value':'','error':''
                        },
                        'tel':{
                            'value':'','error':''
                        }
                    }
                ],
            },
            tab: null,
            tabHeads: {'Details':{'error':false}, 'Addresses':{'error':false}},
            breadcrumbs:[
                    {
                        text:'Home',
                        disabled:false,
                        to:'/'
                    },
                    {
                        text:'Customers',
                        disabled:false,
                        to:'/customers/list'
                    },
                    {
                        text:'Edit',
                        disabled:true,
                        to:'/customers/edit'
                    },  
                ]
        }
    },
    mounted(){
        this.loadingDialog = true
        axios.get('customers/edit/'+this.$route.params.id).then((response)=>{
            this.formdata = response.data.formdata
            this.loadingDialog = false
        })
    },
    methods:{
        update(){
            this.loadingDialog=true
            var fD = new FormData()
            fD.append('data',JSON.stringify(this.formdata))
            axios.post('customers/edit/'+this.$route.params.id,fD).then((response)=>{
                this.loadingDialog = false
                if(response.data.message == 'success'){
                    this.$router.push('/customers/list')
                }
                if(response.data.message == 'failed'){
                    this.errorDialog = true
                    Object.keys(response.data.errors).forEach((key)=>{
                        var arr = key.split(".")
                        console.log(arr)
                        if (arr.length == 2){
                            this.formdata[arr[0]]['error'] = response.data.errors[key][0]
                        }
                        if (arr.length == 3){
                            this.formdata[arr[0]][arr[1]]['error'] = response.data.errors[key][0]
                        }
                        if (arr.length == 4){
                            this.formdata[arr[0]][arr[1]][arr[2]]['error'] = response.data.errors[key][0]
                        }
                        setTimeout(()=>{
                            this.errorDialog = false
                        },1000)
                    })
                }
            })
        },
        addAddress(){
            this.formdata.addresses.push(
            {
                'tag':{
                    'value':'','error':''
                },
                'line1':{
                    'value':'','error':''
                },
                'line2':{
                    'value':'','error':''
                },
                'pin':{
                    'value':'','error':''
                },
                'state':{
                    'value':'','error':''
                },
                'country':{
                    'value':'','error':''
                },
                'tel':{
                    'value':'','error':''
                }
            })
        }
    }
}
</script>
