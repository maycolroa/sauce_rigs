<template>
  <div>
    <header-module
      title="FIRMA"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-firm
                :url="`/administration/users/firm`"
                method="POST"
                :firm="data"
                :is-edit="true"
                :cancel-url="{ path: '/'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormFirm from '@/components/Administrative/Users/FormFirmComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'firm',
  metaInfo: {
    title: 'Configurar Firma'
  },
  components:{
    FormFirm  
  },
  data () {
    return {
      data: {firm_image: '', type: ''}
    }
  },
  created(){

    axios.get(`/administration/users/firm/view`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
    
  },
  methods: {
  }
}
</script>
