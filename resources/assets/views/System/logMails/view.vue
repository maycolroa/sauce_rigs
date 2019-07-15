<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Supervisar Correos /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <div>
              <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                  <b-row>
                    <b-col>
                        <div><b>Módulo:</b> {{ data.module ? data.module.display_name : ''}}</div>
                        <div><b>Evento:</b> {{ data.event }}</div>
                    </b-col>
                    <b-col>                   
                        <div><b>Asunto:</b> {{ data.subject }}</div>
                        <div><b>Fecha:</b> {{ data.created_at }}</div>
                    </b-col>
                </b-row>
                <b-row>
                  <b-col>
                    <div><b>Destinatarios:</b><p> {{ data.recipients }} </p></div>
                  </b-col>
                </b-row>
              </b-card>
            </div>
            
            <div>
              <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                <div v-html="data.message"> </div>
              </b-card>
            </div>
            <div class="row float-right pt-10 pr-15">
              <template>
                <b-btn variant="default" :to="{name: 'system-logmails'}">Atras</b-btn>
              </template>
          </div>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'system-logMails-view',
  metaInfo: {
    title: 'Supervisar Correos - Ver'
  },
  components:{
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/system/logMail/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.ready = true
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>