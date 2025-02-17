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
$element_3 = $_GET['element_3'];

$element_3 = clean_malicious_input($element_3);

if (is_string($element_3)) {
    $temp_element_arr = preg_split("/[;, \n]+/", trim($element_3));
    $element_arr = array();
    for ($i = 0; $i < count($temp_element_arr); $i++) {
        if (!empty(trim($temp_element_arr[$i]))) {
            array_push($element_arr, trim($temp_element_arr[$i]));
        }
    }
} elseif (is_array($element_3)) {
    $temp_element_arr = $element_3;
    $element_arr = array();
    for ($i = 0; $i < count($temp_element_arr); $i++) {
        if (!empty(trim($temp_element_arr[$i]))) {
            array_push($element_arr, trim($temp_element_arr[$i]));
        }
    }
} else {
    exit(0);
}

?>

<!-- Back button -->
<a href="/SoybeanGenVarX/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query data from database -->
<?php

$query_str = "SELECT TE.Chromosome, TE.Start AS TE_Start, TE.End AS TE_End, TE.Length AS TE_Length, TE.Element AS TE_Element, TE.Family AS TE_Family, ";
$query_str = $query_str . "GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Name AS Gene_ID ";
$query_str = $query_str . "FROM soykb.mViz_Soybean_TE AS TE ";
$query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_GFF AS GFF ";
$query_str = $query_str . "ON (TE.Chromosome = GFF.Chromosome) AND ((TE.Start >= GFF.Start AND TE.Start <= GFF.End) OR (TE.End >= GFF.Start AND TE.End <= GFF.End)) ";
$query_str = $query_str . "WHERE (TE.Element IN ('";
for ($i = 0; $i < count($element_arr); $i++) {
    if ($i < (count($element_arr) - 1)) {
        $query_str = $query_str . trim($element_arr[$i]) . "', '";
    } elseif ($i == (count($element_arr) - 1)) {
        $query_str = $query_str . trim($element_arr[$i]);
    }
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "ORDER BY TE.Chromosome, TE.Element, TE.Start, TE.End; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $result_arr = pdoResultFilter($result);
} else {
    echo "No data found!!!";
}

?>


<!-- Render the table -->
<?php

if (count($result_arr) > 0) {
    echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
    echo "<table style='text-align:center;'>";

    // Table header
    echo "<tr>";
    foreach ($result_arr[0] as $key => $value) {
        echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
    }
    echo "<th style=\"border:1px solid black;\">Sequence</th>";
    echo "<th style=\"border:1px solid black;\">Reference Sequence</th>";
    echo "</tr>";

    // Table body
    for ($i = 0; $i < count($result_arr); $i++) {
        $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        foreach ($result_arr[$i] as $key => $value) {
            echo "<td style=\"border:1px solid black;min-width:120px;\">" . $value . "</td>";
        }

        if (file_exists("assets/TE_sequences/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Family']) . "/" . $result_arr[$i]['Chromosome'] . "_" . $result_arr[$i]['TE_Start'] . "_" . $result_arr[$i]['TE_End'] . "_" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Element']) . ".fasta")) {
            echo "<td style=\"border:1px solid black; min-width:80px;\"><a href=\"assets/TE_sequences/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Family']) . "/" . $result_arr[$i]['Chromosome'] . "_" . $result_arr[$i]['TE_Start'] . "_" . $result_arr[$i]['TE_End'] . "_" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Element']) . ".fasta\" target=\"_blank\"><button>Download Sequence</button></a></td>";
        } else {
            echo "<td style=\"border:1px solid black; min-width:80px;\">-</td>";
        }

        $te_family = preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Family']);

        if (file_exists("assets/TE_reference_sequences/" . $te_family . "/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Element']) . ".fasta")) {
            echo "<td style=\"border:1px solid black; min-width:80px;\"><a href=\"assets/TE_reference_sequences/" . $te_family . "/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $result_arr[$i]['TE_Element']) . ".fasta\" target=\"_blank\"><button>Download Reference Sequence</button></a></td>";
        } else {
            echo "<td style=\"border:1px solid black; min-width:80px;\">-</td>";
        }

        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
}

?>


<?php include '../footer.php'; ?>