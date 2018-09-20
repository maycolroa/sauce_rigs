<template>
      <div>
        {{audiometry['air_leftPta']}}
      </div>
</template>


<script>

export default {
  name: 'results-audiometry',
  props:{
    gap: {type: Number, required: true},
    airPta: {type: Number, required: true},
    osseousPta: {type: Number, required: true},
    severityGradeAirPta: {type: String, required: true},
    severityGradeOsseousPta: {type: String, required: true},
    severityGradeAir4000: {type: String, required: true},
    severityGradeOsseous4000: {type: String, required: true},
    severityGradeAir6000: {type: String, required: true},
    severityGradeAir8000: {type: String, required: true},
  },
  components: {
    
  },
  data:() => ({

    }),

  watch:{

  },
  mounted(){

  }
}
</script>
