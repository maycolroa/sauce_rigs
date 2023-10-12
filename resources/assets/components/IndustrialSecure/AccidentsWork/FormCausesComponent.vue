<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
            	<b-card border-variant="primary" title="Análisis de causas">
				    <div class="col-md-12">
                        <b-form-row style="padding-top: 15px;">
                            <b-form-feedback class="d-block" v-if="form.errorsFor(`causes`)" style="padding-bottom: 10px;">
                            {{ form.errorsFor(`causes`) }}
                            </b-form-feedback>
                            <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                                <template v-for="(cause, index) in form.causes">
                                <b-card no-body class="mb-2 border-secondary" :key="cause.key" style="width: 100%;">
                                    <b-card-header class="bg-secondary">
                                    <b-row>
                                        <b-col cols="10" class="d-flex justify-content-between"> {{ form.causes[index].description ? form.causes[index].description : `Nuevo Causa principal ${index + 1}` }}</b-col>
                                        <b-col cols="2">
                                        <div class="float-right">
                                            <b-button-group>
                                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + cause.key+'-1'" variant="link">
                                                    <span class="collapse-icon"></span>
                                                </b-btn>
                                            </b-button-group>
                                        </div>
                                        </b-col>
                                    </b-row>
                                    </b-card-header>
                                    <b-collapse :id="`accordion${cause.key}-1`" visible :accordion="`accordion-123`">
                                    <b-card-body>
                                        <b-form-row>
                                            <vue-textarea class="col-md-12" v-model="form.causes[index].description" disabled name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.description`)" rows="1"></vue-textarea>
                                        </b-form-row>
                                        <b-form-row>
                                            <div class="col-md-12">
                                                <div class="float-right" style="padding-top: 10px;">
                                                <b-btn variant="primary" @click.prevent="addCauseSecondary(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa Secundaria</b-btn>
                                                </div>
                                            </div>
                                        </b-form-row>
                                        <b-form-row style="padding-top: 15px;">
                                            <b-form-feedback class="d-block" v-if="form.errorsFor(`causes.${index}.secondary`)" style="padding-bottom: 10px;">
                                                {{ form.errorsFor(`causes.${index}.secondary`) }}
                                            </b-form-feedback>
                                            <template v-for="(secondaryCause, index2) in cause.secondary">
                                                <b-card no-body class="mb-2 border-secondary" :key="secondaryCause.key" style="width: 100%;">
                                                    <b-card-header class="bg-secondary">
                                                        <b-row>
                                                            <b-col cols="10" class="d-flex justify-content-between"> {{ form.causes[index].secondary[index2].description ? form.causes[index].secondary[index2].description : `Nueva Causa Secundaria ${index2 + 1}` }}</b-col>
                                                            <b-col cols="2">
                                                                <div class="float-right">
                                                                    <b-button-group>
                                                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + secondaryCause.key+'-1'" variant="link">
                                                                        <span class="collapse-icon"></span>
                                                                        </b-btn>
                                                                        <b-btn @click.prevent="removeCauseSecondary(index, index2)" 
                                                                       
                                                                        size="sm" 
                                                                        variant="secondary icon-btn borderless"
                                                                        v-b-tooltip.top title="Eliminar Causa Secundaria">
                                                                            <span class="ion ion-md-close-circle"></span>
                                                                        </b-btn>
                                                                    </b-button-group>
                                                                </div>
                                                            </b-col>
                                                        </b-row>
                                                    </b-card-header>
                                                    <b-collapse :id="`accordion${secondaryCause.key}-1`" visible :accordion="`accordion-1234`">
                                                        <b-card-body>
                                                            <b-form-row>
                                                                <vue-textarea class="col-md-12" v-model="form.causes[index].secondary[index2].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.secondary.${index2}.description`)" rows="1"></vue-textarea>
                                                            </b-form-row>
                                                            <b-form-row>
                                                                <div class="col-md-12">
                                                                    <div class="float-right" style="padding-top: 10px;">
                                                                        <b-btn variant="primary" @click.prevent="addTertiary(index, index2)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa Terciaria</b-btn>
                                                                    </div>
                                                                </div>
                                                            </b-form-row>
                                                            <b-form-row style="padding-top: 15px;">
                                                                <b-form-feedback class="d-block" v-if="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary`)" style="padding-bottom: 10px;">
                                                                {{ form.errorsFor(`causes.${index}.secondary.${index2}.tertiary`) }}
                                                                </b-form-feedback>
                                                                <div class="table-responsive" style="padding-right: 15px;">
                                                                    <table class="table table-bordered table-hover" v-if="secondaryCause.tertiary.length > 0">
                                                                        <thead class="bg-secondary">
                                                                            <tr>
                                                                                <th scope="col" class="align-middle">#</th>
                                                                                <th scope="col" class="align-middle">Descripción</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <template v-for="(item, index3) in secondaryCause.tertiary">
                                                                                <tr :key="index3">
                                                                                    <td class="align-middle">
                                                                                        <b-btn @click.prevent="removeTertiary(index, index2, index3)" 
                                                                                        size="xs" 
                                                                                        variant="outline-primary icon-btn borderless"
                                                                                        v-b-tooltip.top title="Eliminar Causa terciaria">
                                                                                        <span class="ion ion-md-close-circle"></span>
                                                                                        </b-btn>
                                                                                    </td>
                                                                                    <td style="padding: 0px;">
                                                                                        <vue-textarea class="col-md-12" v-model="form.causes[index].secondary[index2].tertiary[index3].description" label="" name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary.${index3}.description`)" rows="1"></vue-textarea>
                                                                                    </td>
                                                                                </tr>
                                                                            </template>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </b-form-row>
                                                        </b-card-body>
                                                    </b-collapse>
                                                </b-card>
                                            </template>
                                        </b-form-row>
                                    </b-card-body>
                                    </b-collapse>
                                </b-card>
                                </template>
                            </perfect-scrollbar>
                        </b-form-row>
                    </div>
            	</b-card>
<br><br>
                <!--<b-card v-show="form.isEdit">
                    <div style="overflow-x: auto;" id="tree_cause" class="tree_cause">
                    </div>
                    <b-btn type="button" @click="download" variant="primary">Descargar Imagen</b-btn>
                </b-card>-->
				<br>
				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{"Cancelar"}}</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';

export default {
    components: {
        VueTextarea,
        VueInput,
        PerfectScrollbar,
        //CausesExport
    },
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        causes: {
			default() {
				return {
					causes: [
                        {
                            key: new Date().getTime(),
                            description: 'Causas Inmediatas',
                            secondary: []
                        },
                        {
                            key: new Date().getTime(),
                            description: 'Causas Básicas / Raíz',
                            secondary: []
                        }
                    ],
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
    },
    watch: {
        causes() {
            this.loading = false;
            this.form = Form.makeFrom(this.causes, this.method);
        }
    },
    data() {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.causes, this.method),
            
            holder: [],
            dragging: false,
            nodes: []
        };
    },
    methods: {
        submit(e) {
            this.loading = true;
            this.form
                .submit(e.target.action)
                .then(response => {
                    this.loading = false;
                    this.$router.push({ name: "industrialsecure-accidentswork" });
                })
                .catch(error => {
                    this.loading = false;
                });
        },
        addCause() {
            this.form.causes.push({
                key: new Date().getTime(),
                description: '',
                secondary: []
            })
        },
        removeCause(index)
        {
            if (this.form.causes[index].id != undefined)
                this.form.delete.causes.push(this.form.causes[index].id)

            this.form.causes.splice(index, 1)
        },
        addCauseSecondary(index)
        {
            this.form.causes[index].secondary.push({
                key: new Date().getTime(),
                description: '',
                tertiary: []
            })
        },
        removeCauseSecondary(indexObj, index)
        {
            if (this.form.causes[indexObj].secondary[index].id != undefined)
                this.form.delete.secondary.push(this.form.causes[indexObj].secondary[index].id)

            this.form.causes[indexObj].secondary.splice(index, 1)
        },
        addTertiary(indexObj, indexSub) 
        {
            this.form.causes[indexObj].secondary[indexSub].tertiary.push({
                key: new Date().getTime(),
                description: ''
            })
        },
        removeTertiary(indexObj, indexSub, index)
        {
            if (this.form.causes[indexObj].secondary[indexSub].tertiary[index].id != undefined)
                this.form.delete.tertiary.push(this.form.causes[indexObj].secondary[indexSub].tertiary[index].id)

            this.form.causes[indexObj].secondary[indexSub].tertiary.splice(index, 1)
        },
        /*download()
        {
            let serializer = new XMLSerializer();
            var svgData = serializer.serializeToString(document.getElementById('tree_cause'));
            var svgBlob = new Blob([svgData], {type:"image/svg+xml;charset=utf-8"});
            var svgUrl = URL.createObjectURL(svgBlob);
            var downloadLink = document.createElement("a");
            downloadLink.href = svgUrl;
            downloadLink.download = "treeCause.svg";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        },
        initTree() {
            const margin = {top: 20, right: 300, bottom: 100, left: 20},
            width  = 2000 - margin.left - margin.right,
            height = 1000 - margin.top - margin.bottom;

            // declares a tree layout and assigns the size
            const treemap = d3.tree().size([height, width]);

            //  assigns the data to a hierarchy using parent-child relationships
            let nodes = d3.hierarchy(this.causes.treeData, d => d.children);

            // maps the node data to the tree layout
            nodes = treemap(nodes);

            // append the svg object to the body of the page
            // appends a 'group' element to 'svg'
            // moves the 'group' element to the top left margin
            const svg = d3.select(".tree_cause").append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                .attr("id", 'svg_main')
                .style("background-color", 'white'),
                 g = svg.append("g")
                    .attr("transform",
                        "translate(" + margin.left + "," + margin.top + ")");

            // adds the links between the nodes
            const link = g.selectAll(".link")
                .data( nodes.descendants().slice(1))
            .enter().append("path")
                .attr("class", "link")
                .style("stroke", d => d.data.level)
            .attr("d", d => {
                return "M" + d.y + "," + d.x
                    + "," + d.parent.y + "," + d.parent.x;
                });

            // adds each node as a group
            const node = g.selectAll(".node")
                .data(nodes.descendants())
                .enter().append("g")
                .attr("id", "gbox")
                .attr("class", d => "node" + (d.children ? " node--internal" : " node--leaf"))
                .attr("transform", d => "translate(" + d.y + "," + d.x + ")");

            var svg2 = document.getElementById('svg_main');
            var bbox = svg2.firstElementChild.getBBox();

            console.log(bbox.height)

            // adds the circle to the node
            node.append("circle")
            .attr("r", d => d.data.value)
            .style("stroke", d => d.data.type)
            .style("fill", d => d.data.level);
            
            // adds the text to the node
            node.append("foreignObject")
            .attr("width", "250px")
            //.attr("width", d => parseInt(d.data.name.length * 10) + 'px')
            //.attr("height", "100px")
            .attr("height", d => this.builderHeight(parseInt(d.data.name.length)))
            .attr("dy", ".35em")
            .attr("x", d => this.builderX(d.data))
            .attr("y", 5)
            .style("border","1px")
            .style("border-color","black")
            .style("border-style","solid")
            .style("text-align", "center")
            .style("margin", "auto")
            .html(d => '<text style="background-color: white;">'+d.data.name+'</text>');
        },
        builderHeight(length) {
            let height = Math.ceil(length / 45) * 30;

            if (height < 45)
                height = 45;

            return height + 'px';
        },
        builderX(data) {
            return data.isPar != undefined && data.isPar ? -260 : data.value;
        }*/
    },
    mounted() {
        //this.initTree();
    }
};
</script>


<style lang="scss">
body {
background-color: #eee;
}

.node--internal {
    max-width: 100%;
    max-height: 100%;
}

.node--leaf {
    max-width: 100%;
    max-height: 100%;
}

.node circle {
fill: #fff;
stroke: steelblue;
stroke-width: 3px;
}

.node text { font: 12px sans-serif; }

.link {
fill: none;
stroke: #fff;
stroke-width: 2px;
}
</style>