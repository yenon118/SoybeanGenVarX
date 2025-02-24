<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.plot.ly/plotly-3.0.0.min.js" charset="utf-8"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome = $_GET['chromosome_1'];
$position_start = $_GET['position_start_1'];
$position_end = $_GET['position_end_1'];
$data_option = $_GET['cnv_data_option_1'];

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

?>

<h3>Figure:</h3>
<div>
    <div id="cn_and_improvement_status_summary_plot_div">Loading CN and improvement status summary plot...</div>
    <div id="cn_and_improvement_status_summary_table_div">Loading CN and improvement status summary table...</div>
    <div>
        <hr>
        <h3>Full Table:</h3>
        <div id="cn_and_improvement_status_table_div">Loading CN and improvement status table...</div>

    </div>
</div>

<?php include '../footer.php'; ?>


<script type="text/javascript" language="javascript" src="./js/viewCNVAndImprovementStatus.js"></script>

<script type="text/javascript" language="javascript">
    var chromosome = <?php if (isset($chromosome)) {
                            echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);
                        } else {
                            echo "";
                        } ?>;
    var position_start = <?php if (isset($position_start)) {
                                echo json_encode($position_start, JSON_INVALID_UTF8_IGNORE);
                            } else {
                                echo "";
                            } ?>;
    var position_end = <?php if (isset($position_end)) {
                            echo json_encode($position_end, JSON_INVALID_UTF8_IGNORE);
                        } else {
                            echo "";
                        } ?>;
    var data_option = <?php if (isset($data_option)) {
                            echo json_encode($data_option, JSON_INVALID_UTF8_IGNORE);
                        } else {
                            echo "";
                        } ?>;

    if (chromosome && position_start && position_end && data_option) {
        $.ajax({
            url: './php/queryCNVAndImprovementStatus.php',
            type: 'GET',
            contentType: 'application/json',
            data: {
                Chromosome: chromosome,
                Start: position_start,
                End: position_end,
                Data_Option: data_option
            },
            success: function(response) {
                res = JSON.parse(response);

                if (res) {
                    document.getElementById("cn_and_improvement_status_summary_plot_div").style.minHeight = "800px";

                    // Render data
                    document.getElementById('cn_and_improvement_status_table_div').innerText = "";
                    document.getElementById('cn_and_improvement_status_table_div').innerHTML = "";
                    document.getElementById('cn_and_improvement_status_table_div').appendChild(
                        constructInfoTable(JSON.parse(JSON.stringify(res['data'])))
                    );
                    document.getElementById('cn_and_improvement_status_table_div').style.maxHeight = '1000px';
                    document.getElementById('cn_and_improvement_status_table_div').style.display = 'inline-block';
                    document.getElementById('cn_and_improvement_status_table_div').style.overflow = 'scroll';


                    // Summarize data
                    var result_dict = summarizeQueriedData(
                        JSON.parse(JSON.stringify(res['data'])),
                        'Improvement_Status',
                        'CN'
                    );

                    var result_arr = result_dict['Data'];
                    var summary_array = result_dict['Summary'];

                    for (let i = 0; i < summary_array.length; i++) {
                        if (summary_array[i].hasOwnProperty('Number_of_Accession_with_Phenotype')) {
                            delete summary_array[i]['Number_of_Accession_with_Phenotype']
                        }
                        if (summary_array[i].hasOwnProperty('Number_of_Accession_without_Phenotype')) {
                            delete summary_array[i]['Number_of_Accession_without_Phenotype']
                        }
                    }

                    // Make plot
                    var cnAndImprovementStatusData = collectDataForFigure(result_arr, 'Improvement_Status', 'CN');
                    plotFigure(cnAndImprovementStatusData, 'CN', 'Improvement_Status_Summary', 'cn_and_improvement_status_summary_plot_div');

                    // Render data
                    document.getElementById('cn_and_improvement_status_summary_table_div').innerText = "";
                    document.getElementById('cn_and_improvement_status_summary_table_div').innerHTML = "";
                    document.getElementById('cn_and_improvement_status_summary_table_div').appendChild(
                        constructInfoTable(summary_array)
                    );
                    document.getElementById('cn_and_improvement_status_summary_table_div').style.overflow = 'scroll';
                }
            },
            error: function(xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                document.getElementById('cn_and_improvement_status_summary_plot_div').innerText = "";
                document.getElementById('cn_and_improvement_status_summary_plot_div').innerHTML = "";
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "CN and improvement status distribution summary figure is not available due to lack of data!!!";
                document.getElementById('cn_and_improvement_status_summary_plot_div').appendChild(p_tag);
                document.getElementById('cn_and_improvement_status_summary_table_div').innerText = "";
                document.getElementById('cn_and_improvement_status_summary_table_div').innerHTML = "";
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "CN and improvement status distribution summary figure is not available due to lack of data!!!";
                document.getElementById('cn_and_improvement_status_summary_table_div').appendChild(p_tag);
                document.getElementById('cn_and_improvement_status_table_div').innerText = "";
                document.getElementById('cn_and_improvement_status_table_div').innerHTML = "";
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "CN and improvement status distribution table is not available due to lack of data!!!";
                document.getElementById('cn_and_improvement_status_table_div').appendChild(p_tag);
            }
        });
    } else {
        document.getElementById('cn_and_improvement_status_summary_plot_div').innerText = "";
        document.getElementById('cn_and_improvement_status_summary_plot_div').innerHTML = "";
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "CN and improvement status distribution summary figure is not available due to lack of data!!!";
        document.getElementById('cn_and_improvement_status_summary_plot_div').appendChild(p_tag);
        document.getElementById('cn_and_improvement_status_summary_table_div').innerText = "";
        document.getElementById('cn_and_improvement_status_summary_table_div').innerHTML = "";
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "CN and improvement status distribution summary figure is not available due to lack of data!!!";
        document.getElementById('cn_and_improvement_status_summary_table_div').appendChild(p_tag);
        document.getElementById('cn_and_improvement_status_table_div').innerText = "";
        document.getElementById('cn_and_improvement_status_table_div').innerHTML = "";
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "CN and improvement status distribution table is not available due to lack of data!!!";
        document.getElementById('cn_and_improvement_status_table_div').appendChild(p_tag);
    }
</script>