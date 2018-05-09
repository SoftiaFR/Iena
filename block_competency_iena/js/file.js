function filter() {
    var keyword = document.getElementById("input-framework").value;
    var select = document.getElementById("select-framework");
    for (var i = 0; i < select.length; i++) {
        var txt = select.options[i].text;
        if (txt.substring(0, keyword.length).toLowerCase() !== keyword.toLowerCase() && keyword.trim() !== "") {
            $(select.options[i]).attr('disabled', 'disabled').hide();
        } else {
            $(select.options[i]).removeAttr('disabled').show();
        }
    }
}


function updateTextComp(value, idcourse,wwwroot){
    console.log(value);
    $.ajax({
        url: wwwroot+'/blocks/competency_iena/competency_iena_competencies_api.php?courseid='+idcourse,
        type: 'POST',
        timeout: 10000,
        contentType: 'application/x-www-form-urlencoded',
        data: {idcompetence:value},
        success: function(result) {
            var json_res = JSON.parse(result);
            $('#btn-comp-iena').show();
            $('#name_comp_iena').html(json_res.shortname);
            $('#desc_comp_iena').html(json_res.description);
            $('#id-comp-iena').val(json_res.id);
        }
    });
}

function updateTextRef(value, idcourse,wwwroot) {
    console.log(value);
    $.ajax({
        url: wwwroot + '/blocks/competency_iena/competency_iena_competencies_api.php?courseid=' + idcourse,
        type: 'POST',
        timeout: 10000,
        contentType: 'application/x-www-form-urlencoded',
        data: {idref: value},
        success: function (result) {
            var json_res = JSON.parse(result);
            console.log(json_res.id);
            $('#name_ref_iena').html(json_res.shortname);
            $('#desc_ref_iena').html(json_res.description);
            document.getElementById("ref_mod").value = json_res.id;
            $('#change_ref').css("visibility", 'visible')
        }
    });
}

