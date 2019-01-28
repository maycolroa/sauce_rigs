<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Nivel localización en formulario /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-location-level-form-componet
                url="/administration/configurations/locationLevelForms"
                method="POST"
                :modules="modules"
                :locationLevels="locationLevels"
                :cancel-url="{ name: 'configurations-locationlevelform'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLocationLevelFormComponet from '@/components/Administrative/Configurations/LocationLevelForm/FormLocationLevelFormComponet.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'configurations-locationlevelform-create',
  metaInfo: {
    title: 'Nivel localización en formulario - Crear'
  },
  components:{
    FormLocationLevelFormComponet
  },
  data(){
    return {
      modules: [],
      locationLevels: []

    }
  },
  created(){
    this.fetchSelect('modules', '/selects/conf/locationLevelFormModules')
    this.fetchSelect('locationLevels', '/radios/conf/locationLevelForm')
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
