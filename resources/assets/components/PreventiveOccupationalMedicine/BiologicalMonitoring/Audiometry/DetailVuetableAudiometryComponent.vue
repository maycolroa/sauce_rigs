<template>
      <div>
        <b-card bg-variant="transparent" border-variant="transparent" title="" class="px-0 box-shadow-none">
          <b-row>
            <b-col md="6">
              <div v-if="row.date"><b>Fecha:</b> {{row.date}}</div>
              <div v-if="row.employee_name"><b>Nombre Empleado:</b> {{row.employee_name}}</div>
              <div v-if="row.exposition_level"><b>Nivel exposicion(Disometría):</b> {{row.exposition_level}}</div>
              <div v-if="row.recommendations"><b>Recomendaciones Generales:</b> {{row.recommendations.length > 70 ? row.recommendations.substring(0, 70) + '...' : row.recommendations}}</div>
              <div v-if="row.updated_at"><b>Fecha Actualización:</b> {{row.updated_at}}</div>
            </b-col>
            <b-col md="6">
              <div v-if="row.employee_name"><b>Identificación Empleado:</b> {{row.employee_name}}</div>
              <div v-if="row.previews_events"><b>Eventos Previos:</b> {{row.previews_events}}</div>
              <div v-if="row.epp"><b>EPP:</b> {{row.epp}}</div>
              <div v-if="row.base_type"><b>Base:</b> {{ row.base_type == 'Base' ? 'Si' : 'No'}} <template  v-if="row.base"> - <router-link :to="{ path: `/preventiveoccupationalmedicine/biologicalmonitoring/audiometry/view/${row.base}` }">Ver Audiometria</router-link></template></div>
              <div v-if="row.base_state"><b>Tipo Base:</b> {{row.base_state}}</div>
              <div v-if="row.observation"><b>Observaciones:</b> {{row.observation.length > 70 ? row.observation.substring(0, 70) + '...' : row.observation}}</div>
              <div v-if="row.created_at"><b>Fecha Creación:</b> {{row.created_at}}</div>
            </b-col>
          </b-row>
          <hr class="my-2">
          <b-row>
            <b-col md="6">
              <div v-if="row.air_right_500"><b>Derecha Aereo 500hz:</b> {{row.air_right_500}} dB</div>
              <div v-if="row.air_right_1000"><b>Derecha Aereo 1000hz:</b> {{row.air_right_1000}} dB</div>
              <div v-if="row.air_right_2000"><b>Derecha Aereo 2000hz:</b> {{row.air_right_2000}} dB</div>
              <div v-if="row.air_right_3000"><b>Derecha Aereo 3000hz:</b> {{row.air_right_3000}} dB</div>
              <div v-if="row.air_right_4000"><b>Derecha Aereo 4000hz:</b> {{row.air_right_4000}} dB</div>
              <div v-if="row.air_right_6000"><b>Derecha Aereo 6000hz:</b> {{row.air_right_6000}} dB</div>
              <div v-if="row.air_right_8000"><b>Derecha Aereo 800hz:</b> {{row.air_right_8000}} dB</div>
              <div v-if="row.osseous_right_500"><b>Derecha Oseo 500hz:</b> {{row.osseous_right_500}} dB</div>
              <div v-if="row.osseous_right_1000"><b>Derecha Oseo 1000hz:</b> {{row.osseous_right_1000}} dB</div>
              <div v-if="row.osseous_right_2000"><b>Derecha Oseo 2000hz:</b> {{row.osseous_right_2000}} dB</div>
              <div v-if="row.osseous_right_3000"><b>Derecha Oseo 3000hz:</b> {{row.osseous_right_3000}} dB</div>
              <div v-if="row.osseous_right_4000"><b>Derecha Oseo 4000hz:</b> {{row.osseous_right_4000}} dB</div>
            </b-col>
            <b-col md="6">
              <div v-if="row.air_left_500"><b>Izquierda Aereo 500hz:</b> {{row.air_left_500}} dB</div>
              <div v-if="row.air_left_1000"><b>Izquierda Aereo 1000hz:</b> {{row.air_left_1000}} dB</div>
              <div v-if="row.air_left_2000"><b>Izquierda Aereo 2000hz:</b> {{row.air_left_2000}} dB</div>
              <div v-if="row.air_left_3000"><b>Izquierda Aereo 3000hz:</b> {{row.air_left_3000}} dB</div>
              <div v-if="row.air_left_4000"><b>Izquierda Aereo 4000hz:</b> {{row.air_left_4000}} dB</div>
              <div v-if="row.air_left_6000"><b>Izquierda Aereo 6000hz:</b> {{row.air_left_6000}} dB</div>
              <div v-if="row.air_left_8000"><b>Izquierda Aereo 800hz:</b> {{row.air_left_8000}} dB</div>
              <div v-if="row.osseous_left_500"><b>Izquierda Oseo 500hz:</b> {{row.osseous_left_500}} dB</div>
              <div v-if="row.osseous_left_1000"><b>Izquierda Oseo 1000hz:</b> {{row.osseous_left_1000}} dB</div>
              <div v-if="row.osseous_left_2000"><b>Izquierda Oseo 2000hz:</b> {{row.osseous_left_2000}} dB</div>
              <div v-if="row.osseous_left_3000"><b>Izquierda Oseo 3000hz:</b> {{row.osseous_left_3000}} dB</div>
              <div v-if="row.osseous_left_4000"><b>Izquierda Oseo 4000hz:</b> {{row.osseous_left_4000}} dB</div>
            </b-col>
          </b-row>

          <hr class="my-2">
          <p class="text-muted">Resultado</p>
              
          <b-row>
            <b-col md="6">
              <p class="text-muted">Derecho</p>
              <div v-if="!osseous_right"><span ><b>GAP:</b> </span> {{row.gap_right}}</div>
              <div v-if="osseous_right"><b>{{row.gap_right}}</b></div>
              <div><b>PTA Aereo:</b> {{row.air_right_pta}}</div>
              <div><b>Grado de severidad Aereo PTA:</b> {{row.severity_grade_air_right_pta}}</div>
              <div><b>Grado de severidad Aereo 4000 Hz:</b> {{row.severity_grade_air_right_4000}}</div>
              <div><b>Grado de severidad Aereo 6000 Hz:</b> {{row.severity_grade_air_right_6000}}</div>
              <div><b>Grado de severidad Aereo 8000 Hz:</b> {{row.severity_grade_air_right_8000}}</div>
            
              <div v-if="osseous_right">
                <div v-if="row.osseous_right_pta"><b>PTA Oseo:</b> {{row.osseous_right_pta}}</div>
                <div v-if="row.severity_grade_osseous_right_pta"><b>Grado de severidad Oseo PTA:</b> {{row.severity_grade_osseous_right_pta}}</div>
                <div v-if="row.severity_grade_osseous_right_4000"><b>Grado de severidad Oseo 4000 Hz:</b> {{row.severity_grade_osseous_right_4000}}</div>
              </div>
            </b-col>
            <b-col md="6">
              <p class="text-muted">Izquierdo</p>
              <div v-if="!osseous_left"><span ><b>GAP:</b> </span> {{row.gap_left}}</div>
              <div v-if="osseous_left"><b>{{row.gap_left}}</b></div>
              <div><b>PTA Aereo:</b> {{row.air_left_pta}}</div>
              <div><b>Grado de severidad Aereo PTA:</b> {{row.severity_grade_air_left_pta}}</div>
              <div><b>Grado de severidad Aereo 4000 Hz:</b> {{row.severity_grade_air_left_4000}}</div>
              <div><b>Grado de severidad Aereo 6000 Hz:</b> {{row.severity_grade_air_left_6000}}</div>
              <div><b>Grado de severidad Aereo 8000 Hz:</b> {{row.severity_grade_air_left_8000}}</div>
            
              <div v-if="osseous_left">
                <div v-if="row.osseous_left_pta"><b>PTA Oseo:</b> {{row.osseous_left_pta}}</div>
                <div v-if="row.severity_grade_osseous_left_pta"><b>Grado de severidad Oseo PTA:</b> {{row.severity_grade_osseous_left_pta}}</div>
                <div v-if="row.severity_grade_osseous_left_4000"><b>Grado de severidad Oseo 4000 Hz:</b> {{row.severity_grade_osseous_left_4000}}</div>
              </div>
            </b-col>
          </b-row>
        </b-card>
      </div>
</template>


<script>
import ResultsAudiometry from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/Audiometry/ResultsAudiometryComponent.vue';

export default {
  props:{
    row: {
      type: Object, 
      default() {
        return {
          date: "",
          previews_events: "",
          type: "",
          employee_id: "",
          exposition_level: "",
          air_left_500: "",
          air_left_1000: "",
          air_left_2000: "",
          air_left_3000: "",
          air_left_4000: "",
          air_left_6000: "",
          air_left_8000: "",
          air_right_500: "",
          air_right_1000: "",
          air_right_2000: "",
          air_right_3000: "",
          air_right_4000: "",
          air_right_6000: "",
          air_right_8000: "",
          osseous_right_500: "",
          osseous_right_1000: "",
          osseous_right_2000: "",
          osseous_right_3000: "",
          osseous_right_4000: "",
          osseous_left_500: "",
          osseous_left_1000: "",
          osseous_left_2000: "",
          osseous_left_3000: "",
          osseous_left_4000: "",
          recommendations: "",
          observation: "",
          epp: "",
          created_at: "",
          gap_left: "",
          air_left_pta: "",
          osseous_left_pta: "",
          severity_grade_air_left_pta: "",
          severity_grade_osseous_left_pta: "",
          severity_grade_air_left_4000: "",
          severity_grade_osseous_left_4000: "",
          severity_grade_air_left_6000: "",
          severity_grade_air_left_8000: "",
          gap_right: "",
          air_right_pta: "",
          osseous_right_pta: "",
          severity_grade_air_right_pta: "",
          severity_grade_osseous_right_pta: "",
          severity_grade_air_right_4000: "",
          severity_grade_osseous_right_4000: "",
          severity_grade_air_right_6000: "",
          severity_grade_air_right_8000: "",
        };
      }
    },
  },
  components: {
    ResultsAudiometry
  },
  computed:{
    osseous_right(){
      if(this.row.osseous_right_pta || this.row.severity_grade_osseous_right_pta || this.row.severity_grade_osseous_right_4000){
        return true;
      }
      return false;
    },
    osseous_left(){
      if(this.row.osseous_left_pta || this.row.severity_grade_osseous_left_pta || this.row.severity_grade_osseous_left_4000){
        return true;
      }
      return false;
    },
  },
  data:() => ({

    }),

  watch:{

  },
  mounted() {
    
  },
}
</script>
