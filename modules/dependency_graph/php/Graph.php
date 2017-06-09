<?php
    class Node {
        private static function FindNextIdentifier (array $tokens, int $start) : ?string {
            for ($i=$start; $i<count($tokens); ++$i) {
                $cur = $tokens[$i];
                if (is_string($cur)) {
                    if ($cur == "(") {
                        //It's probably a T_FUNCTION for an anonymous function, ignore it
                        return null;
                    }
                    continue;
                }
                $nme = token_name($cur[0]);
                if ($nme == "T_STRING") {
                    return $cur[1];
                }
            }
            throw new Exception("No identifier found");
        }
        private static function FindAllTopLevelIdentifiers (string $path) : array {
            $raw    = file_get_contents($path);
            $tokens = token_get_all($raw);

            $result = [];

            $skip_counter = 0;
            for ($i=0; $i<count($tokens); ++$i) {
                $cur = $tokens[$i];
                if (is_string($cur)) {
                    if ($cur == "{") {
                        ++$skip_counter;
                    } else if ($cur == "}") {
                        --$skip_counter;
                    }
                    if ($skip_counter < 0) {
                        throw new Exception("More closing than opening braces");
                    }
                    continue;
                }
                $nme = token_name($cur[0]);
                if ($nme == "T_CURLY_OPEN") {
                    ++$skip_counter;
                }
                if ($skip_counter > 0) {
                    continue;
                }

                if ($nme == "T_CLASS" || $nme == "T_INTERFACE" || $nme == "T_FUNCTION") {
                    $identifier = self::FindNextIdentifier($tokens, $i+1);
                    if (!is_null($identifier))  {
                        $result[] = $identifier;
                    }
                }
            }

            return $result;
        }
        private static function FindAllUsedIdentifiers (string $path) : array {
            $raw    = file_get_contents($path);
            $tokens = token_get_all($raw);

            $result = [];

            for ($i=0; $i<count($tokens); ++$i) {
                $cur = $tokens[$i];
                if (is_string($cur)) {
                    continue;
                }
                $nme = token_name($cur[0]);

                if ($nme == "T_STRING") {
                    $result[] = $cur[1];
                }
                if ($nme == "T_VARIABLE") {
                    $result[] = $cur[1];
                }
            }

            return $result;
        }

        private static $Id = 0;

        private $id;
        private $path = null;
        private $tli_arr;
        private $used_arr;

        public function __construct () {

        }
        public function setSuperGlobal (string $name) {
            if (!is_null($this->path)) {
                throw new Exception("Already set");
            }
            ++self::$Id;
            $this->id = self::$Id;
            $this->path = "SUPERGLOBAL/{$name}";
            $this->tli_arr = [$name];
            $this->used_arr = [];
            return $this;
        }
        public function setPath (string $path) {
            if (!is_null($this->path)) {
                throw new Exception("Already set");
            }
            //echo "creating node {$path}\n";
            ++self::$Id;
            $this->id = self::$Id;
            $this->path = $path;
            $this->tli_arr = self::FindAllTopLevelIdentifiers($path);
            $this->used_arr = self::FindAllUsedIdentifiers($path);
            return $this;
        }
        public function getId () : int {
            return $this->id;
        }
        public function getPath () : string {
            return $this->path;
        }
        public function getTopLevelIdentifierArr () : array {
            return $this->tli_arr;
        }
        public function getUsedIdentifierArr () : array {
            return $this->used_arr;
        }
    }
    class Edge {
        private $from;
        private $to;
        public function __construct (Node $from, Node $to) {
            $this->from = $from;
            $this->to   = $to;
        }
        public function getFrom () : Node {
            return $this->from;
        }
        public function getTo () : Node {
            return $this->to;
        }
    }
    class Graph {
        private $path2node = [];
        private $tli2node_arr = [];

        private $node_arr = [];
        private $edge_arr = [];

        private function addTopLevelIdentifier (string $tli, Node $n) {
            if (!isset($this->tli2node_arr[$tli])) {
                $this->tli2node_arr[$tli] = [];
            }
            $this->tli2node_arr[$tli][] = $n;
        }
        public function addNode (Node $n) {
            $this->node_arr[] = $n;

            $this->path2node[$n->getPath()] = $n;

            foreach ($n->getTopLevelIdentifierArr() as $tli) {
                $this->addTopLevelIdentifier($tli, $n);
            }
            //var_dump($n->getTopLevelIdentifierArr());
            //var_dump($n->getUsedIdentifierArr());
        }
        private function addEdges (Node $n, string $identifier) {
            $dependency_arr = $this->tli2node_arr[$identifier] ?? [];
            foreach ($dependency_arr as $d) {
                if ($n == $d) {
                    continue;
                }
                $this->edge_arr[] = new Edge($n, $d);
            }
        }
        public function findEdges () {
            foreach ($this->path2node as $n) {
                foreach ($n->getUsedIdentifierArr() as $used) {
                    $this->addEdges($n, $used);
                }
            }
        }
        public function getNodeArr () : array {
            return $this->node_arr;
        }
        public function getEdgeArr () : array {
            return $this->edge_arr;
        }

        public function __construct () {
        }
        public function addSuperGlobals () {
            $this->addNode((new Node())->setSuperGlobal('$GLOBALS'));
            $this->addNode((new Node())->setSuperGlobal('$_SERVER'));
            $this->addNode((new Node())->setSuperGlobal('$_GET'));
            $this->addNode((new Node())->setSuperGlobal('$_POST'));
            $this->addNode((new Node())->setSuperGlobal('$_FILES'));
            $this->addNode((new Node())->setSuperGlobal('$_COOKIE'));
            $this->addNode((new Node())->setSuperGlobal('$_SESSION'));
            $this->addNode((new Node())->setSuperGlobal('$_REQUEST'));
            $this->addNode((new Node())->setSuperGlobal('$_ENV'));
        }
    }
    function scanForNodes (Graph $g, string $directory_path) {
        $name_arr = scandir($directory_path);
        foreach ($name_arr as $name) {
            $skip = false;
            switch ($name) {
                case ".":
                case "..": {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
            $path = "{$directory_path}/{$name}";
            if (is_dir($path)) {
                //echo "scanning {$path}\n";
                scanForNodes($g, $path);
            } else {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if ($ext == "inc" || $ext == "php") {
                    $g->addNode((new Node())->setPath($path));
                }
            }
        }
    }

    $root = __DIR__ . "/../../../";
    $extra_dir  = $extra_dir ?? [];
    $directory_arr = [
        //"htdocs",
        //"modules",
        //"php/libraries",
        //"modules/data_team_helper"
    ];
    $directory_arr = array_merge($directory_arr, $extra_dir);

    $g = new Graph();
    foreach ($directory_arr as $directory) {
        scanForNodes($g, "{$root}/{$directory}");
    }
    $g->addSuperGlobals();

    $g->findEdges();
    //echo "Nodes: " . count($g->getNodeArr()) . "\n";
    //echo "Edges: " . count($g->getEdgeArr()) . "\n";

    $node_arr = [];
    foreach ($g->getNodeArr() as $n) {
        $node_arr[] = (object)[
            "path"=>$n->getPath(),
            "id"=>$n->getId(),
            "tli"=>$n->getTopLevelIdentifierArr(),
            "used"=>$n->getUsedIdentifierArr()
        ];
        //if ($n->getPath)
    }
    $edge_arr = [];
    foreach ($g->getEdgeArr() as $e) {
        $edge_arr[] = (object)[
            "from"=>$e->getFrom()->getId(),
            "to"  =>$e->getTo()->getId()
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
