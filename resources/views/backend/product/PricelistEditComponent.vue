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
                    <span class="headline">Edit Prcelist: {{formdata.name.value}}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12>
                                <v-text-field label="Name*" :error-messages="formdata.name.error" v-model="formdata.name.value"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click="$emit('close-pricelist-edit-dialog')">Close</v-btn>
                    <v-btn color="blue darken-1" flat @click="saveItem">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default{
    props:['dialog','formdata','id'],
    mounted(){

    },
    data(){
        return{
            success:false,
        }
    },
    methods:{
        saveItem(){
            var fD = new FormData();
            Object.keys(this.formdata).forEach((key)=>{
                fD.append(key,this.formdata[key]['value'])
            })
            axios.post('products/pricelist/edit/'+this.id,fD).then((response)=>{
                if(response.status == 200){
                    if(response.data.message == 'success'){
                        this.success=true
                        setTimeout(()=>{
                            this.$emit('close-pricelist-edit-dialog')
                            this.success=false
                        },600);
                        this.resetForm()
                    }
                    else{
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
            this.formdata = {
                'name':{
                    'value':'',
                    'error':'',
                },
            }
        }
    }
}
</script>
