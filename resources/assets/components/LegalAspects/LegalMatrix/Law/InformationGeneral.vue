<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
            <b-row>
              <b-col>
               <div class="text-center"><b>Título:</b><p> {{ law.name }} </p></div>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <div class="text-center"><b>Descripción:</b><p> {{ law.description }} </p></div>
              </b-col>
            </b-row>
            <b-row>
                <b-col class="text-center">
                    <div><b>Tema Ambiental:</b> {{ law.risk_aspect ? law.risk_aspect.name : ''}}</div>
                    <div><b>Tipo de norma:</b> {{ law.law_type ? law.law_type.name : ''}}</div>
                    <div><b>Número de norma:</b> {{ law.law_number }}</div>
                    <div><b>Año de norma:</b> {{ law.law_year }}</div>
                    <div><b>Ente:</b> {{ law.entity ? law.entity.name : '' }}</div>
                </b-col>
                <b-col class="text-center">
                    <div><b>Sistema que aplica:</b> {{ law.system_apply ? law.system_apply.name : '' }}</div>                
                    <div><b>Observaciones:</b> {{ law.observations }}</div>
                    <div><b>Fecha de creación:</b> {{ law.created_at }}</div>
                    <div><b>Tema SST:</b> {{ law.sst_risk ? law.sst_risk.name : '' }}</div>
                    <div><b>Derogada:</b> {{ law.repealed }}</div>
                    <div v-show="law.url"><a :href="law.url" target='blank'>Ver Archivo de la norma</a></div>
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
            system_apply: { name: "" },
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
