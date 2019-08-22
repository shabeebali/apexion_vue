<template>
    <div>
        <v-dialog v-model="dialog" lazy persistent>
            <v-card v-if="success" class="pa-4">
                <v-layout row wrap>
                    <v-flex xs12 >
                        <div class="text-xs-center">
                            <v-icon x-large color="green lighten-1">check_circle</v-icon>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <div class="text-xs-center">
                            <div>Saved</div>
                        </div>
                    </v-flex>
                </v-layout>
            </v-card>
            <v-card v-else>
                <v-card-title>
                    <span class="headline">Add Category Type</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12>
                                <v-text-field label="Name*" :error-messages="formdata.name.error" v-model="formdata.name.value"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-select label="Code Length" v-model="formdata.code_length.value" :items="code_length.options"></v-select>
                            </v-flex>
                            <v-flex xs12>
                                <v-switch v-model="formdata.autogen.value" true-value="1" false-value="0" label="Auto Generate?"></v-switch>
                            </v-flex>
                            <v-flex xs12>
                                <v-select :disabled="formdata.autogen.value != '1'" v-model="formdata.code_type.value" label="Code Type" :items="code_type[formdata.code_length.value]['options']"></v-select>
                            </v-flex>
                            <v-flex xs12>
                                <v-switch v-model="formdata.in_pc.value" true-value="1" false-value="0" label="Include in Product Code?"></v-switch>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="$emit('close-category-type-add-dialog');resetForm();">Close</v-btn>
                    <v-btn color="blue darken-1" text @click="saveItem">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default{
    props:['dialog'],

    data(){
        return{
            success:false,
            formdata:{
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
            code_length:{
                'options':[
                    {
                        'text':'1','value':'1',
                    },
                    {
                        'text':'2','value':'2',
                    },
                    {
                        'text':'3','value':'3',
                    }
                ],
            },
            code_type:{
                '1':{
                    'options':[
                        {
                            'text':'A','value':'alpha',
                        },
                        {
                            'text':'1','value':'numeric',
                        }
                    ]
                },
                '2':{
                    'options':[
                        {
                            'text':'AA','value':'alpha-alpha',
                        },
                        {
                            'text':'A1','value':'alpha-numeric',
                        },
                        {
                            'text':'1A','value':'numeric-alpha',
                        },
                        {
                            'text':'11','value':'numeric-numeric',
                        },
                    ]
                },
                '3':{
                    'options':[
                        {
                            'text':'AAA','value':'alpha-alpha-alpha',
                        },
                        {
                            'text':'AA1','value':'alpha-alpha-numeric',
                        },
                        {
                            'text':'A1A','value':'alpha-numeric-alpha',
                        },
                        {
                            'text':'1AA','value':'numeric-alpha-alpha',
                        },
                        {
                            'text':'11A','value':'numeric-numeric-alpha',
                        },
                        {
                            'text':'1A1','value':'numeric-alpha-numeric',
                        },
                        {
                            'text':'A11','value':'alpha-numeric-numeric',
                        },
                        {
                            'text':'111','value':'numeric-numeric-numeric',
                        },
                    ]
                },
            }
        }
    },
    methods:{
        saveItem(){
            var fD = new FormData();
            Object.keys(this.formdata).forEach((key)=>{
                fD.append(key,this.formdata[key]['value'])
            })
            axios.post('products/category_type/add',fD).then((response)=>{
                if(response.status == 200){
                    if(response.data.message == 'success'){
                        this.success=true
                        setTimeout(()=>{
                            this.$emit('close-category-type-add-dialog')
                            this.success=false
                        },600);

                        this.resetForm()
                    }
                    else{
                        Object.keys(response.data.errors).forEach((key)=>{
                            this.formdata[key]['error'] = response.data.errors[key]
                        })
                        console.log(this.formdata)
                    }
                }
                else{
                    console.log(response)
                }
            })
        },
        resetForm(){
            this.formdata = {
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
            }
        }
    }
}
</script>
