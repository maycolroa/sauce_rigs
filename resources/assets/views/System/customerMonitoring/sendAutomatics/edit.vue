<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="MONITOREO DE CLIENTES - EDITAR ENVIO AUTOMÁTICO"
      url="system-customermonitoring-automaticsSend"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-send-automatic
                :url="`/system/notification/${this.$route.params.id}`"
                method="PUT"
                :send="data"
                :is-edit="true"
                :usersOptions="usersOptions"
                :daysOptions="daysOptions"
                :cancel-url="{ name: 'system-customermonitoring-automaticsSend'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormSendAutomatic from '@/components/System/CustomerMonitoring/FormSendAutomatic.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-customermonitoring-automatics-send-edit',
  metaInfo: {
    title: 'Monitoreo de clientes - Editar Envio Automático'
  },
  components:{
    FormSendAutomatic
  },
  data () {
    return {
      data: [],
      daysOptions: [],
      usersOptions: []
    }
  },
  created(){
    axios.get(`/system/notification/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.fetchSelect('usersOptions', '/selects/usersAutomaticSend');
        this.fetchSelect('daysOptions', '/selects/days');
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
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
