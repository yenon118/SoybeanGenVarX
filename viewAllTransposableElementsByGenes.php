<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$gene_id_3 = $_GET['gene_id_3'];

if (is_string($gene_id_3)) {
    $temp_gene_arr = preg_split("/[;, \n]+/", $gene_id_3);
    $gene_arr = array();
    for ($i = 0; $i < count($temp_gene_arr); $i++) {
        if (!empty(trim($temp_gene_arr[$i]))) {
            array_push($gene_arr, trim($temp_gene_arr[$i]));
        }
    }
} elseif (is_array($gene_id_3)) {
    $temp_gene_arr = $gene_id_3;
    $gene_arr = array();
    for ($i = 0; $i < count($temp_gene_arr); $i++) {
        if (!empty(trim($temp_gene_arr[$i]))) {
            array_push($gene_arr, trim($temp_gene_arr[$i]));
        }
    }
} else {
    echo "<p>Please input correct gene IDs!!!</p>";
    exit(0);
}

?>

<!-- Back button -->
<a href="/SoybeanGenVarX/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query gene regions from database -->
<?php
$query_str = "SELECT Chromosome, Start, End, Strand, Name AS Gene_ID, Gene_Description FROM soykb.mViz_Soybean_GFF";
$query_str = $query_str . " WHERE (Name IN ('";
for ($i = 0; $i < count($gene_arr); $i++) {
    if ($i < (count($gene_arr)-1)){
        $query_str = $query_str . $gene_arr[$i] . "', '";
    } else {
        $query_str = $query_str . $gene_arr[$i];
    }
}
$query_str = $query_str . "'));";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);

?>

<!-- Render gene table -->
<?php
if (isset($gene_result_arr) && is_array($gene_result_arr) && !empty($gene_result_arr)) {
    // Make the gene table
    echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
    echo "<table style='text-align:center; border:3px solid #000;'>";

    // Table header
    echo "<tr>";
    foreach ($gene_result_arr[0] as $key => $value) {
        echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
    }
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($gene_result_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        foreach ($gene_result_arr[$j] as $key => $value) {
            echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<br />";
    echo "<br />";
} else {
    echo "<p>Genes could not be found in the database!!!</p>";
    exit(0);
}

?>

<!-- Query and render transposable element table -->
<?php
if(isset($gene_result_arr) && is_array($gene_result_arr) && !empty($gene_result_arr)) {

    for ($i = 0; $i < count($gene_result_arr); $i++) {

        // Construct transposable element sql query string
        $query_str = "SELECT GFF.Chromosome, GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Name AS Gene_ID, ";
        // $query_str = $query_str . "TE.Divergence_Percentage AS TE_Divergence_Percentage, TE.Deletion_Percentage AS TE_Deletion_Percentage, TE.Insertion_Percentage AS TE_Insertion_Percentage, ";
        $query_str = $query_str . "TE.Start AS TE_Start, TE.End AS TE_End, TE.Length AS TE_Length, TE.Element AS TE_Element, TE.Family AS TE_Family ";
        // $query_str = $query_str . "TE.Position_Repeat_Start AS TE_Position_Repeat_Start, TE.Position_Repeat_End AS TE_Position_Repeat_End ";
        $query_str = $query_str . "FROM soykb.mViz_Soybean_GFF AS GFF ";
        $query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_TE AS TE ";
        $query_str = $query_str . "ON (GFF.Chromosome = TE.Chromosome) AND (((TE.Start >= GFF.Start) AND (TE.Start <= GFF.End)) OR ((TE.End >= GFF.Start) AND (TE.End <= GFF.End))) ";
        $query_str = $query_str . "WHERE (GFF.Chromosome = '" . $gene_result_arr[$i]["Chromosome"] . "') AND (TE.Chromosome = '" . $gene_result_arr[$i]["Chromosome"] . "') ";
        $query_str = $query_str . "AND (GFF.Start = " . $gene_result_arr[$i]["Start"] . ") AND (GFF.End = " . $gene_result_arr[$i]["End"] . ");";

        // Query transposable element data
        $stmt = $PDO->prepare($query_str);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $te_result_arr = pdoResultFilter($result);

        if(isset($te_result_arr) && is_array($te_result_arr) && !empty($te_result_arr)) {
            // Render transposable element table
            echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
            echo "<table style='text-align:center; border:3px solid #000;'>";

            // Table header
            echo "<tr>";
            foreach ($te_result_arr[0] as $key => $value) {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            }
            echo "<th style=\"border:1px solid black; min-width:80px;\">Sequence</th>";
            echo "<th style=\"border:1px solid black; min-width:80px;\">Reference Sequence</th>";
            echo "</tr>";

            // Table body
            for ($j = 0; $j < count($te_result_arr); $j++) {
                $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

                echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
                foreach ($te_result_arr[$j] as $key => $value) {
                    echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
                }

                if (file_exists("assets/TE_sequences/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Family']) . "/" . $te_result_arr[$j]['Chromosome'] . "_" . $te_result_arr[$j]['TE_Start'] . "_" . $te_result_arr[$j]['TE_End'] . "_" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Element']) . ".fasta")) {
                    echo "<td style=\"border:1px solid black; min-width:80px;\"><a href=\"assets/TE_sequences/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Family']) . "/" . $te_result_arr[$j]['Chromosome'] . "_" . $te_result_arr[$j]['TE_Start'] . "_" . $te_result_arr[$j]['TE_End'] . "_" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Element']) . ".fasta\" target=\"_blank\"><button>Download Sequence</button></a></td>";
                } else {
                    echo "<td style=\"border:1px solid black; min-width:80px;\">-</td>";
                }

                $te_family = preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Family']);

                if (file_exists("assets/TE_reference_sequences/" . $te_family . "/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Element']) . ".fasta")) {
                    echo "<td style=\"border:1px solid black; min-width:80px;\"><a href=\"assets/TE_reference_sequences/" . $te_family . "/" . preg_replace("/[^[:alnum:][:space:]]/u", '_', $te_result_arr[$j]['TE_Element']) . ".fasta\" target=\"_blank\"><button>Download Reference Sequence</button></a></td>";
                } else {
                    echo "<td style=\"border:1px solid black; min-width:80px;\">-</td>";
                }

                echo "</tr>";
            }

            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>No transposable element found for " . $gene_result_arr[$i]["Gene_ID"] . "</p>";
        }

        echo "<br /><br />";
    }

} else {
    echo "<p>Genes could not be found in the database!!!</p>";
    exit(0);
}

?>

<?php include '../footer.php'; ?>
