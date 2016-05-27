$( document ).ready(function() {
    $( ".datepicker" ).datepicker({
      dateFormat: "dd.mm.yy", // this is equal to the symfony format "dd.MM.yyyy"
      changeYear: true,
      firstDay: 1, // 0=Su,1=Mo,..
    });
});
