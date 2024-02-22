<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="VER DOCUMENTOS"
      url="industrialsecure-roadsafety-documents"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <document-form
                :document="data"
                positions-data-url="/selects/positions"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-roadsafety-documents'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import DocumentForm from '@/components/IndustrialSecure/RoadSafety/Documents/FormDocumentComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-roadsafety-documents-view',
  metaInfo: {
    title: 'Documentos - Ver'
  },
  components:{
    DocumentForm
  },
  data () {
    return {
      data: [],
			typeDocument: []
    }
  },
  created(){
    axios.get(`/industrialSecurity/roadsafety/documents/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
    this.fetchSelect('typeDocument', '/selects/typesDocument')
  },
  methods: {
		fetchSelect(key, url)
		{
			GlobalMethods.getDataMultiselect(url)
			.then(response => {
				this[key] = response;
			})
			.catch(error => {
				Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				this.$router.go(-1);
			});
		},
	}
}
</script>