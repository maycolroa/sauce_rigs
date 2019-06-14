<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
            <b-row>
                <b-col>
                    <div><b>Tìtulo:</b> {{ law.name }}</div>
                    <div><b>Descripciòn:</b> {{ law.description }}</div>
                    <div><b>Riesgo Ambiental:</b> {{ law.risk_aspect ? law.risk_aspect.name : ''}}</div>
                    <div><b>Tipo de norma</b> {{ law.law_type ? law.law_type.name : ''}}</div>
                    <div><b>Nùmero de norma</b> {{ law.law_number }}</div>
                    <div><b>Año de norma</b> {{ law.law_year }}</div>
                </b-col>
                <b-col>
                    <div><b>Ente:</b> {{ law.entity ? law.entity.name : '' }}</div>
                    <div><b>Sistema que aplica:</b> {{ law.apply_system }}</div>
                    <div><b>Observaciones:</b> {{ law.observations }}</div>
                    <div><b>Fecha de creaciòn:</b> {{ law.created_at }}</div>
                    <div><b>Riesgo SST</b> {{ law.sst_risk ? law.sst_risk.name : '' }}</div>
                    <div><b>Derogada</b> {{ law.repealed }}</div>
                </b-col>
            </b-row>
        </b-card>
      </div>
</template>


<script>

export default {
  props:{
    law: {
      default() {
        return {
            name: '',
            law_number: '',
            apply_system: '',
            law_year: '',
            law_type: { name: "" },
            description: '',
            observations: '',
            risk_aspect: { name: "" },
            entity: { name: "" },
            sst_risk: { name: "" },
            repealed: '',
            created_at: ''
        };
      }
    }
  }
}
</script>
