<template>
    <div>
        <v-content>
            <v-container fluid>
                <list-component
                listHeadline="Users"
                listClass="users-list"
                :baseRoute="baseRoute"
                :filterables="filterables"
                :listSettingsRoute="listSettingsRoute"
                :triggerUpdate="triggerUpdate"
                v-on:triggered-update="triggerUpdate=false"
                v-on:open-add-dialog="redirectAdd"
                v-on:open-edit-dialog="editItem"
                :addFlag="addFlag"
                exportable
                importable></list-component>
            </v-container>
        </v-content>
    </div>
</template>
<script>
import ListComponent from '../../../js/components/ListComponent.vue'
export default{
    
    components:{
        'list-component':ListComponent,
    },
    data(){
        return{
            baseRoute:'/settings/users?',
            listSettingsRoute:'',
            filterables:[],
            addDialog:false,
            triggerUpdate:false,
            addFlag:true,
            editDialog:false,
            editId:0,
        }
    },
    mounted(){
        axios.get('/settings/users/filterables').then((response)=>{
            if(response.status == 200)
            {
                this.filterables = response.data.filterables
            }
            else{
                alert('Something went wrong!!!')
                console.log(response)
            }
        })
    },
    methods:{
        editItem($event){
            this.$router.push('edit/'+$event)
        },
        redirectAdd(){
            this.$router.push('add')
        }
    }   
}
</script>
