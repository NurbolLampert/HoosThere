<?php
header("Content-Type: application/json");

$rows = max(1, intval($_GET["rows"] ?? 0));
$cols = max(1, intval($_GET["cols"] ?? 0));

$need = min(10, $rows * $cols);
$on   = [];

while (count($on) < $need) {
    $r = rand(0, $rows - 1);
    $c = rand(0, $cols - 1);
    $on["r{$r}c{$c}"] = true;
}

echo json_encode([
    "rows"   => $rows,
    "cols"   => $cols,
    "lights" => array_keys($on)
]);

