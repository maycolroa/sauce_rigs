<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Audiometrias /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <biological-monitoring-audiometry-form
                url="/biologicalmonitoring/audiometry"
                method="POST"
                employees-data-url="/selects/employees"
                :cancel-url="{ name: 'audiometry'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import BiologicalMonitoringAudiometryForm from '@/components/BiologicalMonitoring/Audiometry/FormComponent.vue';

export default {
  name: 'audiometry-create',
  metaInfo: {
    title: 'Audiometria - Crear'
  },
  components:{
    BiologicalMonitoringAudiometryForm
  }
}
</script>
