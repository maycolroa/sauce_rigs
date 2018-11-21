<template>
      <div>
        <b-card bg-variant="transparent" border-variant="transparent" title="" body-class="py-0 px-0" class="px-0 box-shadow-none">
          <p v-if="!osseous"><span ><b>GAP:</b></span> {{gap}}</p>
          <p v-if="osseous"><b>{{gap}}</b></p>
          <div>  
            <p class="text-muted">Aereo</p>
            <div><b>PTA:</b> {{airPta}}</div>
            <div><b>Grado de severidad PTA:</b> {{severityGradeAirPta}}</div>
            <div><b>Grado de severidad 4000 Hz:</b> {{severityGradeAir4000}}</div>
            <div><b>Grado de severidad 6000 Hz:</b> {{severityGradeAir6000}}</div>
            <div><b>Grado de severidad 8000 Hz:</b> {{severityGradeAir8000}}</div>
          </div>
          
          <div v-if="osseous">
            <hr>
            <p class="text-muted">Oseo</p>
            <div v-if="osseousPta"><b>PTA:</b> {{osseousPta}}</div>
            <div v-if="severityGradeOsseousPta"><b>Grado de severidad PTA:</b> {{severityGradeOsseousPta}}</div>
            <div v-if="severityGradeOsseous4000"><b>Grado de severidad 4000 Hz:</b> {{severityGradeOsseous4000}}</div>
          </div>
        </b-card>
      </div>
</template>


<script>

export default {
  name: 'results-audiometry',
  props:{
    gap: {type: String, default: null},
    airPta: {type: Number, default: null},
    osseousPta: {type: Number, default: null},
    severityGradeAirPta: {type: String, default: null},
    severityGradeOsseousPta: {type: String, default: null},
    severityGradeAir4000: {type: String, default: null},
    severityGradeOsseous4000: {type: String, default: null},
    severityGradeAir6000: {type: String, default: null},
    severityGradeAir8000: {type: String, default: null},
  },
  components: {
    
  },
  computed:{
    osseous(){
      if(this.osseousPta || this.severityGradeOsseousPta || this.severityGradeOsseous4000){
        return true;
      }
      return false;
    },
  },
  data:() => ({

    }),

  watch:{

  },
  mounted(){

  }
}
</script>
