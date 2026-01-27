const { default: Swal } = require("sweetalert2");

const flashData = $('.flash-data').data('flashdata');
// console.log(flashData);
if(flashData){
    Swal({
        title:'Data Order',
        text: 'Berhasil' + flashData,
        type: 'success'
    });
}


function hideSpinner() {
	document.getElementById('loading-spinner').style.display = 'none';
}