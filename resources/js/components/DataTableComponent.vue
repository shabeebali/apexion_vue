<template>
    <div>
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
                    <div class="v-datatable__actions__search ml-4 mb-2">
                        <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                        ></v-text-field>
                    </div>
                    <div class="v-datatable__actions__select">
                        Rows Per Page:
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
            :headers="tableHeaders"
            :items="tableItems"
            item-key="name"
            select-all
            :total-items="totalItems"
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
                        <td v-if="it.actions" class="justify-center layout px-0">
                            <v-icon v-if="it.edit"
                            small
                            class="mr-2"
                            @click="editItem(props.item)"
                            >
                            edit
                            </v-icon>
                            <v-icon v-if="it.delete"
                                small
                                @click="deleteItem(props.item)"
                                >
                                delete
                            </v-icon>
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
                        <v-btn color="blue darken-1" flat @click="$emit('close-list-settings-model')">Close</v-btn>
                        <v-btn color="blue darken-1" flat @click="saveListSettings">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-card>
    </div>
</template>
<style scoped>
.v-datatable__actions__search{
    display: flex;
    flex:1 1 0;
}
.v-datatable.v-table thead{
    background-color: #f5f5f5 !important;
    border-color: #f5f5f5 !important;
}
.v-datatable.v-table th:first-child{
    width:80px;
}
</style>
<script>
export default{
    data(){
        return{
            selected:[],
            search:'',
            pagination:{},
            loading:false,
            tableItems:[],
            tableHeaders:[],
            totalItems:0,
            listFields:[],
            myfiltered:[],
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
    props:['route','listSettingsModel','refreshList'],
    watch: {
        pagination: {
            handler () {
                this.getDataFromApi(this.search)
                .then(data => {
                    this.tableItems = data.items
                    this.totalItems = data.total
                    this.tableHeaders = data.headers
                    this.listFields = data.fields
                    this.myfiltered = data.myfiltered
                })
            },
            deep: true
        },
        route:{
            handler(){
                this.getDataFromApi(this.search)
                .then(data => {
                    this.tableItems = data.items
                    this.totalItems = data.total
                    this.tableHeaders = data.headers
                    this.listFields = data.fields
                    this.myfiltered = data.myfiltered
                })
            }
        },
        refreshList:{
            handler(){
                if(this.refreshList == true){
                    this.getDataFromApi(this.search)
                    .then(data => {
                        this.tableItems = data.items
                        this.totalItems = data.total
                        this.tableHeaders = data.headers
                        this.listFields = data.fields
                        this.myfiltered = data.myfiltered
                    })
                }
            }
        },
        search: {
            handler () {
                this.deboucedSearch();

            },
            deep: true
        }
    },
    mounted(){
        this.deboucedSearch = _.debounce(()=>{
            this.getDataFromApi(this.search)
            .then(data => {
                this.tableItems = data.items
                this.totalItems = data.total
                this.tableHeaders = data.headers
                this.listFields = data.fields
                this.myfiltered = data.myfiltered
            })
        },1000);
        this.getDataFromApi(this.search)
        .then(data => {
            this.tableItems = data.items
            this.totalItems = data.total
            this.tableHeaders = data.headers
            this.listFields = data.fields
            this.myfiltered = data.myfiltered
        })
    },
    methods:{
        resetItem(key){
            this.$emit('reset-item',key);
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
            axios.post('/products/list_settings',fD).then((response)=>{
                if (response.status == 200) {
                    if(response.data.message == "success"){
                        this.$emit('close-list-settings-model');
                        this.getDataFromApi(this.search)
                        .then(data => {
                            this.tableItems = data.items
                            this.totalItems = data.total
                            this.tableHeaders = data.headers
                            this.listFields = data.fields
                            this.myfiltered = data.myfiltered
                        })
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
            console.log(item.id);
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
                }
                let pageStart=(page-1)*rowsPerPage+1;
                let pageStop =  Math.min(page*rowsPerPage,total);
                this.pageControlText = pageStart.toString()+' - '+pageStop.toString()+' of '+total.toString();
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
            return axios.get(this.route+'&page='+page+rpp+'&search='+search).then((response)=>{
                if(response.status == 200){
                    console.log(response.data)
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
