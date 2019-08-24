<template>
    <div>
        <v-navigation-drawer v-if="Object.keys(filterables).length > 0" v-model="listFilterModel" app fixed right width=320 temporary clipped>
            <v-row class="mx-0" justify="space-between">
                <v-col cols=6 class="pa-0">
                    <v-subheader>Filter By </v-subheader>
                </v-col>
                <v-col cols=2 class="pa-0">
                    <v-btn icon text @click.stop="listFilterModel = false"><v-icon>close</v-icon></v-btn>
                </v-col>
            </v-row>
            <v-row justify="space-between">
                <v-col cols=4 class="py-0">
                    <v-btn tile text color="primary" v-on:click="applyFilter">Apply</v-btn>
                </v-col>
                <v-col cols=4 class="py-0">
                    <v-btn tile text color="dark" v-on:click="resetFilter">Reset</v-btn>
                </v-col>
            </v-row>
            <v-divider class="mt-3"></v-divider>
            <v-row wrap>
                <template v-for="(filterable,index) in filterables">
                    <v-col cols=12 class="px-4" v-if="filterable.type == 'select'">
                        <v-select :label="filterable.name" :items="filterable.options" v-model="filterables[index].value"></v-select>
                    </v-col>
                    <v-col cols=12 v-if="filterable.type == 'slider'" class="px-4">
                        <v-subheader class="pl-0 mb-2" style="justify-content:center">{{filterable.name}}</v-subheader>
                        <v-row>
                            <v-col class="px-4">
                              <v-range-slider v-model="filterables[index].range" :max="filterable.max_value" :min="filterable.min_value" hide-details class="align-center">
                                <template v-slot:prepend>
                                  <v-text-field   v-model="filterables[index].range[0]" class="mt-0 pt-0"  hide-details single-line type="number" style="width: 60px"></v-text-field>
                                </template>
                                <template v-slot:append>
                                  <v-text-field v-model="filterables[index].range[1]" class="mt-0 pt-0" hide-details single-line type="number" style="width: 60px"></v-text-field>
                                </template>
                              </v-range-slider>
                            </v-col>
                          </v-row>
                    </v-col>
                    <v-col cols=12 class="px-4" v-if="filterable.type == 'multiselect'">
                        <v-autocomplete v-model="filterables[index].value" :items="filterable.options" :label="filterable.name "  multiple>
                            <template v-slot:selection="data">
                                <v-chip :selected="data.selected" close class="chip--select-multi" @input="remove(data.item)" >
                                    {{ data.item.text }}
                                </v-chip>
                            </template>
                            <template v-slot:item="data">
                                <template v-if="data.item.group == ''">
                                    <v-list-item-content v-text="data.item.text"></v-list-item-content>
                                </template>
                                <template v-else>
                                    <v-list-item-content>
                                        <v-list-item-title v-html="data.item.text"></v-list-item-title>
                                        <v-list-item-subtitle v-html="data.item.group"></v-list-item-subtitle>
                                    </v-list-item-content>
                                </template>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </template>>
            </v-row>
        </v-navigation-drawer>
        
        <v-row class="mb-2 mx-0">
            <v-subheader class="headline">{{listHeadline}}</v-subheader>
            <v-spacer></v-spacer>

            <v-tooltip bottom v-if="exportable && exportFlag">
                <template v-slot:activator="{ on }">
                    <v-btn text icon v-on="on" @click.stop="exportFn">
                        <v-icon>publish</v-icon>
                    </v-btn>
                </template>
                <span>Export</span>
            </v-tooltip>
            <v-tooltip bottom v-if="importable && importFlag">
                <template v-slot:activator="{ on }">
                    <v-btn text icon v-on="on" :to="{path:$router.currentRoute.path+'/import'}">
                        <v-icon>get_app</v-icon>
                    </v-btn>
                </template>
                <span>Import</span>
            </v-tooltip>
            <v-btn text icon v-if="Object.keys(filterables).length > 0" @click.stop="listFilterModel=!listFilterModel"><v-icon>filter_list</v-icon></v-btn>
            <v-btn v-if="Object.keys(listFields).length > 0" class="" text icon @click.stop="listSettingsModel=true"><v-icon>settings</v-icon></v-btn>
            <v-btn tile text @click.stop="updateList"><v-icon>mdi-refresh</v-icon></v-btn>
            <v-btn text tile v-if="addFlag" class="primary" @click="$emit('open-add-dialog')">Add</v-btn>
        </v-row>
        <v-card>
            <template v-if="myfiltered">
                <v-row row wrap>
                    <template v-for="ff in myfiltered">
                        <v-col lg2 md3 sm4 xs6 v-if="ff.type =='select'">
                            <v-chip v-on:input="resetItem(ff.key)" close>{{ff.name}}: {{ff.value}}</v-chip>
                        </v-col>
                        <template v-if="ff.type =='multiselect'">
                            <v-col lg2 md3 sm4 xs6 v-for="(t,index) in ff.terms" :key="index">
                                <v-chip v-on:input="resetItem(ff.key,t.id)" close>{{t.name}}</v-chip>
                            </v-col>
                        </template>
                        <v-col lg2 md3 sm4 xs6 v-if="ff.type == 'slider'">
                            <v-chip v-on:input="resetItem(ff.key)" close>{{ff.range[0]}} < {{ff.name}} > {{ff.range[1]}}</v-chip>
                        </v-col>
                    </template>
                </v-row>
            </template>
            <div class="text-xs-center pt-2">
                
            </div>
            <v-card-title>
                <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details clearable></v-text-field>
                <v-spacer></v-spacer>
            </v-card-title>
            <v-row>
                <v-col xs6 class="v-data-footer d-col justify-start ml-6">
                    <div class="v-data-footer__select">
                        Rows per page:
                        <v-select :items="itemsPerPageSelectItems" hide-details v-on:change="itemsPerPageUpdate" v-model="pagination.itemsPerPage">
                        </v-select>
                    </div>
                    <div class="v-data-footer__pagination">
                            {{pageControlText}}
                    </div>
                    <!--
                    <div class="v-data-footer__icons-before">
                        <v-btn text icon :disabled="pagination.page ==1" v-on:click="pagination.page -= 1">
                            <v-icon light>chevron_left</v-icon>
                        </v-btn>
                    </div>
                    <div class="v-data-footer__icons-after">
                        <v-btn text icon :disabled="pagination.page == Math.ceil(totalItems/pagination.itemsPerPage)" v-on:click="pagination.page += 1">
                            <v-icon light>chevron_right</v-icon>
                        </v-btn>
                    </div>
                    -->
                </v-col>
                <v-col xs6>
                    <v-pagination v-model="pagination.page" :length="pages" :total-visible="7" ></v-pagination>
                </v-col>
            </v-row>
            <v-data-table fixed-header
            class="apex-list"
            :headers="tableHeaders"
            :items="tableItems"
            item-key="id"
            show-select
            hide-default-footer
            v-model="selected"
            :loading="loading"
            :options.sync="pagination"
            :server-items-length="totalItems"
            :footer-props="footerProps"
            :items-per-page="itemsPerPage"
            >
                <template v-slot:item.name="{ item }">
                    <v-row row wrap align-content-space-between>
                        <v-col xs10 align-self-center>
                            <router-link :to="{path:$router.currentRoute.path+'/view/'+item.id}">{{item.name}}</router-link>
                        </v-col>
                        <v-col xs2 align-self-center class="text-xs-right">
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on }">
                                    <v-btn icon v-on="on" @click="textCopy(item.name)">
                                        <v-icon  light>file_copy</v-icon>
                                    </v-btn>
                                </template>
                                <span>Copy</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
                </template>
                <template v-slot:item.status="{ item }">
                    <div v-if="item.status == 'processing'" class="red--text">{{item.status}}</div>
                    <div v-if="item.status == 'draft'" class="cyan--text">{{item.status}}</div>
                    <div v-if="item.status == 'completed'" class="green--text">{{item.status}}</div>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-tooltip bottom v-if="item.actions.edit">
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on" @click="editItem(item.actions)">
                                <v-icon  small>edit</v-icon>
                            </v-btn>
                        </template>
                        <span>Edit</span>
                    </v-tooltip>
                    <v-btn text @click="viewItem(item.actions)" v-if="item.actions.view">
                        <v-icon  small>mdi-eye</v-icon> View
                    </v-btn>
                    <v-tooltip bottom v-if="item.actions.delete">
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on" @click="deleteItem(item)">
                                <v-icon  small>delete</v-icon>
                            </v-btn>
                        </template>
                        <span>Delete</span>
                    </v-tooltip>
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
                    <v-row class="justify-start mx-0">
                        <v-col cols=12 v-if="deleteManyFlag">
                            <v-btn @click="deleteMany" color="error" :disabled="selected.length == 0">Delete</v-btn>
                        </v-col>
                        <v-col cols=12>
                            <v-btn v-if="extraButton" @click="extraBtnMethod" color="success" :disabled="selected.length == 0">{{extraButtonLabel}}</v-btn>
                        </v-col>
                    </v-row>
                </template>
            </v-data-table>
            <v-row>
                <v-col xs6 class="v-data-footer d-col justify-start ml-6">
                    <div class="v-data-footer__select">
                        Rows per page:
                        <v-select :items="itemsPerPageSelectItems" hide-details v-on:change="itemsPerPageUpdate" v-model="pagination.itemsPerPage">
                        </v-select>
                    </div>
                    <div class="v-data-footer__pagination">
                            {{pageControlText}}
                    </div>
                </v-col>
                <v-col xs6>
                    <v-pagination v-model="pagination.page" :length="pages" :total-visible="7" ></v-pagination>
                </v-col>
            </v-row>
            <v-dialog v-model="listSettingsModel" persistent max-width="600px" v-if="listFields">
                <v-card>
                    <v-card-title>
                        <span class="headline">Fields to include in table</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container grid-list-md>
                            <v-row wrap >
                                <template v-for="(ff,key) in listFields" v-if="key != 'id'">
                                    <v-col cols=12>
                                        <v-switch v-model="ff.selected" :label="ff.text"></v-switch>
                                    </v-col>
                                </template>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="listSettingsModel = false">Close</v-btn>
                        <v-btn color="blue darken-1" text @click="saveListSettings">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="loadingDialog" persistent  width="300">
              <v-card color="teal" dark class="pa-3">
                <v-card-text>
                    Please stand by
                    <v-progress-linear indeterminate color="white" class="mt-3"></v-progress-linear>
                </v-card-text>
              </v-card>
            </v-dialog>
        </v-card>
    </div>
</template>
<style>
.apex-list .v-datatable__actions__search{
    display: col;
    col:1 1 0;
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
            addFlag:false,
            listRoute:'',
            listFilterModel:false,
            listSettingsModel:false,
            selected:[],
            search:'',
            pagination:{
            },
            page:15,
            loading:false,
            tableItems:[],
            tableHeaders:[],
            totalItems:0,
            listFields:[],
            myfiltered:[],
            deleteIds:[],
            deleteManyFlag:false,
            loadingDialog:false,
            exportFlag:false,
            importFlag:false,
            footerProps:{
                'items-per-page-options':[15,25,50,100],
            },
            itemsPerPage:15,
            itemsPerPageItems:[15,25,50,100],
            itemsPerPageSelectItems:[
                {
                    'text':'15',
                    'value':15
                },
                {
                    'text':'25',
                    'value':25
                },
                {
                    'text':'50',
                    'value':50
                },
                {
                    'text':'100',
                    'value':100
                }
            ],
            pageControlText:'',
        }
    },
    props:{
        'baseRoute':{
            type: String
        },
        'listSettingsRoute':{
            type: String
        },
        'filterables': '',
        'triggerUpdate':{
            type: Boolean
        },
        'listHeadline':{
            type: String
        },
        'listClass':{
            type: String
        },
        'exportable':{
            type:Boolean,
            default:false
        },
        'importable':{
            type:Boolean,
            default:false
        },
        'extraButton':{
            type:Boolean,
            default:false,
        },
        'extraButtonLabel':{
            type:String,
            default:''
        },
        'extraButtonRoute':{
            type:String,
            default:''
        }
    },
    computed:{
        exportRoute: function(){
            return window.axios.defaults.baseURL+this.baseRoute.substr(0,this.baseRoute.indexOf('?'))+'/export'
        },
        pages () {
            if (this.pagination.itemsPerPage == null || this.totalItems == null){
               return 0 
            }
            return Math.ceil(this.totalItems / this.pagination.itemsPerPage)
        }
    },
    watch: {
        pagination: {
            handler () {
                const { sortBy, sortDesc, page, itemsPerPage } = this.pagination
                let pageStart=(page-1)*itemsPerPage+1;
                let pageStop =  Math.min(page*itemsPerPage,this.totalItems);
                if(this.totalItems > 0){
                    this.pageControlText = pageStart.toString()+'-'+pageStop.toString()+' of '+this.totalItems.toString();
                }
                else{
                    this.pageControlText = '-'
                }
                this.updateList()
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
        exportFn(){
            this.loadingDialog = true
            axios({
                url: this.exportRoute,
                method: 'GET',
                responseType: 'blob', // important
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', this.listHeadline+'.xlsx'); //or any other extension
                document.body.appendChild(link);
                this.loadingDialog = false

               link.click();
            });
        },
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
                if(this.filterables[key].type == 'multiselect'){
                    if(this.filterables[key].value !== undefined || this.filterables[key].value != ''){
                        str = str+"&"+key+"="+this.filterables[key].value.toString()
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
                this.addFlag = data.addFlag
                this.exportFlag = data.exportFlag
                this.importFlag = data.importFlag
                this.deleteManyFlag = data.items[0].actions.delete
            })
        },
        resetItem($event, id = null){
            Object.keys(this.filterables).forEach((key)=>{
                if(key == $event){
                    if(this.filterables[key].type == 'select'){
                        this.filterables[key].value = '-1'
                    }
                    if(this.filterables[key].type == 'slider'){
                        this.filterables[key].range = this.filterables[key].default
                    }
                    if(this.filterables[key].type == 'multiselect'){
                        var list = this.filterables[key].value
                        list.splice(list.indexOf(id), 1)
                        this.filterables[key].value = list
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
        viewItem(item){
            console.log(item)
            this.$emit('open-view-dialog',item.id)
        },
        deleteItem(item){
            this.confirmDialog('Do you really want to delete this item?').then(res => {
                if(res == true){
                    this.loadingDialog=true
                    var fD = new FormData()
                    fD.append('delete_ids',item.id)
                    axios.post(this.baseRoute.substr(0,this.baseRoute.indexOf('?'))+'/delete',fD).then((response)=>{
                        if(response.status == 200 && response.data.message == 'success'){
                            this.loadingDialog = false
                            this.updateList()
                            this.deleteId = new Array()
                        }
                        else{
                            alert('Something went wrong!')
                        }
                    })
                }
                else{
                    
                }
            })
        },
        deleteMany(){
            this.confirmDialog('Do you really want to delete these items?').then(res => {
                if(res == true){
                    this.loadingDialog=true
                    this.deleteIds = new Array()
                    this.selected.forEach((item)=>{
                        this.deleteIds.push(item.id)
                    })
                    var fD = new FormData()
                    fD.append('delete_ids',this.deleteIds)
                    axios.post(this.baseRoute.substr(0,this.baseRoute.indexOf('?'))+'/delete',fD).then((response)=>{
                        if(response.status == 200 && response.data.message == 'success'){
                            this.loadingDialog = false
                            this.updateList()
                            this.deleteId = new Array()
                            this.selected = []
                        }
                        else{
                            alert('Something went wrong!')
                        }
                    })
                }
            })
        },
        getDataFromApi(search){
            this.loading = true;
            const { sortBy, sortDesc, page, itemsPerPage } = this.pagination
            return this.getItems(page,itemsPerPage,search,sortBy,sortDesc).then((data)=>{
                let total = data.total;
                let items = data.items;
                let headers = data.headers;
                let fields = data.fields;
                let myfiltered = data.myfiltered;
                let addFlag = data.addFlag;
                let exportFlag = data.exportFlag
                let importFlag = data.importFlag
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
                let pageStart=(page-1)*itemsPerPage+1;
                let pageStop =  Math.min(page*itemsPerPage,total);
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
                    'myfiltered':myfiltered,
                    'addFlag':addFlag,
                    'exportFlag':exportFlag,
                    'importFlag':importFlag,
                }
                this.loading=false
                return data2
            })

        },
        getItems(page,itemsPerPage,search,sortBy,sortDesc){
            var data={
                'items':[],
                'headers':[],
            };
            var items = [];
            var headers = [];
            var rpp = ''
            if(itemsPerPage>0){
                rpp='&rpp='+itemsPerPage;
            }
            var sort = ''
            if(sortBy.length > 0){
                sort = '&sortby='+sortBy
            }
            var desc = ''
            if(sortDesc[0] == true){
                desc = '&descending=1'
            }
            if(search == null){
                search = ''
            }
            return axios.get(this.listRoute+'&page='+page+rpp+'&search='+search+sort+desc).then((response)=>{
                if(response.status == 200){
                    data.items = response.data.items;
                    data.headers = response.data.headers;
                    data.total = response.data.total;
                    data.fields = response.data.fields;
                    data.myfiltered = response.data.filtered;
                    data.addFlag = response.data.addflag;
                    data.exportFlag = response.data.exportflag;
                    data.importFlag = response.data.importflag;
                    return data;
                }
                else{
                    console.log(response.status)
                }
            })
        },
        itemsPerPageUpdate(){
            this.pagination.page=1
        },
        textCopy(text){
            const el = document.createElement('textarea');
            el.value = text;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        },
        extraBtnMethod(){
            this.loadingDialog=true
            var Ids = new Array()
            this.selected.forEach((item)=>{
                Ids.push(item.id)
            })
            var fD = new FormData()
            fD.append('ids',Ids)
            axios.post(this.baseRoute.substr(0,this.baseRoute.indexOf('?'))+'/'+this.extraButtonRoute,fD).then((response)=>{
                if(response.status == 200 && response.data.message == 'success'){
                    this.loadingDialog = false
                    this.updateList()
                    this.deleteId = new Array()
                }
                else{
                    alert('Something went wrong!')
                }
            })
        }
    },
}
</script>
