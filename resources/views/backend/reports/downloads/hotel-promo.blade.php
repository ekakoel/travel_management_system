<!DOCTYPE html>
<html>
    <head>
        <title>Hotel Promo Pricelist </title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <script src="https://kit.fontawesome.com/ea60220155.js" crossorigin="anonymous"></script>
    </head>
    <body style="background-color: white !important;">
        <div class="container-print">
            <div id="tblPromo" class="p-18">
                <div class="mb-30">
                    <div class="row">
                        <div class="col-12">
                            <div class="h3">
                                Hotel Promo Pricelist
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="pb-20">
                                <table id="tbHotelNormal" class="data-table table table-bordered table-print">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; font-size:13px;">No</th>
                                            <th style="width: 15%; font-size:13px;">Hotel</th>
                                            <th style="width: 15%; font-size:13px;">Rooms</th>
                                            <th style="width: 25%; font-size:13px;">Promo</th>
                                            <th style="width: 30%; font-size:13px;">Period</th>
                                            <th style="width: 10%; font-size:13px">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($promos as $no=>$promo)
                                            @foreach ($hotels->where('id', $promo->hotels_id) as $hotel)
                                                @php
                                                    $usd_crate = ceil($promo->contract_rate / $usdrates->rate);
                                                    $usd_crate_mark = $usd_crate + $promo->markup;
                                                    $taxs = ceil(($usd_crate_mark * $tax->tax)/100);
                                                    $usd_prate = $usd_crate_mark + $taxs;
                                                    $jh = count($hotels);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        {{ $hotel->name }}
                                                    </td>
                                                    @foreach ($rooms->where('id',$promo->rooms_id) as $room)
                                                        <td >
                                                            {{ $room->rooms }}
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        {{ $promo->name }}
                                                    </td>
                                                    <td>
                                                        <p style="font-size: 1.2rem !important;">Booking Period:</p>
                                                        {{ date("d M y",strtotime($promo->book_periode_start))." - ".date("d M y",strtotime($promo->book_periode_end)) }}
                                                        <p style="font-size: 1.2rem !important;">Stay Period:</p>
                                                        {{ date("d M y",strtotime($promo->periode_start))." - ".date("d M y",strtotime($promo->periode_end)) }}
                                                    </td>
                                                    <td>
                                                        {{ "$ ".  number_format($usd_prate) ." /night" }}
                                                    </td>      
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden-print row float-container">
            <div class="col-12 text-right">
                <button class="btn btn-primary" onclick="printFunction()"><i class="icon-copy fa fa-print" aria-hidden="true"></i> Print</button>
                {{-- <button class="btn btn-primary" id="printButton" onclick="savePDF()"><i class="icon-copy fa fa-download" aria-hidden="true"></i> Download PDF</button> --}}
                <a href="/download">
                    <button class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                </a>
            </div>
        </div>
        {{-- <script>
            window.jsPDF = window.jspdf.jsPDF;
                var docPDF = new jsPDF({
                    format: 'a4',
                    unit: 'mm',
                    orientation: 'p'
                }
            );
            function savePDF(){
                var elementHTML = document.querySelector("#tblPromo");
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
        </script> --}}
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
       
    </body>
</html>