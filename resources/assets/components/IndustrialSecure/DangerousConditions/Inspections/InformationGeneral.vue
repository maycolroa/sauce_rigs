<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
            <b-row>
                <b-col class="text-left">
                  <div><b>Inspección:</b> <br>{{ qualification.inspection }}</div>
                  <div><b>Fecha Creación:</b> <br>{{ qualification.created_at }}</div>
                </b-col>
                <b-col class="text-left">
                  <div><b>Sede:</b> <br>{{ qualification.headquarter }}</div>
                  <div><b>Fecha Calificación:</b> <br>{{ qualification.qualification_date }}</div>
                </b-col>
                <b-col class="text-left">
                  <div><b>Área:</b> <br>{{ qualification.area }}</div>
                  <div><b>Responsable:</b> <br>{{ qualification.qualifier }}</div>
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
            headquarter: '',
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
