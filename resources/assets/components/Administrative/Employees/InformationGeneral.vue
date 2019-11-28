<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
            <b-row>
                <b-col>
                    <div><b>Identificación:</b> {{employee.identification}}</div>
                    <div><b>Nombre:</b> {{employee.name}}</div>
                    <div><b>Fecha de nacimiento:</b> {{employee.date_of_birth}}</div>
                    <div><b>Edad:</b> {{employee.age}}</div>
                    <div><b>Sexo:</b> {{employee.sex}}</div>
                    <div><b>Fecha de ingreso:</b> {{employee.income_date}}</div>
                    <div><b>{{ keywordCheck('position') }}:</b> {{employee.position}}</div>
                </b-col>
                <b-col>
                    <div><b>{{ keywordCheck('regional') }}:</b> {{employee.regional}}</div>
                    <div><b>{{ keywordCheck('headquarter') }}:</b> {{employee.headquarter}}</div>
                    <div><b>{{ keywordCheck('process') }}:</b> {{employee.process}}</div>
                    <div><b>{{ keywordCheck('area') }}:</b> {{employee.area}}</div>
                    <div><b>Negocio:</b> {{employee.deal}}</div>
                    <div><b>{{ keywordCheck('eps') }}:</b> {{employee.eps}}</div>
                </b-col>
            </b-row>
        </b-card>
      </div>
</template>


<script>

export default {
  name: 'employee-information',
  props:{
    employee: {
      default() {
        return {
            identification: '',
            name: '',
            date_of_birth: '',
            age: '',
            sex: '',
            email: '',
            income_date: '',
            regional: '',
            headquarter: '',
            area: '',
            process: '',
            position: '',
            business: '',
            eps: '',
            deal: ''
        };
      }
    }
  }
}
</script>
