<template>
    <div>
        <v-navigation-drawer v-model="listFilterModel" app fixed right clipped>
            <v-layout row justify-space-between>
                <v-flex xs6>
                    <v-subheader>Filter By </v-subheader>
                </v-flex>
                <v-flex xs2>
                    <v-btn icon flat @click.stop="listFilterModel = false"><v-icon>close</v-icon></v-btn>
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
        <v-content :class="listClass">
            <v-container fluid>
                <v-layout  row mb-2>
                    <v-subheader class="headline">{{listHeadline}}</v-subheader>
                    <v-spacer></v-spacer>
                    <v-btn class="" flat icon @click.stop="listFilterModel=!listFilterModel"><v-icon>filter_list</v-icon></v-btn>
                    <v-btn v-if="Object.keys(listFields).length > 0" class="" flat icon @click.stop="listSettingsModel=true"><v-icon>settings</v-icon></v-btn>
                    <v-btn v-if="addFlag" class="primary" @click="$emit('open-add-dialog')">Add</v-btn>
                </v-layout>
                <v-card>
                    <template v-if="myfiltered">
                        <v-layout row wrap v-for="ff in myfiltered" :key="ff.key">
                            <v-flex xs2 v-if="ff.type =='select'">
                                <v-chip v-on:input="resetItem(ff.key)" close>{{ff.name}}: {{ff.value}}</v-chip>
                            </v-flex>
                            <v-flex xs2 v-if="ff.type == 'slider'">
                                <v-chip v-on:input="resetItem(ff.key)" close>{{ff.range[0]}} < {{ff.name}} > {{ff.range[1]}}</v-chip>
                            </v-flex>
                        </v-layout>
                    </template>
                    <div class="v-datatable v-table v-datatable--select-all theme--light">
                        <div class="v-datatable__actions">
                            <div class="v-datatable__actions__search ml-4 mb-2" style="flex: 1 1 0;">
                                <v-text-field
                                v-model="search"
                                append-icon="search"
                                label="Search"
                                single-line
                                hide-details
                                ></v-text-field>
                            </div>
                            <div class="v-datatable__actions__select">
                                Rows per page:
                                <v-select :items="rowsPerPageSelectItems" hide-details v-on:change="rowsPerPageUpdate" v-model="pagination.rowsPerPage">
                                </v-select>
                            </div>
                            <div class="v-datatable__actions__range-controls">
                                <div class="v-datatable__actions__pagination">
                                    {{pageControlText}}
                                </div>
                                <v-btn flat icon :disabled="pagination.page ==1" v-on:click="pagination.page -= 1">
                                    <v-icon light>chevron_left</v-icon>
                                </v-btn>
                                <v-btn flat icon :disabled="pagination.page == Math.ceil(totalItems/pagination.rowsPerPage)" v-on:click="pagination.page += 1">
                                    <v-icon light>chevron_right</v-icon>
                                </v-btn>
                            </div>
                        </div>
                    </div>
                    <v-data-table
                    class="apex-list"
                    :headers="tableHeaders"
                    :items="tableItems"
                    item-key="id"
                    select-all

                    v-model="selected"
                    :loading="loading"
                    :pagination.sync="pagination"
                    >
                        <template v-slot:items="props">
                            <td>
                                <v-checkbox
                                v-model="props.selected"
                                primary
                                hide-details
                                ></v-checkbox>
                            </td>
                            <template v-for="(it,key) in props.item" v-if="key != 'id'">
                                <td v-if="it.actions" class="justify-end layout">
                                    <v-tooltip bottom v-if="it.edit">
                                        <template v-slot:activator="{ on }">
                                            <v-btn icon v-on="on" @click="editItem(props.item)">
                                                <v-icon  small>edit</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Edit</span>
                                    </v-tooltip>
                                    <v-tooltip bottom v-if="it.delete">
                                        <template v-slot:activator="{ on }">
                                            <v-btn icon v-on="on" @click="deleteIds.push(props.item.id); deleteDialog = true;">
                                                <v-icon  small>delete</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Delete</span>
                                    </v-tooltip>
                                </td>
                                <td v-else>{{it}}</td>
                            </template>
                        </template>
                        <template v-slot:no-results>
                            <v-alert :value="true" color="error" icon="warning">
                                Your search for "{{ search }}" found no results.
                            </v-alert>
                        </template>
                        <template v-slot:no-data>
                            <v-alert :value="true" color="info" icon="warning">
                                No data available. Please Add.
                            </v-alert>
                        </template>
                        <template v-slot:footer>
                            <v-layout class="justify-start">
                                <v-btn @click="deleteMany" color="error" :disabled="selected.length == 0">Delete</v-btn>
                            </v-layout>
                        </template>
                    </v-data-table>
                    <v-dialog v-model="listSettingsModel" persistent max-width="600px" v-if="listFields">
                        <v-card>
                            <v-card-title>
                                <span class="headline">Fields to include in table</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container grid-list-md>
                                    <v-layout wrap >
                                        <template v-for="(ff,key) in listFields" v-if="key != 'id'">
                                            <v-flex xs12>
                                                <v-switch v-model="ff.selected" :label="ff.text"></v-switch>
                                            </v-flex>
                                        </template>
                                    </v-layout>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" flat @click="listSettingsModel = false">Close</v-btn>
                                <v-btn color="blue darken-1" flat @click="saveListSettings">Save</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="deleteDialog" persistent max-width="300px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">Are you sure want to delete this item ?</span>
                            </v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="green darken-1" flat @click="deleteDialog = false">No</v-btn>
                                <v-btn color="red darken-1" flat @click="deleteItem">Yes</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="loadingDialog"
                      hide-overlay
                      persistent
                      width="300"
                    >
                      <v-card
                        color="teal"
                        dark
                      >
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
                </v-card>
            </v-container>
        </v-content>
    </div>
</template>
<style>
.apex-list .v-datatable__actions__search{
    display: flex;
    flex:1 1 0;
}
.apex-list .v-datatable.v-table thead{
    background-color: #f5f5f5 !important;
    border-color: #f5f5f5 !important;
}
.apex-list .v-datatable.v-table th:first-child{
    width:80px;
}
</style>
<script>
export default{
    data(){
        return{
            deleteDialog:false,
            listRoute:'',
            listFilterModel:false,
            listSettingsModel:false,
            selected:[],
            search:'',
            pagination:{},
            loading:false,
            tableItems:[],
            tableHeaders:[],
            totalItems:0,
            listFields:[],
            myfiltered:[],
            deleteIds:[],
            loadingDialog:false,
            rowsPerPageSelectItems:[
                {
                    'text':'5',
                    'value':5
                },
                {
                    'text':'10',
                    'value':10
                },
                {
                    'text':'25',
                    'value':25
                },
                {
                    'text':'All',
                    'value':-1
                }
            ],
            pageControlText:'',
        }
    },
    props:['baseRoute','listSettingsRoute','filterables','triggerUpdate','addFlag','listHeadline','listClass'],
    watch: {
        pagination: {
            handler () {
                const { sortBy, descending, page, rowsPerPage } = this.pagination
                let pageStart=(page-1)*rowsPerPage+1;
                let pageStop =  Math.min(page*rowsPerPage,this.totalItems);
                if(this.totalItems > 0){
                    this.pageControlText = pageStart.toString()+'-'+pageStop.toString()+' of '+this.totalItems.toString();
                }
                else{
                    this.pageControlText = '-'
                }
            },
            deep: true
        },
        search: {
            handler () {
                this.deboucedSearch();

            },
            deep: true
        },
        listRoute:{
            handler(){
                this.updateList()
            }
        },
        baseRoute:{
            handler(){
                this.listRoute = this.baseRoute
            }
        },
        triggerUpdate:{
            handler(){
                if(this. triggerUpdate == true){
                    this.updateList()
                }
            }
        }
    },
    mounted(){
        this.listRoute = this.baseRoute
        this.deboucedSearch = _.debounce(()=>{
            this.updateList()
        },300);
        this.updateList()
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
            this.listRoute = this.baseRoute+str
        },
        updateList(){
            this.getDataFromApi(this.search)
            .then(data => {
                this.tableItems = data.items
                this.totalItems = data.total
                this.tableHeaders = data.headers
                this.listFields = data.fields
                this.myfiltered = data.myfiltered
                console.log(this.tableHeaders)
                console.log(this.tableItems)
            })


        },
        resetItem($event){
            Object.keys(this.filterables).forEach((key)=>{
                if(key == $event){
                    if(this.filterables[key].type == 'select'){
                        this.filterables[key].value = '-1'
                    }
                    if(this.filterables[key].type == 'slider'){
                        this.filterables[key].range = this.filterables[key].default
                    }
                }
            });
            this.applyFilter()
        },
        saveListSettings:function(){
            var fD = new FormData();
            var fd_str='';
            Object.keys(this.listFields).forEach((key)=>{
                if(this.listFields[key].selected){
                    fd_str = fd_str+key+",";
                }
            })

            fd_str = fd_str.substring(0, fd_str.length - 1);
            fD.append('fields',fd_str);
            axios.post(this.listSettingsRoute,fD).then((response)=>{
                if (response.status == 200) {
                    if(response.data.message == "success"){
                        this.listSettingsModel = false
                        this.updateList()
                    }
                }
                else{
                    console.log(response);
                }
            }).catch((error)=>{
                console.log(error);
            });
        },
        editItem(item){
            this.$emit('open-edit-dialog',item.id)
        },
        deleteItem(){
            this.deleteDialog=false
            this.loadingDialog=true
            var fD = new FormData()
            var delete_ids = new Array()
            this.deleteIds.forEach((id)=>{
                delete_ids.push(id)
            })
            fD.append('delete_ids',delete_ids)
            axios.post(this.baseRoute.substr(0,(this.baseRoute.length -1))+'/delete',fD).then((response)=>{
                if(response.status == 200 && response.data.message == 'success'){
                    this.loadingDialog = false
                    this.updateList()
                    this.deleteId = new Array()
                }
                else{
                    alert('Something went wrong!')
                }
            })
        },
        deleteMany(){
            this.deleteIds = new Array()
            this.selected.forEach((item)=>{
                this.deleteIds.push(item.id)
            })
            this.deleteItem()
        },
        getDataFromApi(search){
            this.loading = true;
            const { sortBy, descending, page, rowsPerPage } = this.pagination
            return this.getItems(page,rowsPerPage,search).then((data)=>{
                let total = data.total;
                let items = data.items;
                let headers = data.headers;
                let fields = data.fields;
                let myfiltered = data.myfiltered;
                /*
                if (this.pagination.sortBy) {
                    items = items.sort((a, b) => {
                        let sortA = a[sortBy]
                        let sortB = b[sortBy]
                        if(!isNaN(sortA)){
                            sortA = parseFloat(sortA);
                        }
                        if(!isNaN(sortB)){
                            sortB = parseFloat(sortB);
                        }
                        if (descending) {
                            if (sortA < sortB) return 1
                            if (sortA > sortB) return -1
                            return 0
                        } else {
                            if (sortA < sortB) return -1
                            if (sortA > sortB) return 1
                            return 0
                        }
                    })
                }*/
                let pageStart=(page-1)*rowsPerPage+1;
                let pageStop =  Math.min(page*rowsPerPage,total);
                if(total > 0){
                    this.pageControlText = pageStart.toString()+'-'+pageStop.toString()+' of '+total.toString();
                }
                else{
                    this.pageControlText = '-'
                }
                var data2={
                    'items':items,
                    'headers':headers,
                    'total':total,
                    'fields':fields,
                    'myfiltered':myfiltered
                }
                this.loading=false
                return data2
            })

        },
        getItems(page,rowsPerPage,search){
            var data={
                'items':[],
                'headers':[],
            };
            var items = [];
            var headers = [];
            if(rowsPerPage>0){
                var rpp='&rpp='+rowsPerPage;
            }
            else{
                var rpp = '';
            }
            return axios.get(this.listRoute+'&page='+page+rpp+'&search='+search).then((response)=>{
                if(response.status == 200){
                    data.items = response.data.items;
                    data.headers = response.data.headers;
                    data.total = response.data.total;
                    data.fields = response.data.fields;
                    data.myfiltered = response.data.filtered;
                    return data;
                }
                else{
                    console.log(response.status)
                }
            }).catch((error)=>{
                console.log(error);
            });
        },
        rowsPerPageUpdate(){
            this.pagination.page=1
        }
    },
}
</script>
