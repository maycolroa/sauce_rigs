<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="CREAR DOCUMENTOS"
      url="industrialsecure-roadsafety-documents"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <document-form
                url="/industrialSecurity/roadsafety/documents"
                method="POST"
                positions-data-url="/selects/positions"
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
  name: 'industrialsecure-roadsafety-documents-create',
  metaInfo: {
    title: 'Documentos - Crear'
  },
  components:{
    DocumentForm
  },
  props: {
  },
  data(){
		return {
		}
	},
	created(){
	},
  methods: {
	}
}
</script>
