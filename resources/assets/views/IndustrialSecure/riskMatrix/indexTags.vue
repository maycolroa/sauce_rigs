<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      :subtitle="`TAGS / ${title}`"
      url="industrialsecure-dangermatrix-tags"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!title"/>
            <div v-if="title">
              <template>
                  <vue-table
                    :configName="configTable"
                    ></vue-table>
              </template>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
<script>
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'dangermatrix',
  metaInfo: {
    title: 'Tags'
  },
  components:{
    Loading
  },
  data(){
    return {
      title: '',
      configTable: ''
    }
  },
  computed:
  {
    currentUrl()
    {
      return this.$route.name
    }
  },
  created()
  {
    this.getConfig()
  },
  methods:
  {
    getConfig()
    {
      if (this.currentUrl == 'industrialsecure-dangermatrix-tags-administrative-controls')
      {
        this.title = "Controles administativos"
        this.configTable = "industrialsecure-dangermatrix-tags-administrative-controls"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-engineering-controls')
      {
        this.title = "Controles de ingenieria"
        this.configTable = "industrialsecure-dangermatrix-tags-engineering-controls"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-possible-consequences-danger')
      {
        this.title = "Posibles consecuencias del peligro"
        this.configTable = "industrialsecure-dangermatrix-tags-possible-consequences-danger"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-substitutions')
      {
        this.title = "Sustituciones"
        this.configTable = "industrialsecure-dangermatrix-tags-substitutions"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-warning-signage')
      {
        this.title = "Señalizaciones, Advertencias"
        this.configTable = "industrialsecure-dangermatrix-tags-warning-signage"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-epp')
      {
        this.title = "EPP"
        this.configTable = "industrialsecure-dangermatrix-tags-epp"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-participants')
      {
        this.title = "Participantes"
        this.configTable = "industrialsecure-dangermatrix-tags-participants"
      }
      else if (this.currentUrl == 'industrialsecure-dangermatrix-tags-danger-description')
      {
        this.title = "Descripción de peligros"
        this.configTable = "industrialsecure-dangermatrix-tags-danger-description"
      }
    }
  },
}
</script>
