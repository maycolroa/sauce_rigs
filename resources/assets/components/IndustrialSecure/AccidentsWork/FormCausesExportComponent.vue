<template>
        <flowy
            class="q-mx-auto"
            :nodes="causes.nodes"
            id="tree_cause"
        ></flowy>
</template>

<script>
export default {
    props: {
        causes: {
			default() {
				return {
					causes: [],
                    accident_id: '',
                    isEdit: false,
                    delete: {
                        causes: [],
                        secondary: [],
                        tertiary: []
                    },
                    nodes: []
				};
			}
		}
    }
};
</script>