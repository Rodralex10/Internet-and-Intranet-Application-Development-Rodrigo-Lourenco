<?php
session_start();

function drawGraph($graph, $path = []) {
    $width = 1400;
    $height = 1100;
    $margin = 50;
    $image = imagecreate($width, $height);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $red = imagecolorallocate($image, 255, 0, 0);
    $blue = imagecolorallocate($image, 0, 0, 255);

    // Coordinates of Slovak cities
    $coordinates = [
        "Bratislava" => [48.1486, 17.1077],
        "Trnava" => [48.3774, 17.5888],
        "Nitra" => [48.3064, 18.0845],
        "Pezinok" => [48.2895, 17.2664],
        "Nové Zámky" => [47.9850, 18.1615],
        "Komárno" => [47.7634, 18.1204],
        "Levice" => [48.2169, 18.6070],
        "Zvolen" => [48.5723, 19.1386],
        "Banská Bystrica" => [48.7395, 19.1535],
        "Lučenec" => [48.3326, 19.6677],
        "Košice" => [48.7164, 21.2611],
        "Prešov" => [49.0030, 21.2394],
        "Poprad" => [49.0614, 20.2985],
        "Spišská Nová Ves" => [48.9444, 20.5619],
        "Žilina" => [49.2232, 18.7394],
        "Martin" => [49.0645, 18.9213],
        "Trenčín" => [48.8945, 18.0440],
        "Považská Bystrica" => [49.1195, 18.4310],
        "Prievidza" => [48.7745, 18.6274]
    ];

    // Bounders
    $lat_min = 47.7634; $lat_max = 49.2232;
    $lon_min = 17.1077; $lon_max = 21.2611;

    $positions = [];
    foreach ($coordinates as $city => [$lat, $lon]) {
        $x = (int)(($lon - $lon_min) / ($lon_max - $lon_min) * ($width - 2 * $margin)) + $margin;
        $y = (int)(($lat_max - $lat) / ($lat_max - $lat_min) * ($height - 2 * $margin)) + $margin;
        $positions[$city] = [$x, $y];
    }

    foreach ($graph as $from => $edges) {
        foreach ($edges as $to => $dist) {
            if ($from < $to) {
                $isPath = (in_array($from, $path) && in_array($to, $path) &&
                    abs(array_search($from, $path) - array_search($to, $path)) === 1);
                $color = $isPath ? $red : $black;

                [$x1, $y1] = $positions[$from];
                [$x2, $y2] = $positions[$to];
                imageline($image, $x1, $y1, $x2, $y2, $color);
            }
        }
    }

    foreach ($positions as $city => [$x, $y]) {
        imagefilledellipse($image, $x, $y, 18, 18, $blue);
        imagestring($image, 2, $x - strlen($city) * 3, $y - 25, $city, $black);
    }

    header("Content-type: image/png");
    imagepng($image);
    imagedestroy($image);
}

if (isset($_SESSION['graph']) && isset($_SESSION['path'])) {
    drawGraph($_SESSION['graph'], $_SESSION['path']);
}

?>
