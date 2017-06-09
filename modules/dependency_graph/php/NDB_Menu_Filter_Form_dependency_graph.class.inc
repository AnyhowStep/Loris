<?php
    header("Content-Security-Policy: script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com;");

    require_once(__DIR__ . "/Graph.php");

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

    $root = __DIR__ . "/../../../";
    $directory_arr = array_merge($extra_dir, $modules);

    $g = new Graph();
    $g->addSuperGlobals();
    foreach ($directory_arr as $directory) {
        scanForNodes($g, "{$root}/{$directory}");
    }

    $g->findEdges();
    //echo "Nodes: " . count($g->getNodeArr()) . "\n";
    //echo "Edges: " . count($g->getEdgeArr()) . "\n";

    $focus_label = $_GET["focus_label"] ?? "";
    if (!is_string($focus_label)) {
        throw new Exception("focus label must be a string");
    }

    $focus_node = null;
    if ($focus_label) {
        foreach ($g->getNodeArr() as $n) {
            if ($n->getLabel() == $focus_label) {
                $focus_node = $n;
                break;
            }
        }
    }

    $node_set = [];

    $edge_arr = [];
    foreach ($g->getEdgeArr() as $e) {
        $edge = (object)[
            "from"=>$e->getFrom()->getId(),
            "to"  =>$e->getTo()->getId()
        ];
        if (!is_null($focus_node)) {
            if ($e->getFrom() != $focus_node && $e->getTo() != $focus_node) {
                continue;
            }
        }
        $edge_arr[] = $edge;
        $node_set[$e->getFrom()->getId()] = $e->getFrom();
        $node_set[$e->getTo()->getId()] = $e->getTo();
    }

    foreach ($node_set as $id=>$n) {
        $node_set[$id] = (object)[
            "path"=>$n->getPath(),
            "id"=>$n->getId(),
            "tli"=>$n->getTopLevelIdentifierArr(),
            "used"=>$n->getUsedIdentifierArr(),
            "label"=>$n->getLabel(),
            "group"=>$n->getGroup()
        ];
    }
    /*
    echo "const node_arr = ";
    echo json_encode($node_arr);
    echo ";\n";
    echo "const edge_arr = ";
    echo json_encode($edge_arr);
    echo ";\n";*/
?>
<html>
    <head>
        <script>
            <?php
                echo "const node_set = ";
                echo json_encode($node_set);
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
            for (let i=0; i<edge_arr.length; ++i) {
                const edge = edge_arr[i];
                const node = node_set[edge.to];
                if (node.value) {
                    node.value += 1;
                } else {
                    node.value = 5;
                }
                if (node.label == "/$_POST") {
                    console.log(node);
                }
            }
            //console.log(edge_arr.length);
            const container = document.getElementById("graph");
            const data = {
                nodes: new vis.DataSet(
                    Object.values(node_set)
                ),
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
            }, Object.keys(node_set).length * 500);
        };
    </script>
</html>
<?php die(); ?>
