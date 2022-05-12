<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
          <b-row>
              <b-col>
                  <div><b>Identificación:</b> {{ employeeDetail.identification }}</div>
                  <div><b>Nombre:</b> {{ employeeDetail.name }}</div>
                  <div><b>Fecha de nacimiento:</b> {{ dateBirth }}</div>
                  <div><b>Sexo:</b> {{ employeeDetail.sex }}</div>
                  <div><b>Fecha de ingreso:</b> {{ incomeDate }}</div>
                  <div><b>Antigüedad:</b> {{ employeeDetail.antiquity }}</div>
                  <div><b>Edad:</b> {{ employeeDetail.age }}</div>
              </b-col>
              <b-col>
                  <div><b>{{ keywordCheck('position') }}:</b> {{ employeeDetail.position ? employeeDetail.position.name : '' }}</div>
                  <div><b>{{ keywordCheck('businesses') }}:</b> {{ employeeDetail.business ? employeeDetail.business.name : '' }}</div>
                  <div><b>{{ keywordCheck('regional') }}:</b> {{ employeeDetail.regional ? employeeDetail.regional.name : ''}}</div>
                  <div><b>{{ keywordCheck('headquarter') }}:</b> {{ employeeDetail.headquarter ? employeeDetail.headquarter.name : '' }}</div>
                  <div><b>{{ keywordCheck('process') }}:</b> {{ employeeDetail.process ? employeeDetail.process.name : '' }}</div>
                  <div v-if="employeeDetail.area"><b>{{ keywordCheck('area') }}:</b> {{ employeeDetail.area.name }}</div>
                  <div><b>{{ keywordCheck('eps') }}:</b> {{ employeeDetail.eps ? `${employeeDetail.eps.code} - ${employeeDetail.eps.name}` : '' }}</div>
                  <div><b>{{ keywordCheck('afp') }}:</b> {{ employeeDetail.afp ? `${employeeDetail.afp.code} - ${employeeDetail.afp.name}` : '' }}</div>
                  <div><b>{{ keywordCheck('arl') }}:</b> {{ employeeDetail.arl ? `${employeeDetail.arl.code} - ${employeeDetail.arl.name}` : '' }}</div>
              </b-col>
          </b-row>
        </b-card>
      </div>
</template>


<script>

export default {
  props:{
    employeeDetail: {
      default() {
        return {
            identification: '',
            name: '',
            sex: '',
            date_of_birth: '',
            income_date: '',
            antiquity: '',
            age: '',
            position: '',
            business: '',
            regional: '',
            headquarter: '',
            process: '',
            area: '',
            eps: '',
            afp: '',
            arl: ''
        };
      }
    }
  },
  computed: {
    dateBirth() {
      return this.formatDate(this.employeeDetail.date_of_birth)
    },
    incomeDate() {
      return this.formatDate(this.employeeDetail.income_date)
    }
  }
}
</script>
