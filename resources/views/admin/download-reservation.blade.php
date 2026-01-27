@php
    use Illuminate\Support\Carbon;
    // header("Content-type: application/vnd-ms-excel");
    // header("Content-Disposition: attachment; filename=Data Pegawai.xls");
@endphp
<!DOCTYPE html>
<html>
    <head>
        <title>Download Contract</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/print-style.css">
        {{-- <link rel="stylesheet" type="text/css" href="/css/style.css"> --}}
        <script src="https://kit.fontawesome.com/ea60220155.js" crossorigin="anonymous"></script>
    
    <script>
        function printFunction() { 
            var css = '@page { size: potrait; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');
            style.type = 'text/css';
            style.media = 'print';
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);
            window.print();
        }
    </script>
    </head>
    
    <body>
        <div class="print-container d-print-flex">
            <div class="row">
                <div class="col-md-6">
                    <div class="logo-head">
                        <img src="/storage/logo/logo-color-bali-kami.png"alt="Bali Kami Tour & Travel">
                    </div>
                    <div class="bussines-title">{{ $business->name }}</div>
                    <div class="bussines-subtitle">{{ __('messages.'.$business->caption) }}</div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="title-head">Contract <span>合同</span></div>
                    <div class="date-label">
                        {{ dateFormat($reservation->created_at) }}
                    </div>
                </div>
            </div>
            <hr class="form-hr">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="page-subtitle d-print-none">Reservation 
                                            @if ($reservation->status != "Active")
                                                <span>
                                                    <a href="#" data-toggle="modal" data-target="#update-reservation-{{ $reservation->id }}"> 
                                                        <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit Reservation" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
        </div>
        {{-- <button class="btn btn-primary" onclick="printFunction()"><i class="icon-copy fa fa-print" aria-hidden="true"></i> Print</button> --}}
        <button class="btn btn-primary" id="printButton" onclick="savePDF()"><i class="icon-copy fa fa-download" aria-hidden="true"></i> Download PDF</button>
        <button class="btn btn-primary" onclick="printFunction()"><i class="icon-copy fa fa-print" aria-hidden="true"></i> Print</button>
        <script>
            window.jsPDF = window.jspdf.jsPDF;
                var docPDF = new jsPDF({
                    format: 'a4',
                    unit: 'mm',
                    orientation: 'p'
                }
            );
            function savePDF(){
                var elementHTML = document.querySelector("#tbHotelNormal");
                docPDF.html(elementHTML, {
                    callback: function(doc) {
                        // Save the PDF
                        doc.save('document-html.pdf');
                    },
                    margin: [10, 10, 10, 10],
                    autoPaging: 'text',
                    x: 0,
                    y: 0,
                    width: 190, //target width in the PDF document
                    windowWidth: 675 //window width in CSS pixels
                });
            }
    </body>
</html>
