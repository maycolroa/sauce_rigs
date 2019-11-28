<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="CREAR ETIQUETA"
      url="administrative-customlabels"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-label
                url="/administration/label"
                labels-url-data="/selects/system/labels"
                method="POST"
                :cancel-url="{ name: 'administrative-customlabels'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLabel from '@/components/Administrative/Labels/FormLabelComponent.vue';

export default {
  name: 'administrative-customlabels-create',
  metaInfo: {
    title: 'Configurar Etiqueta - Crear'
  },
  components:{
    FormLabel
  }
}
</script>
