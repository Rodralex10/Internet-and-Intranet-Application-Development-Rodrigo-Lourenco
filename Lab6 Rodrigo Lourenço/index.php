<?php

function buildFixedGraph() {
    return [
        "Bratislava" => ["Trnava" => 50, "Nitra" => 92, "Pezinok" => 21],
        "Trnava" => ["Bratislava" => 50, "Nitra" => 47, "Pezinok" => 34],
        "Nitra" => ["Bratislava" => 92, "Trnava" => 47, "Nové Zámky" => 41, "Levice" => 70],
        "Nové Zámky" => ["Nitra" => 41, "Komárno" => 47],
        "Komárno" => ["Nové Zámky" => 47],
        "Levice" => ["Nitra" => 70, "Zvolen" => 65],
        "Zvolen" => ["Levice" => 65, "Banská Bystrica" => 21, "Lučenec" => 56],
        "Banská Bystrica" => ["Zvolen" => 21, "Martin" => 48],
        "Lučenec" => ["Zvolen" => 56, "Košice" => 130],
        "Košice" => ["Lučenec" => 130, "Prešov" => 35, "Spišská Nová Ves" => 77],
        "Prešov" => ["Košice" => 35, "Poprad" => 82],
        "Poprad" => ["Prešov" => 82, "Spišská Nová Ves" => 27, "Žilina" => 137],
        "Spišská Nová Ves" => ["Poprad" => 27, "Košice" => 77],
        "Žilina" => ["Poprad" => 137, "Martin" => 29, "Považská Bystrica" => 45, "Prievidza" => 85],
        "Martin" => ["Žilina" => 29, "Banská Bystrica" => 48, "Trenčín" => 69],
        "Trenčín" => ["Martin" => 69, "Považská Bystrica" => 42, "Prievidza" => 59],
        "Považská Bystrica" => ["Žilina" => 45, "Trenčín" => 42],
        "Prievidza" => ["Žilina" => 85, "Trenčín" => 59],
        "Pezinok" => ["Bratislava" => 21, "Trnava" => 34],
    ];
}

function dijkstra($graph, $start, $end) {
    $dist = [];
    $prev = [];
    $visited = [];

    foreach ($graph as $node => $_) {
        $dist[$node] = INF;
        $prev[$node] = null;
    }

    $dist[$start] = 0;

    while (count($visited) < count($graph)) {
        $minNode = null;
        foreach ($dist as $node => $d) {
            if (!isset($visited[$node]) && ($minNode === null || $d < $dist[$minNode])) {
                $minNode = $node;
            }
        }

        if ($minNode === null || !isset($graph[$minNode])) break;

        foreach ($graph[$minNode] as $neighbor => $cost) {
            $alt = $dist[$minNode] + $cost;
            if ($alt < $dist[$neighbor]) {
                $dist[$neighbor] = $alt;
                $prev[$neighbor] = $minNode;
            }
        }

        $visited[$minNode] = true;
    }

    if ($dist[$end] === INF) return [INF, []];

    $path = [];
    for ($at = $end; $at !== null; $at = $prev[$at]) {
        array_unshift($path, $at);
    }

    return [$dist[$end], $path];
}

function dijkstraAll($graph, $start) {
    $dist = [];
    $visited = [];

    foreach ($graph as $node => $_) {
        $dist[$node] = INF;
    }

    $dist[$start] = 0;

    while (count($visited) < count($graph)) {
        $minNode = null;
        foreach ($dist as $node => $d) {
            if (!isset($visited[$node]) && ($minNode === null || $d < $dist[$minNode])) {
                $minNode = $node;
            }
        }

        if ($minNode === null || !isset($graph[$minNode])) break;

        foreach ($graph[$minNode] as $neighbor => $cost) {
            $alt = $dist[$minNode] + $cost;
            if ($alt < $dist[$neighbor]) {
                $dist[$neighbor] = $alt;
            }
        }

        $visited[$minNode] = true;
    }

    return $dist;
}

function generateDistanceTable($graph) {
    $html = "<h2>Distance Table (Shortest Paths Between All Cities)</h2><table border='1' cellpadding='5'><tr><th></th>";
    foreach ($graph as $city => $_) {
        $html .= "<th>$city</th>";
    }
    $html .= "</tr>";

    foreach ($graph as $from => $_) {
        $html .= "<tr><th>$from</th>";
        $distances = dijkstraAll($graph, $from);
        foreach ($graph as $to => $_) {
            $val = $distances[$to] === INF ? '∞' : $distances[$to];
            $html .= "<td>$val</td>";
        }
        $html .= "</tr>";
    }

    $html .= "</table>";
    return $html;
}

session_start();
$graph = buildFixedGraph();
$cities = array_keys($graph);
$start = $cities[array_rand($cities)];
$end = $cities[array_rand($cities)];
list($distance, $path) = dijkstra($graph, $start, $end);
$_SESSION['graph'] = $graph;
$_SESSION['path'] = $path;

echo "<h1>Assignment 6 - Shortest Path Between Slovak Cities</h1>";
echo "<h2>From <strong>$start</strong> to <strong>$end</strong></h2>";
if ($distance === INF) {
    echo "<p>No path found.</p>";
} else {
    echo "<p><strong>Total Distance:</strong> $distance km</p>";
    echo "<p><strong>Route:</strong> " . implode(" → ", $path) . "</p>";
}

echo "<h2>Graph Visualization</h2>";
echo "<img src='graph.php' alt='Graph Image'><br><br>";

echo generateDistanceTable($graph);
echo "<br><form method='get'><button type='submit'>Generate New Path</button></form>";
?>

