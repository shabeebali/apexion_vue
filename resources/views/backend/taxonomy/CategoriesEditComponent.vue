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
                    <span class="headline">Add Category</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12>
                                <v-text-field label="Name*" :error-messages="formdata.name.error" v-model="formdata.name.value"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-select label="Category Type" v-model="formdata.type_id.value" :items="category_type.options"></v-select>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field label="Code*" :error-messages="formdata.code.error" v-model="formdata.code.value" :readonly="formdata.code.readonly"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click="$emit('close-edit-dialog')">Close</v-btn>
                    <v-btn color="blue darken-1" flat @click="saveItem">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default{
    props:['dialog','id'],
    data(){
        return{
            formdata:{
                'name':{
                    'value':'',
                    'error':'',
                },
                'code':{
                    'value':'',
                    'error':'',
                    'readonly':false,
                },
                'type_id':{
                    'value':''
                },
            },
            success:false,
            category_type:{
                'options':[],
            },
            response:[],
        }
    },
    computed:{
        type_id(){
            return this.formdata.type_id.value
        }
    },
    watch:{
        id:{
            handler(){
                if(this.id == 0){
                    this.resetForm()
                }
                else{
                    axios.get('products/categories/edit/'+this.id).then((response)=>{
                        this.response = response.data.formdata
                        if(response.status == 200){
                            Object.keys(response.data.formdata).forEach((key)=>{
                                this.formdata[key]['value'] = response.data.formdata[key];
                            })
                        }
                    })
                }
            }
        },
        type_id:{
            handler(){
                //this.formdata.code.value = ''
                this.formdata.code.readonly = false
                Object.keys(this.category_type.options).forEach((key)=>{
                    if(this.formdata.type_id.value == this.category_type.options[key].value){
                        if(this.formdata.type_id.value != this.response.type_id){
                            if(this.category_type.options[key].autogen){
                                this.formdata.code.readonly = true
                                this.formdata.code.value = this.category_type.options[key].next_code
                            }
                        }
                        else{
                            if(this.category_type.options[key].autogen){
                                this.formdata.code.readonly = true
                            }
                            this.formdata.code.value = this.response.code
                        }
                    }

                })
            },
        }
    },
    mounted(){
        axios.get('products/categories/add').then((response)=>{
            if(response.status == 200){
                this.category_type.options = response.data.category_type
            }
            else{
                console.log(response)
            }
        })
    },
    methods:{
        saveItem(){
            var fD = new FormData();
            Object.keys(this.formdata).forEach((key)=>{
                fD.append(key,this.formdata[key]['value'])
            })
            console.log(this.formdata)
            axios.post('products/categories/edit/'+this.id,fD).then((response)=>{
                if(response.status == 200){
                    if(response.data.message == 'success'){
                        this.success=true
                        setTimeout(()=>{
                            this.$emit('close-edit-dialog')
                            this.success=false
                            this.resetForm()
                        },600);
                    }
                    else{
                        Object.keys(this.formdata).forEach((key)=>{
                            this.formdata[key].error = ''
                        })
                        Object.keys(response.data.errors).forEach((key)=>{
                            this.formdata[key]['error'] = response.data.errors[key]
                        })
                    }
                }
                else{
                    console.log(response)
                }
            })
        },
        resetForm(){
            this.formdata={
                'name':{
                    'value':'',
                    'error':'',
                },
                'code':{
                    'value':'',
                    'error':''
                },
                'type_id':{
                    'value':''
                },
            }
        }
    }
}
</script>
