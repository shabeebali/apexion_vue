<template>
    <div>
        <template v-for="item in formFields">
            <row-builder v-if="item.row" :row="item.items" v-on:form-updated="updFormData" :formdata="formdata"></row-builder>
        </template>
    </div>
</template>
<script>
import rowBuilder from './rowBuilderComponent.vue'
export default{
    props:['addRoute','formFields','formdata'],

    components:{
        'row-builder':rowBuilder,
    },
    methods:{
        updFormData(formdata){
            Object.keys(formdata).forEach((key)=>{
                this.formdata[key] = formdata[key]
            })
            this.$emit('form-updated',this.formdata)
        },

    }
}
</script>
