<template>
    <div>
        
    </div>
</template>
<script>
    export default{
        data(){
            return{
                formData:{
                },
            }
        },
        props:['formFields','config','addModel'],
        mounted:function(){
        },
        watch:{
            formFields:function(newFields){
                newFields.forEach((item,index)=>{
                    this.formData[item.name]='';
                });
            }
        },
        methods:{
            saveFn:function(){
                console.log(this.formFields);
                var fD = new FormData();
                this.formFields.forEach((item,index)=>{
                    fD.append(item.name,this.formData[item.name]);
                });
                axios.post(this.config.route,fD).then((response)=>{
                    if (response.status == 200) {
                        if(response.data.message == "success"){
                            this.$emit('close-model');
                            //this.$emit('get-list');
                        }
                        else{
                            var req = response.data.request;
                            var err = response.data.errors;
                            this.generateValidation(req,err);
                        }
                    }
                    else{
                        console.log(response.status);
                    }
                }).catch((error)=>{
                    console.log(error);
                });
            },
            generateValidation(req,err){
                this.formFields.forEach((item,index)=>{
                    if(err[item.name]){
                        this.formFields[index]['errormsg']= err[item.name][0];
                        console.log(this.formFields);
                    }
                });
            },
        }
    }
</script>
