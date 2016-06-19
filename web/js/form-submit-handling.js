function formSubmit(form, sender){
    $(sender).addClass('disabled');
    form.submit(); 
}
