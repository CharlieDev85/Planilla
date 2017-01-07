$(document).ready(function() {
    $(".txtNum").keydown(function (e) {

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $(".alpha-only").on("input", function(){
        var regexp = /[^a-zA-Z\sÑñáÁéÉíÍóÓúÚ]/g;
        if($(this).val().match(regexp)){
            $(this).val( $(this).val().replace(regexp,'') );
        }
    });



    $("#radiobutt input[type=radio]").each(function(i){
        $(this).click(function(){
            if(i==1){//second radiobutton
                $("#radioFechaInactividad").prop("disabled", false);
            }else{
                $("#radioFechaInactividad").prop("disabled", true);
                $("#radioFechaInactividad").prop("value", "");
            }
        });
    });

});



