<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$motif = $_GET['motif'];
$gene = $_GET['gene'];
$chromosome = $_GET['chromosome'];
$motif_start = $_GET['motif_start'];
$motif_end = $_GET['motif_end'];
$gene_binding_sequence = $_GET['gene_binding_sequence'];
$upstream_length = 2000;

if (is_string($motif_start)) {
    $motif_start = intval(trim($motif_start));
} elseif (is_int($motif_start)) {
    $motif_start = $motif_start;
} elseif (is_float($motif_start)) {
    $motif_start = intval($motif_start);
}

if (is_string($motif_end)) {
    $motif_end = intval(trim($motif_end));
} elseif (is_int($motif_end)) {
    $motif_end = $motif_end;
} elseif (is_float($motif_end)) {
    $motif_end = intval($motif_end);
}
?>

<?php


// Get binding TF
// $query_str = "
// SELECT M.Motif AS Binding_TF, TF.TF_Family,
// MS.Chromosome AS Binding_Chromosome, MS.Start AS Binding_Start, MS.End AS Binding_End, MS.Sequence AS Gene_Binding_Sequence,
// M.Gene, GFF.Chromosome, GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Gene_Description,
// GROUP_CONCAT(GD.Position ORDER BY GD.Position ASC SEPARATOR ', ') AS Variant_Position
// FROM (
//     SELECT Motif, Gene FROM mViz_Soybean_Motif
//     WHERE ((Motif = '" . $motif . "') AND (Gene = '" . $gene . "'))
// ) AS M
// LEFT JOIN mViz_Soybean_TF AS TF
// ON M.Motif = TF.TF
// LEFT JOIN mViz_Soybean_" . $chromosome . "_Motif_Sequence AS MS
// ON M.Motif = MS.Name
// LEFT JOIN (
//     SELECT ID, Name, Chromosome, Start, End, Strand, Gene_Description,
//     CASE Strand
//         WHEN '+' THEN Start-1-" . $upstream_length . "
//         ELSE End+1
//     END AS Promoter_Start,
//     CASE Strand
//         WHEN '+' THEN Start-1
//         ELSE End+1+" . $upstream_length . "
//     END AS Promoter_End
//     FROM mViz_Soybean_GFF
// ) AS GFF
// ON ((M.Gene = GFF.ID) AND (MS.Chromosome = GFF.Chromosome) AND (MS.Start BETWEEN GFF.Promoter_Start AND GFF.Promoter_End))
// LEFT JOIN (
//     SELECT DISTINCT Chromosome, Position FROM mViz_Soy1066_" . $chromosome . "_genotype_data
// ) AS GD
// ON ((MS.Chromosome = GD.Chromosome) AND (GD.Position BETWEEN MS.Start AND MS.End))
// WHERE ((GFF.Chromosome = '" . $chromosome . "') AND (MS.Start = " . $motif_start . ") AND (MS.End = " . $motif_end . "))
// GROUP BY M.Motif, TF.TF_Family, MS.Chromosome, MS.Start, MS.End, MS.Sequence, M.Gene, GFF.Chromosome, GFF.Start, GFF.End, GFF.Strand, GFF.Gene_Description
// ORDER BY MS.Chromosome, MS.Start
// LIMIT 1;
// ";

// Get binding TF (Optimized MySQL query string)
// $query_str = "
// SELECT M.Motif AS Binding_TF, TF.TF_Family,
// MS.Chromosome AS Binding_Chromosome, MS.Start AS Binding_Start, MS.End AS Binding_End, MS.Sequence AS Gene_Binding_Sequence,
// M.Gene, GFF.Chromosome, GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Gene_Description,
// GROUP_CONCAT(DISTINCT GD.Position ORDER BY GD.Position ASC SEPARATOR ', ') AS Variant_Position
// FROM (
//     SELECT Motif, Gene FROM mViz_Soybean_Motif
//     WHERE ((Motif = '" . $motif . "') AND (Gene = '" . $gene . "'))
// ) AS M
// LEFT JOIN mViz_Soybean_TF AS TF
// ON M.Motif = TF.TF
// LEFT JOIN mViz_Soybean_" . $chromosome . "_Motif_Sequence AS MS
// ON M.Motif = MS.Name
// LEFT JOIN (
//     SELECT ID, Name, Chromosome, Start, End, Strand, Gene_Description,
//     CASE Strand
//         WHEN '+' THEN Start-1-" . $upstream_length . "
//         ELSE End+1
//     END AS Promoter_Start,
//     CASE Strand
//         WHEN '+' THEN Start-1
//         ELSE End+1+" . $upstream_length . "
//     END AS Promoter_End
//     FROM mViz_Soybean_GFF
// ) AS GFF
// ON ((M.Gene = GFF.ID) AND (MS.Chromosome = GFF.Chromosome) AND (MS.Start BETWEEN GFF.Promoter_Start AND GFF.Promoter_End))
// LEFT JOIN mViz_Soy1066_" . $chromosome . "_genotype_data AS GD
// ON ((MS.Chromosome = GD.Chromosome) AND (GD.Position BETWEEN MS.Start AND MS.End))
// WHERE ((GFF.Chromosome = '" . $chromosome . "') AND (MS.Start = " . $motif_start . ") AND (MS.End = " . $motif_end . "))
// GROUP BY M.Motif, TF.TF_Family, MS.Chromosome, MS.Start, MS.End, MS.Sequence, M.Gene, GFF.Chromosome, GFF.Start, GFF.End, GFF.Strand, GFF.Gene_Description
// ORDER BY MS.Chromosome, MS.Start
// LIMIT 1;
// ";

// Get binding TF (Optimized MySQL query string)
$query_str = "
SELECT M.Motif AS Binding_TF, TF.TF_Family,
MS.Chromosome AS Binding_Chromosome, MS.Start AS Binding_Start, MS.End AS Binding_End, MS.Sequence AS Gene_Binding_Sequence,
M.Gene, GFF.Chromosome, GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Gene_Description
FROM (
    SELECT Motif, Gene FROM mViz_Soybean_Motif
    WHERE ((Motif = '" . $motif . "') AND (Gene = '" . $gene . "'))
) AS M
LEFT JOIN mViz_Soybean_TF AS TF
ON M.Motif = TF.TF
LEFT JOIN mViz_Soybean_" . $chromosome . "_Motif_Sequence AS MS
ON M.Motif = MS.Name
LEFT JOIN (
    SELECT ID, Name, Chromosome, Start, End, Strand, Gene_Description,
    CASE Strand
        WHEN '+' THEN Start-1-" . $upstream_length . "
        ELSE End+1
    END AS Promoter_Start,
    CASE Strand
        WHEN '+' THEN Start-1
        ELSE End+1+" . $upstream_length . "
    END AS Promoter_End
    FROM mViz_Soybean_GFF
) AS GFF
ON ((M.Gene = GFF.ID) AND (MS.Chromosome = GFF.Chromosome) AND (MS.Start BETWEEN GFF.Promoter_Start AND GFF.Promoter_End))
WHERE ((GFF.Chromosome = '" . $chromosome . "') AND (MS.Start = " . $motif_start . ") AND (MS.End = " . $motif_end . "))
ORDER BY MS.Chromosome, MS.Start
LIMIT 1;
";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$motif_result_arr = pdoResultFilter($result);

// Div tags for selected motif
echo "<div id=\"" . $gene . "_b\" style='width:auto; height:auto; overflow:visible; max-height:1000px;'></div>";

if (isset($motif_result_arr) && !empty($motif_result_arr)){
    echo "<br />";
    echo "<br />";
    echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
    echo "<table style='text-align:center; border:3px solid #000;'>";

    // Table header
    echo "<tr>";
    foreach ($motif_result_arr[0] as $key => $value) {
        echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
    }
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($motif_result_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        foreach ($motif_result_arr[$j] as $key => $value) {
            echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<br />";
    echo "<br />";
}

// Div tags for selected weblogo and motif sequence table
echo "<div id=\"" . $gene . "_weblogo\" style='width:auto; height:auto; overflow:visible; max-height:1000px;'></div>";
echo "<div id=\"" . $gene . "_detail_table\" style='width:auto; height:auto; overflow:visible; max-height:1000px;'></div>";
?>

<script type="text/javascript" language="javascript" src="./js/getMotifWeblogo.js"></script>

<script type="text/javascript" language="javascript">
    let motif = <?php echo "'" . $motif . "'"; ?>;
    let gene = <?php echo "'" . $gene . "'"; ?>;
    let chromosome = <?php echo "'" . $chromosome . "'"; ?>;
    let motif_start = <?php echo $motif_start; ?>;
    let motif_end = <?php echo $motif_end; ?>;
    let motif_sequence = <?php echo "'" . $gene_binding_sequence . "'"; ?>;
    getMotifWeblogo(motif, gene, chromosome, motif_start, motif_end, motif_sequence)
</script>

<?php include '../footer.php'; ?>
