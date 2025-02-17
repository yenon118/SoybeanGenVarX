<?php

include '../../config.php';
include 'pdoResultFilter.php';


$chromosome = $_GET['Chromosome'];
$position_start = $_GET['Start'];
$position_end = $_GET['End'];
$data_option = $_GET['Data_Option'];


$chromosome = clean_malicious_input($chromosome);
$chromosome = preg_replace('/\s+/', '', $chromosome);

$position_start = clean_malicious_input($position_start);
$position_start = preg_replace('/\s+/', '', $position_start);

$position_end = clean_malicious_input($position_end);
$position_end = preg_replace('/\s+/', '', $position_end);

$data_option = clean_malicious_input($data_option);
$data_option = preg_replace('/\s+/', '', $data_option);

$position_start = abs(intval(preg_replace("/[^0-9.]/", "", $position_start)));
$position_end = abs(intval(preg_replace("/[^0-9.]/", "", $position_end)));


$db = "soykb";
$accession_mapping_table_name = "mViz_Soybean_Accession_Mapping";
$phenotype_table_name = "mViz_Soybean_Phenotype_Data";

if (isset($data_option)) {
    if ($data_option == "Individual_Hits") {
        $cnv_table_name = "mViz_Soybean_CNVS ";
    } else if ($data_option == "Consensus_Regions") {
        $cnv_table_name = "mViz_Soybean_CNVR ";
    }
}


// Construct query string
$query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, ";
$query_str = $query_str . "AM.SoyKB_Accession AS Accession, AM.Improvement_Status, AM.Classification, ";
$query_str = $query_str . "CNV.CN, CNV.Status ";
$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT C.Chromosome, C.Start, C.End, C.Width, C.Strand, C.Accession, C.CN, ";
$query_str = $query_str . "    CASE C.CN ";
$query_str = $query_str . "    WHEN 'CN0' THEN 'Loss' ";
$query_str = $query_str . "    WHEN 'CN1' THEN 'Loss' ";
$query_str = $query_str . "    WHEN 'CN3' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN4' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN5' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN6' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN7' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN8' THEN 'Gain' ";
$query_str = $query_str . "    ELSE 'Normal' ";
$query_str = $query_str . "    END as Status ";
$query_str = $query_str . "    FROM " . $db . "." . $cnv_table_name . " AS C ";
$query_str = $query_str . "    WHERE (C.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (C.Start BETWEEN " . $position_start . " AND " . $position_end . ") ";
$query_str = $query_str . "    AND (C.End BETWEEN " . $position_start . " AND " . $position_end . ") ";
$query_str = $query_str . ") AS CNV ";
$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table_name . " AS AM ";
$query_str = $query_str . "ON CAST(CNV.Accession AS BINARY) = CAST(AM.Accession AS BINARY) ";
$query_str = $query_str . "ORDER BY CNV.CN, CNV.Chromosome, CNV.Start, CNV.End, AM.SoyKB_Accession; ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
