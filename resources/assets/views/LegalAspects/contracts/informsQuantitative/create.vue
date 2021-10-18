<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="CREAR INFORME"
			url="legalaspects-informs"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inform-component
                url="/legalAspects/inform"
                method="POST"
                :cancel-url="{ name: 'legalaspects-informs'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInformComponent from '@/components/LegalAspects/Contracts/MonthInforms/FormInformComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-informs-create',
  metaInfo: {
    title: 'Informes - Crear'
  },
  components:{
    FormInformComponent
  },
  data(){
    return {}
  }
}
</script>
