<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="TAG CONTROLES ADMINISTRATIVOS"
      url="industrialsecure-dangermatrix-tags"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'industrialsecure-tags-administrative-controls-create'}" variant="primary">Crear Tag</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
          <div>
            <template>
                <vue-table
                  configName="industrialsecure-dangermatrix-tags-administrative-controls"
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
  name: 'dangermatrix-tag-administrative-controls',
  metaInfo: {
    title: 'Tags Controles Administrativos'
  },
}
</script>
