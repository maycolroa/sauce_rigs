<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="VER INSPECCIÓN"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection
                :inspection="data"
                :view-only="true"
                :typesInspection="typesInspection"
                :cancel-url="{ name: 'dangerousconditions-inspections'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormInspection from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-inspections-view',
  metaInfo: {
    title: 'Inspecciones - Ver'
  },
  components:{
    FormInspection
  },
  data () {
    return {
      data: [],
      typesInspection: []
    }
  },
  created(){
    this.fetchSelect('typesInspection', '/selects/industrialSecurity/inspectionType')

    axios.get(`/industrialSecurity/dangerousConditions/inspection/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
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