<?php
$array = [
    "foo" => "bar",
    "cuck" => "sandwhich",
];
header('Content-Type: application/json');
echo json_encode($array);
?>