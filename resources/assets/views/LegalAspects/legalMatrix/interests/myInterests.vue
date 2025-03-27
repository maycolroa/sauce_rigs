<template>
  	<div>
		<header-module
			title="MATRIZ LEGAL"
			subtitle="ESTABLECER MIS INTERESES"
			url="legalaspects-legalmatrix"
		/>

		<div class="col-md">
			<b-card no-body>
				<b-card-body>
					<loading :display="!ready"/>
                    <form-my-interests
						v-if="ready"
						url="/legalAspects/legalMatrix/interest/saveInterests"
                		method="POST"
                		:cancel-url="{ name: 'legalaspects-legalmatrix'}"
						:interest="data"
						:options="options"
						:descriptions="descriptions"
						urlDataInterests="/selects/legalMatrix/interestsSystem"/>
				</b-card-body>
			</b-card>
		</div>
  	</div>
</template>

<script>
import FormMyInterests from '@/components/LegalAspects/LegalMatrix/Interest/FormMyInterestsComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
	name: 'legalaspects-lm-myinterests',
	metaInfo: {
		title: 'Matriz Legal - Establecer mis intereses'
	},
	components:{
		FormMyInterests,
		Loading
	},
	data () {
		return {
			data: [],
			options: [],
			descriptions: [],
			ready: false,
		}
	},
	created(){

		GlobalMethods.getDataMultiselect(`/radios/legalMatrix/interestsSystem`)
        .then(response => {
			this.options = response;
			setTimeout(() => {
				this.ready = true
			}, 3000)
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });

		GlobalMethods.getDataMultiselect(`/radios/legalMatrix/interestsSystemDescription`)
        .then(response => {
			this.descriptions = response;
			setTimeout(() => {
				this.ready = true
			}, 3000)
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });


		axios.get(`/legalAspects/legalMatrix/interest/myInterests`)
		.then(response => {
			this.data = response.data.data;
			setTimeout(() => {
				this.ready = true
			}, 3000)
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
	},
	methods: {

	}
}
</script>