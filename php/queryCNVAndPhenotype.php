<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$position_start = $_GET['Start'];
$position_end = $_GET['End'];
$data_option = $_GET['Data_Option'];
$cn = $_GET['CN'];
$phenotype = $_GET['Phenotype'];

if (is_string($cn)) {
    $cn_array = preg_split("/[;, \n]+/", $cn);
    for ($i = 0; $i < count($cn_array); $i++) {
        $cn_array[$i] = trim($cn_array[$i]);
    }
} elseif (is_array($cn)) {
    $cn_array = $cn;
    for ($i = 0; $i < count($cn_array); $i++) {
        $cn_array[$i] = trim($cn_array[$i]);
    }
}

if (is_string($phenotype)) {
    $phenotype_array = preg_split("/[;, \n]+/", $phenotype);
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $phenotype_array[$i] = trim($phenotype_array[$i]);
    }
} elseif (is_array($phenotype)) {
    $phenotype_array = $phenotype;
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $phenotype_array[$i] = trim($phenotype_array[$i]);
    }
}

// Construct query string
$query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, AM.SoyKB_Accession AS Accession, AM.GRIN_Accession, AM.Improvement_Status, AM.Classification, CNV.CN, ";
$query_str = $query_str . "CASE CNV.CN ";
$query_str = $query_str . "WHEN 'CN0' THEN 'Loss' ";
$query_str = $query_str . "WHEN 'CN1' THEN 'Loss' ";
$query_str = $query_str . "WHEN 'CN3' THEN 'Gain' ";
$query_str = $query_str . "WHEN 'CN4' THEN 'Gain' ";
$query_str = $query_str . "WHEN 'CN5' THEN 'Gain' ";
$query_str = $query_str . "WHEN 'CN6' THEN 'Gain' ";
$query_str = $query_str . "WHEN 'CN7' THEN 'Gain' ";
$query_str = $query_str . "WHEN 'CN8' THEN 'Gain' ";
$query_str = $query_str . "ELSE 'Normal' ";
$query_str = $query_str . "END as Status ";
for ($i = 0; $i < count($phenotype_array); $i++) {
    $query_str = $query_str . ", G." . $phenotype_array[$i] . " ";
}
$query_str = $query_str . "FROM ";
if ($data_option == "Individual_Hits") {
    $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
} else if ($data_option == "Consensus_Regions") {
    $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
}
$query_str = $query_str . "AS CNV ";
$query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
$query_str = $query_str . "ON BINARY CNV.Accession = AM.Accession ";
$query_str = $query_str . "LEFT JOIN soykb.germplasm AS G ";
$query_str = $query_str . "ON BINARY AM.GRIN_Accession = G.ACNO ";
$query_str = $query_str . "WHERE (CNV.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "AND (CNV.Start BETWEEN " . $position_start . " AND " . $position_end . ") ";
$query_str = $query_str . "AND (CNV.End BETWEEN " . $position_start . " AND " . $position_end . ") ";
if (count($cn_array) > 0) {
    $query_str = $query_str . "AND (CNV.CN IN ('";
    for ($i = 0; $i < count($cn_array); $i++) {
        if($i < (count($cn_array)-1)){
            $query_str = $query_str . trim($cn_array[$i]) . "', '";
        } elseif ($i == (count($cn_array)-1)) {
            $query_str = $query_str . trim($cn_array[$i]);
        }
    }
    $query_str = $query_str . "')) ";
}
$query_str = $query_str . "ORDER BY CNV.CN, CNV.Chromosome, CNV.Start, CNV.End, AM.SoyKB_Accession; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);


?>