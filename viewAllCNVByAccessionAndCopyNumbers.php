<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$accession_2 = $_GET['accession_2'];
$copy_number_2 = $_GET['copy_number_2'];
$cnv_data_option_2 = $_GET['cnv_data_option_2'];

$accession_2 = clean_malicious_input($accession_2);
$accession_2 = preg_replace('/\s+/', '', $accession_2);

$copy_number_2 = clean_malicious_input($copy_number_2);

$cnv_data_option_2 = clean_malicious_input($cnv_data_option_2);
$cnv_data_option_2 = preg_replace('/\s+/', '', $cnv_data_option_2);

if (is_string($copy_number_2)) {
    $temp_copy_number_arr = preg_split("/[;, \n]+/", trim($copy_number_2));
    $copy_number_arr = array();
    for ($i = 0; $i < count($temp_copy_number_arr); $i++) {
        if (!empty(trim($temp_copy_number_arr[$i]))) {
            array_push($copy_number_arr, trim($temp_copy_number_arr[$i]));
        }
    }
} elseif (is_array($copy_number_2)) {
    $temp_copy_number_arr = $copy_number_2;
    $copy_number_arr = array();
    for ($i = 0; $i < count($temp_copy_number_arr); $i++) {
        if (!empty(trim($temp_copy_number_arr[$i]))) {
            array_push($copy_number_arr, trim($temp_copy_number_arr[$i]));
        }
    }
} else {
    echo "Invalid query!!!";
    exit(0);
}

?>

<!-- Back button -->
<a href="/SoybeanGenVarX/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query data from database -->
<?php

// Check GRIN accession mapping
$query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, AM.SoyKB_Accession AS Accession, CNV.CN ";
$query_str = $query_str . "FROM ";
if ($cnv_data_option_2 == "Individual_Hits") {
    $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
} else if ($cnv_data_option_2 == "Consensus_Regions") {
    $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
}
$query_str = $query_str . "AS CNV ";
$query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
$query_str = $query_str . "ON CAST(CNV.Accession AS BINARY) = CAST(AM.Accession AS BINARY) ";
$query_str = $query_str . "WHERE ((CAST(AM.SoyKB_Accession AS BINARY) = CAST('" . $accession_2 . "' AS BINARY)) OR (CAST(AM.GRIN_Accession AS BINARY) = CAST('" . $accession_2 . "' AS BINARY))) AND (CNV.CN IN ('";
for ($i = 0; $i < count($copy_number_arr); $i++) {
    if ($i < (count($copy_number_arr) - 1)) {
        $query_str = $query_str . trim($copy_number_arr[$i]) . "', '";
    } elseif ($i == (count($copy_number_arr) - 1)) {
        $query_str = $query_str . trim($copy_number_arr[$i]);
    }
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "ORDER BY CNV.CN, CNV.Chromosome, CNV.Start, CNV.End; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $result_arr = pdoResultFilter($result);
}

?>


<!-- Render the table -->
<?php
echo "<h3>CNV regions and CNs of accession " . $accession_2 . ": </h3>";
if (isset($result_arr) && is_array($result_arr) && !empty($result_arr) && count($result_arr) > 0) {
    echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
    echo "<table style='text-align:center;'>";

    // Table header
    echo "<tr>";
    foreach ($result_arr[0] as $key => $value) {
        echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
    }
    echo "</tr>";

    // Table body
    for ($i = 0; $i < count($result_arr); $i++) {
        $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        foreach ($result_arr[$i] as $key => $value) {
            echo "<td style=\"border:1px solid black;min-width:120px;\">" . $value . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
} else {
    echo "<p>No CNV ragion and CN of accession " . $accession_2 . "!!!</p>";
}
?>


<?php include '../footer.php'; ?>