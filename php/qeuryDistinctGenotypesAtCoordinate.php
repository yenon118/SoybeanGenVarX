<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$position = $_GET['Position'];

// Construct query string
$query_str = "SELECT DISTINCT G.Genotype FROM soykb.mViz_Soy1066_" . $chromosome . "_genotype_data AS G ";
$query_str = $query_str . "WHERE ((G.Chromosome = '" . $chromosome . "') AND (G.Position = " . $position . ")); ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);
?>
