<template>

    <v-navigation-drawer v-model="listFilterModel" app fixed right clipped>
        <v-layout row justify-space-between>
            <v-flex xs6>
                <v-subheader>Filter By </v-subheader>
            </v-flex>
            <v-flex xs2>
                <v-btn icon flat @click.stop="$emit('close-filter-drawer')"><v-icon>close</v-icon></v-btn>
            </v-flex>
        </v-layout>
        <v-layout row justify-space-between>
            <v-flex xs4>
                <v-btn color="primary" v-on:click="applyFilter">Apply</v-btn>
            </v-flex>
            <v-flex xs4>
                <v-btn color="dark" v-on:click="resetFilter">Reset</v-btn>
            </v-flex>
        </v-layout>
        <v-divider class="mt-3"></v-divider>
        <v-layout row wrap>
            <v-list v-for="(filterable,index) in filterables" :key="index" >
                <v-flex xs12 class="px-4" v-if="filterable.type == 'select'">
                    <v-select :label="filterable.name" :items="filterable.options" v-model="filterables[index].value"></v-select>
                </v-flex>
                <v-flex xs12 v-if="filterable.type == 'slider'" class="px-4">
                    <v-subheader class="pl-0 mb-2" style="justify-content:center">{{filterable.name}}</v-subheader>
                    <v-range-slider v-model = "filterables[index].range" :max="filterable.max_value" :min="filterable.min_value" thumb-label="always" ></v-range-slider>
                </v-flex>
            </v-list>
        </v-layout>
    </v-navigation-drawer>

</template>
<script>
export default{
    props:['filterables','listFilterModel','clickFilter','resetFilterAction'],
    watch:{
        clickFilter:{
            handler(){
                if(this.clickFilter == true){
                    this.applyFilter()
                }
            }
        },
        resetFilterAction:{
            handler(){
                console.log('kitti')
                if(this.resetFilterAction == true){
                    this.resetFilter()
                }
            }
        },
        filterables:{
            handler(){
                this.resetFilter()
            },
            deep:true
        }
    },
    methods:{
        resetFilter(){
            Object.keys(this.filterables).forEach((key)=>{

                if(this.filterables[key].type == 'select'){
                    this.filterables[key].value = '-1';
                }
                if(this.filterables[key].type == 'slider'){
                    this.filterables[key].range = this.filterables[key].default;
                }
            });
            this.applyFilter()
            this.$emit('reset-filter')
        },
        applyFilter(){
            var str = '';
            Object.keys(this.filterables).forEach((key)=>{
                if(this.filterables[key].type == 'select'){
                    if(this.filterables[key].value != '-1'){
                        str = str+"&"+key+"="+this.filterables[key].value
                    }
                }
                if(this.filterables[key].type == 'slider'){
                    if(this.filterables[key].range[0] != this.filterables[key].default[0]
                    || this.filterables[key].range[1] != this.filterables[key].default[1]){
                        str = str+"&"+key+"="+this.filterables[key].range[0]+","+this.filterables[key].range[1]
                    }
                }
            });
            this.$emit('apply-filter',str);
        }
    }
}
</script>
