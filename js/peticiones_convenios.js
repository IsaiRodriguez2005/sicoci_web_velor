function select_tipo_desc(){
    //* dependiendo de la seleccion del 'tipo' de descento, se deshabilitaran los campos.

    tipo = $("#tipo").val();

    if(Number(tipo) == 1 ){
        $("#dsc_conul").attr('disabled', true);
        $("#cost_consul").removeAttr('disabled');
    } else if(Number(tipo) == 2 ){
        $("#cost_consul").attr('disabled', true);
        $("#dsc_conul").removeAttr('disabled');
    }
    
}