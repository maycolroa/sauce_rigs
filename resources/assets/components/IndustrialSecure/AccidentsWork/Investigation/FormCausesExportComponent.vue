<template>
    <button @click="download">Descargar imagen</button>
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
                    treeData: {}
				};
			}
		}
    },
    methods: {
        download()
        {
            let img = new Image(),
                serializer = new XMLSerializer(),
                svgStr = serializer.serializeToString(document.getElementById('svg_main'));

            img.src = 'data:image/svg+xml;base64,'+window.btoa(svgStr);

            // You could also use the actual string without base64 encoding it:
            //img.src = "data:image/svg+xml;utf8," + svgStr;

            var canvas = document.createElement("canvas");

            var w=900;
            var h=400;

            canvas.width = w;
            canvas.height = h;
            canvas.getContext("2d").drawImage(img,0,0,w,h);
            canvas.style="background-color: white;"

            var imgURL = canvas.toDataURL("image/png");


            var dlLink = document.createElement('a');
            dlLink.download = "image";
            dlLink.href = imgURL;
            dlLink.dataset.downloadurl = ["image/png", dlLink.download, dlLink.href].join(':');

            document.body.appendChild(dlLink);
            dlLink.click();
            document.body.removeChild(dlLink);
        },
        initTree() {
            const margin = {top: 20, right: 190, bottom: 30, left: 90},
            width  = 1200 - margin.left - margin.right,
            height = 600 - margin.top - margin.bottom;

            // declares a tree layout and assigns the size
            const treemap = d3.tree().size([height, width]);

            //  assigns the data to a hierarchy using parent-child relationships
            let nodes = d3.hierarchy(this.causes.treeData, d => d.children);

            // maps the node data to the tree layout
            nodes = treemap(nodes);

            // append the svg object to the body of the page
            // appends a 'group' element to 'svg'
            // moves the 'group' element to the top left margin
            const svg = d3.select("body").append("svg")
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
                .attr("class", d => "node" + (d.children ? " node--internal" : " node--leaf"))
                .attr("transform", d => "translate(" + d.y + "," + d.x + ")");

            // adds the circle to the node
            node.append("circle")
            .attr("r", d => d.data.value)
            .style("stroke", d => d.data.type)
            .style("fill", d => d.data.level);
            
            // adds the text to the node
            node.append("foreignObject")
            .attr("lengthAdjust", "spacing")
            .attr("width", "180")
            .attr("height", "100")
            .attr("dy", ".35em")
            .attr("x", d => d.data.value + 5)
            .attr("y", 5)
            .html(d => '<text style="background-color: white; border: 3px; border-color: black;">'+d.data.name+'</text>');
        }
    },
    mounted() {
        this.initTree();
    }
};
</script>

<style lang="scss">
body {
background-color: #eee;
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
