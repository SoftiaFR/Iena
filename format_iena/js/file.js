
$(document).ready(function () {
    var table = $('#example').DataTable({
        dom: 'Bfrtip',
        select: true,
        "bDestroy": true,
        order: [[1, 'asc']],

        buttons: [
            {
                text: 'Tout selectionner',
                action: function () {
                    for (i = 0; i <= table.rows().length; i++) {
                        if (table.row(i).node().style.display != 'none') {
                            table.row(i).select();
                        }
                        console.log(table.row(i).node().style.display);
                    }
                    //table.rows().select();
                }
            },
            {
                text: 'Tout déselectionner',
                action: function () {
                    table.rows().deselect();
                }
            }
        ],
        "scrollY": 300,
        "scrollX": true,
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2
        }
    });

    $('#example tbody').on('click', 'tr', function () {
        $(this).toggleClass('selected');
    });


    $('#button').click(function () {
        //alert(table.rows('.selected').data());
        //console.log(table.rows('.selected').data()[0][1]);
        var result = new Array();
        for (var i = 0; i < table.rows('.selected').data().length; i++) {
            //console.log(table.rows('.selected').data()[i][9]);
            if (table.rows('.selected').data()[i][2] != null) {
                result[i] = table.rows('.selected').data()[i][2];
            }
        }
        console.log(result);
        //console.log($('#courseID').val());
        var url = '/course/format/iena/suivi_unit.php?courseid=' + $('#courseID').val();
        var form = $('<form action="' + url + '" method="post">' +
            '<input type="text" name="api_url" value="' + result.toString() + '" />' +
            '</form>');
        $('body').append(form);
        form.submit();
    });

    $('.sectionH').hide();
    $('.courseAllH').hide();
    $('.' + $('#select-section').val()).show();
    $('.clickMe').click();
    
    $('#select-section').on('change', function () {
        section_function();
        group_function();
        student_function();
    });
    
    $('#example_filter').hide();
    
    $('#molette').click(function () {
        var valeurOption = $('#select-section option:selected').val().split("-");
        var varHref = $('#molette').attr('href');
        $('#molette').attr('href', varHref + '&sectionid=' + valeurOption[1]);
    });

    var getStudFilter = $('#select-section option:selected').val();
    getStudFilter += "filtre";
    $('.' + getStudFilter).hide();

    
    
    
    $('#select-student').on('change', function () {
 
        student_function();

    });

    $('#select-group').on('change', function () {
        group_function();
        student_function();
    });


function group_function()
{
        
     if ($('#select-group').val() === "groupAll") {
            $('.groupAll').show();
        } else {
            var tab_stud = $('#example tbody');
            for (var i = 0, row; row = tab_stud[0].rows[i]; i++) {
                var tab = tab_stud[0].rows[i].className.split(" ");
                for (var j = 0; j < tab.length; j++) {
                    if (tab[j] == $('#select-group').val()) {
                        $('.groupAll').hide();
                        $('.' + tab[j]).show();
                    }
                }
            }  
        }
        $('.clickMe').click();
}
function section_function()
{

        $('.sectionH').hide();
        if ($('#select-section').val() === "Cours") {
            $('.courseAllH').show();
        } else {
            $('.courseAllH').hide();
            
            $('.' + $('#select-section').val()).show();
        }
        $('.clickMe').click();
}
function student_function()
{
 
     if ($('#select-student').val() === "studentFilter") {
            var sectionActuelle = $('#select-section option:selected').val();
            sectionActuelle += "filtre";
            console.log(sectionActuelle);
            var tab_stud = $('#example tbody');

            for (var i = 0, row; row = tab_stud[0].rows[i]; i++) {
                var tab = tab_stud[0].rows[i].className.split(" ");
                for (var j = 0; j < tab.length; j++) {
                    if (tab[j] == sectionActuelle) {
                        $('.' + tab[j]).hide();
                    }
                }
            }
        } else {
            $('.sectionAll').show();
            $('.groupAll').hide();
            $('.' + $('#select-group').val()).show();
        }
        $('.clickMe').click();  
}



});

window.onload = function()
{
    $('.groupAll').hide();
    $('.' + $('#select-student').val()).show();

    $('.' + $('#select-group').val()).show();
    
}