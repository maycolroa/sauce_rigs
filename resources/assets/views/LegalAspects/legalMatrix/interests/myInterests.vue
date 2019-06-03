<template>
  	<div>
		<h4 class="font-weight-bold mb-4">
			<span class="text-muted font-weight-light">Matriz Legal/</span> Establecer mis intereses
		</h4>

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
						urlDataInterests="/selects/legalMatrix/interests"/>
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
			ready: false,
		}
	},
	created(){

		GlobalMethods.getDataMultiselect(`/selects/legalMatrix/interests`)
        .then(response => {
			this.options = response;
			this.ready = true
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });

		axios.get(`/legalAspects/legalMatrix/interest/myInterests`)
		.then(response => {
			this.data = response.data.data;
			this.ready = true
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