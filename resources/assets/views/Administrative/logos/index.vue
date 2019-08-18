<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Configurar Logo</span>
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-logo
                :url="`/administration/logo`"
                method="POST"
                :logo="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLogo from '@/components/Administrative/Logos/FormLogoComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-logos',
  metaInfo: {
    title: 'Configurar Logo'
  },
  components:{
    FormLogo
  },
  data () {
    return {
      data: []
    }
  },
  created(){

    axios.get(`/administration/logo/view`)
    .then(response => {
        this.data = response.data.data;
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