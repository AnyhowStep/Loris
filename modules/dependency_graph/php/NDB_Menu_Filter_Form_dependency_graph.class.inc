<?php
    header("Content-Security-Policy: script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com;");
    $extra_dir = [];
    if (!isset($_GET["ignore_libraries"])) {
        $extra_dir[] = "php/libraries";
    }

    $modules = [];
    if (isset($_GET["all_modules"])) {
        $modules[] = "modules";
    } else {
        $modules = $_GET["modules"] ?? [];
        if (!is_array($modules)) {
            $modules = [];
        }
        for ($i=0; $i<count($modules); ++$i) {
            $cur = $modules[$i];
            if (!is_string($cur)) {
                throw new Exception("string expected for modules");
            }
            $cur = str_replace(".", "", $cur);
            $cur = str_replace("/", "", $cur);
            $modules[$i] = "modules/{$cur}";
        }
    }

    $extra_dir = array_merge($extra_dir, $modules);
    require_once(__DIR__ . "/Graph.php");
?>
<html>
    <head>
        <script>
            <?php
                echo "const node_arr = ";
                echo json_encode($node_arr);
                echo ";\n";
                echo "const edge_arr = ";
                echo json_encode($edge_arr);
                echo ";\n";
            ?>
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.0/vis.min.js">
        </script>
        <link rel="stylesheet" type="title/css" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.0/vis.min.css"/>
    </head>
    <body style="background-color:black;">
        <div id="graph">
        </div>
    </body>
    <script>
        document.body.onload = function () {
            for (let i=0; i<node_arr.length; ++i) {
                const node  = node_arr[i];
                const slash = node.path.lastIndexOf("/");
                node.label = "#" + node.id + ": " + node.path.substr(slash);
                node.group = node.path.substr(0, slash);
                node.group = node.group.substr(node.group.lastIndexOf("/"));
                //console.log(node);
            }
            for (let i=0; i<edge_arr.length; ++i) {
                const edge = edge_arr[i];
                const node = node_arr[edge.to-1];
                if (node.value) {
                    node.value += 1;
                } else {
                    node.value = 2;
                }
                if (node.label == "/$_POST") {
                    console.log(node);
                }
            }
            //console.log(edge_arr.length);
            const container = document.getElementById("graph");
            const data = {
                nodes: new vis.DataSet(node_arr),
                edges: new vis.DataSet(edge_arr)
            };
            const options = {
                layout: {
                    improvedLayout: false
                },
                physics: {
                    enabled: false,
                    solver: "hierarchicalRepulsion",
                    hierarchicalRepulsion: {
                        nodeDistance: 2000,
                        springLength: 4000
                    },
                    stabilization: {
                        iterations: 100
                    }
                },
                nodes: {
                    shape: "dot",
                    font: {
                        color: "red"
                    },
                    scaling: {
                        min: 10,
                        max: 600,
                        label: {
                            enabled: true
                        }
                    }
                },
                edges: {
                    arrows: {
                        to: {
                            enabled: true
                        }
                    },
                    smooth: {
                        type: "continuous"
                    }
                }
            };

            const network = new vis.Network(container, data, options);
            setTimeout(function () {
                options.physics.enabled = true;
                network.setOptions(options);
            }, 1000);
            setTimeout(function () {
                options.physics.enabled = false;
                network.setOptions(options);
            }, node_arr.length * 500);
        };
    </script>
</html>
<?php die(); ?>
