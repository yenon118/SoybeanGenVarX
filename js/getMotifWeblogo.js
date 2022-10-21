
function getMotifWeblogo(motif, gene, chromosome, motif_start, motif_end, motif_sequence) {

    // Clear data appended to the div tags, if there is any
    if (document.getElementById(gene+"_b").innerHTML) {
        document.getElementById(gene+"_b").innerHTML = null;
    }
    if (document.getElementById(gene+"_weblogo").innerHTML) {
        document.getElementById(gene+"_weblogo").innerHTML = null;
    }
    if (document.getElementById(gene+"_detail_table").innerHTML) {
        document.getElementById(gene+"_detail_table").innerHTML = null;
    }

    // Create b tag for motif
    var motif_b = document.createElement("b");
    motif_b.innerHTML = "Selected TF: " + motif;
    document.getElementById(gene+"_b").appendChild(motif_b);

    // Load Ceqlogo / Weblogo image
    var weblogo = document.createElement("img");
    weblogo.setAttribute("src", "assets/motif_weblogos/"+motif+".png");
    document.getElementById(gene+"_weblogo").appendChild(weblogo);

    // Create motif sequence table
    let detail_table = document.createElement("table");
    detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
    let detail_tr_index = document.createElement("tr");
    let detail_tr_position = document.createElement("tr");
    let detail_tr_nucleotide = document.createElement("tr");
    let detail_tr_genotype_count = document.createElement("tr");

    for (let i = 0; i < (motif_end-motif_start+1); i++) {
        var detail_th = document.createElement("th");
        detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
        detail_th.innerHTML = Number(i)+1;
        detail_tr_index.appendChild(detail_th);

        var detail_th = document.createElement("th");
        detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
        detail_th.id = "position_"+(Number(motif_start)+Number(i)).toString();
        detail_th.innerHTML = Number(motif_start)+Number(i);
        detail_tr_position.appendChild(detail_th);

        var detail_td = document.createElement("td");
        detail_td.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
        detail_td.innerHTML = motif_sequence[i];
        detail_tr_nucleotide.appendChild(detail_td);

        var detail_td = document.createElement("td");
        detail_td.id = "genotype_count_"+(Number(motif_start)+Number(i)).toString();
        detail_tr_genotype_count.appendChild(detail_td);
    }

    detail_table.appendChild(detail_tr_index);
    detail_table.appendChild(detail_tr_position);
    detail_table.appendChild(detail_tr_nucleotide);
    detail_table.appendChild(detail_tr_genotype_count);

    document.getElementById(gene+"_detail_table").appendChild(detail_table);

    $.ajax({
        url: 'php/queryGenotypeCount.php',
        type: 'GET',
        contentType: 'application/json',
        data: {
            Chromosome: chromosome,
            Start: motif_start,
            End: motif_end
        },
        success: function (response) {
            let res = JSON.parse(response);
            res = res.data;

            for (let i = 0; i < res.length; i++) {
                var genotype_count_element = document.getElementById("genotype_count_"+(res[i]['Position']).toString());

                if (genotype_count_element.innerHTML === null || genotype_count_element.innerHTML == '') {
                    // Construct table with header
                    genotype_count_element.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
                    var genotype_count_table = document.createElement("table");
                    genotype_count_table.id = "genotype_count_"+(res[i]['Position']).toString()+"_table";
                    var genotype_count_tr_index = document.createElement("tr");
                    var detail_th = document.createElement("th");
                    detail_th.setAttribute("style", "border:1px solid black; min-width:20px; height:18.5px;");
                    detail_th.innerHTML = "Genotype";
                    genotype_count_tr_index.appendChild(detail_th);
                    var detail_th = document.createElement("th");
                    detail_th.setAttribute("style", "border:1px solid black; min-width:20px; height:18.5px;");
                    detail_th.innerHTML = "Category";
                    genotype_count_tr_index.appendChild(detail_th);
                    var detail_th = document.createElement("th");
                    detail_th.setAttribute("style", "border:1px solid black; min-width:20px; height:18.5px;");
                    detail_th.innerHTML = "Count";
                    genotype_count_tr_index.appendChild(detail_th);
                    genotype_count_table.appendChild(genotype_count_tr_index);
                    genotype_count_element.appendChild(genotype_count_table);
                }
            }

            for (let i = 0; i < res.length; i++) {
                var genotype_count_element_table = document.getElementById("genotype_count_"+(res[i]['Position']).toString()+"_table");

                var genotype_count_tr_index = document.createElement("tr");
                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:20px; height:18.5px;");
                detail_td.innerHTML = res[i]['Genotype'];
                genotype_count_tr_index.appendChild(detail_td);
                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:20px; height:18.5px;");
                detail_td.innerHTML = res[i]['Category'];
                genotype_count_tr_index.appendChild(detail_td);
                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:20px; height:18.5px;");
                var position_a = document.createElement("a");
                position_a.href = "/SoybeanGenVarX/viewVariantAndPhenotype.php?chromosome="+res[i]['Chromosome']+"&position="+res[i]['Position']+"&genotype="+res[i]['Genotype'];
                position_a.target = "_blank";
                position_a.innerHTML = res[i]['Count']
                detail_td.appendChild(position_a);
                genotype_count_tr_index.appendChild(detail_td);

                genotype_count_element_table.appendChild(genotype_count_tr_index);

            }
            
        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });

    // Change the overflow style of the div to scroll
    document.getElementById(gene+"_b").style.overflow = 'scroll';
    document.getElementById(gene+"_weblogo").style.overflow = 'scroll';
    document.getElementById(gene+"_detail_table").style.overflow = 'scroll';
}
