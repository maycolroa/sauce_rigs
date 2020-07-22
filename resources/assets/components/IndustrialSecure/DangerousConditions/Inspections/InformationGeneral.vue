<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
            <b-row>
                <b-col class="text-left">
                  <div><b>Inspección:</b> <br>{{ qualification.inspection }}</div>
                  <div><b>Responsable:</b> <br>{{ qualification.qualifier }}</div>
                  <div><b>Fecha Creación:</b> <br>{{ qualification.created_at }}</div>
                  <div><b>Fecha Calificación:</b> <br>{{ qualification.qualification_date }}</div>
                </b-col>
                <b-col class="text-left">
                  <div v-if="locationForm.regional == 'SI'"><b>{{ keywordCheck('regional') }}:</b> <br>{{ qualification.regional }}</div>
                  <div v-if="locationForm.headquarter == 'SI'"><b>{{ keywordCheck('headquarter') }}:</b> <br>{{ qualification.headquarter }}</div>
                  <div v-if="locationForm.process == 'SI'"><b>{{ keywordCheck('process') }}:</b> <br>{{ qualification.process }}</div>
                  <div v-if="locationForm.area == 'SI'"><b>{{ keywordCheck('area') }}:</b> <br>{{ qualification.area }}</div>
                </b-col>
            </b-row>
        </b-card>
      </div>
</template>


<script>

export default {
  props:{
    qualification: {
      default() {
        return {
            inspection: '',
            regional: '',
            headquarter: '',
            process: '',
            area: '',
            created_at: '',
            qualification_date: '',
            qualifier: ''
        };
      }
    }
  }
}
</script>
