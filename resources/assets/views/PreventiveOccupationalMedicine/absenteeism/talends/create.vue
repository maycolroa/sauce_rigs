<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="SUBIR TALEND"
      url="absenteeism-talends"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-talend
                url="/biologicalmonitoring/absenteeism/talendUpload"
                method="POST"
                :cancel-url="{ name: 'absenteeism-talends'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTalend from '@/components/PreventiveOccupationalMedicine/Absenteeism/Talends/FormTalendComponent.vue';

export default {
  name: 'absenteeism-talends-create',
  metaInfo: {
    title: 'Talends - Crear'
  },
  components:{
    FormTalend
  }
}
</script>
