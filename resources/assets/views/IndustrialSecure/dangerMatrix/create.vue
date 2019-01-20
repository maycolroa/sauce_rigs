<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Matriz de Peligros /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-danger-matrix-form
                url="/industrialSecurity/dangersMatrix"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix'}"
                :type-activities="typeActivities"
                :danger-generated="dangerGenerated"
                :si-no="siNo"
                :qualifications="qualifications"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureDangerMatrixForm from '@/components/IndustrialSecure/DangerMatrix/FormDangerMatrixComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-dangermatrix-create',
  metaInfo: {
    title: 'Matriz de Peligros - Crear'
  },
  components:{
    IndustrialSecureDangerMatrixForm
  },
  data(){
    return {
      typeActivities: [],
      dangerGenerated: [],
      siNo: [],
      qualifications: []
    }
  },
  created(){
    this.fetchSelect('typeActivities', '/radios/dmTypeActivities')
    this.fetchSelect('dangerGenerated', '/selects/dmGeneratedDangers')
    this.fetchSelect('siNo', '/radios/siNo')
    this.fetchSelect('qualifications', '/administration/configurations/industrialSecurity/dangersMatrix/getQualificationsComponent')
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
