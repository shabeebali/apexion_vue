<template>
    <div>
        <v-content class="settings">
            <div>
                <v-breadcrumbs :items="breadcrumbs" divider=">"></v-breadcrumbs>
            </div>
            <v-container fluid class="pt-0">
                <v-expansion-panel class="mb-2">
                    <v-expansion-panel-content>
                        <template v-slot:header>
                            <div>General</div>
                        </template>
                        <v-card class="pa-2">
                        	<v-layout row wrap>
                        		<v-flex xs12 md6 px-2>
                        			<v-combobox :items="data.zones" label="Timezone" v-model="data.zone"></v-combobox>
                        		</v-flex>
                        	</v-layout>
                        	<v-card-actions>
                        		<v-btn @click.stop="save" color="primary">Save</v-btn>
                        	</v-card-actions>
                        </v-card>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-container>
        </v-content>
    </div>
</template>
<style>
    .settings .v-expansion-panel__header{
        background-color: #ddd;
    }
</style>
<script>
export default{
	data(){
		return{
			data:{
				zone:'',
				zones:[],
			},
			breadcrumbs:[
                {
                    text:'Home',
                    disabled:false,
                    to:'/'
                },
                {
                    text:'Settings',
                    disabled:true,
                    to:'/settings/general'
                },  
            ]
		}
	},
	mounted(){
		axios.get('settings/zones').then((response)=>{
			this.data.zones = response.data.data
		})
	},
	methods:{
		save(){
			var fD = new FormData()
			fD.append('zone',this.data.zone)
			axios.post('settings/save',fD).then((response)=>{
				alert('Settings Saved');
			})
		}
	}
}
</script>
