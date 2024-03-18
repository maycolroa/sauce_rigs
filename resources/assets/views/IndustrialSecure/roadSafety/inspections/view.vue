<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="VER INSPECCIÓN PLANEADA"
      url="roadSafety-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection-personalized
                v-if="type == '3'"
                :inspection="data"
                :view-only="true"
                :typesInspection="typesInspection"
                :typesItems="typesItems"
                :cancel-url="{ name: 'roadSafety-inspections'}"/> 
            <form-inspection
                v-else
                :inspection="data"
                :view-only="true"
                :typesInspection="typesInspection"
                :cancel-url="{ name: 'roadSafety-inspections'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormInspection from '@/components/IndustrialSecure/RoadSafety/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import FormInspectionPersonalized from '@/components/IndustrialSecure/RoadSafety/Inspections/FormInspectionPersonalizadasComponent.vue';

export default {
  name: 'roadSafety-inspections-view',
  metaInfo: {
    title: 'Inspecciones Planeadas - Ver'
  },
  components:{
    FormInspection,
    FormInspectionPersonalized
  },
  data () {
    return {
      data: [],
      typesInspection: [],
      typesItems: [],
      type: ''
    }
  },
  created(){
    this.fetchSelect('typesInspection', '/selects/industrialSecurity/inspectionType')
    this.fetchSelect('typesItems', '/selects/industrialSecurity/inspectionTypeItems')

    axios.get(`/industrialSecurity/roadsafety/inspection/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.type = response.data.data.type_id
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
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