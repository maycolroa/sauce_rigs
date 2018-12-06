<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
            <b-row>
                <b-col>
                    <div><b>Identificación:</b> {{employee.identification}}</div>
                    <div><b>Nombre:</b> {{employee.name}}</div>
                    <div><b>Fecha de cumpleaños:</b> {{employee.date_of_birth}}</div>
                    <div><b>Edad:</b> {{employee.age}}</div>
                    <div><b>Sexo:</b> {{employee.sex}}</div>
                    <div><b>Email:</b> {{employee.email}}</div>
                    <div><b>Fecha de ingreso:</b> {{employee.income_date}}</div>
                </b-col>
                <b-col>
                    <div><b>Regional:</b> {{employee.regional}}</div>
                    <div><b>Sede:</b> {{employee.headquarter}}</div>
                    <div><b>Área:</b> {{employee.area}}</div>
                    <div><b>Proceso:</b> {{employee.process}}</div>
                    <div><b>Cargo:</b> {{employee.position}}</div>
                    <div><b>Centro de costo:</b> {{employee.business}}</div>
                    <div><b>EPS:</b> {{employee.eps}}</div>
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
            eps: ''
        };
      }
    }
  }
}
</script>
