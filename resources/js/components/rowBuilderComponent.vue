<template>
    <v-layout row wrap>
        <template v-for="item in row">
            <v-flex v-if="item.column" :class="item.class">
                <v-text-field
                v-if="item.textField"
                :label="item.label"
                :value="item.value"
                v-model="formdata[item.name]"
                v-on:input="changed"
                :disabled="item.disabled ? true :false"
                ></v-text-field>
                <v-select v-if="item.selectField" :items="item.options" :label="item.label" :value="item.value"></v-select>
            </v-flex>
            <row v-if="item.row" :row="item.items" v-on:form-updated="updFormData" :formdata="formdata"></row>
        </template>
    </v-layout>
</template>
<script>
export default{
    name:'row',
    props:['row','formdata'],
    mounted(){
        console.log(this.formdata)
    },
    methods:{
        changed(){
            this.$emit('form-updated',this.formdata)
        },
        updFormData(formdata){
            Object.keys(formdata).forEach((key)=>{
                this.formdata[key] = formdata[key]
            })
        },
    }

}
</script>
