<!DOCTYPE html>
<html>
    <head>
        <title>Hotel Package Pricelist </title>
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
            <div id="printTable" class="m-b-18 p-18">
                <div class="mb-30">
                    <div class="row">
                        <div class="col-12">
                            <div class="h3">
                                Hotel Package Pricelist
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="pb-20">
                                <table class="data-table table table-bordered table-hover nowrap">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; font-size:13px;">No</th>
                                            <th style="width: 15%; font-size:13px;">Hotel</th>
                                            <th style="width: 15%; font-size:13px;">Rooms</th>
                                            <th style="width: 25%; font-size:13px;">Package</th>
                                            <th style="width: 30%; font-size:13px;">Stay Period</th>
                                            <th style="width: 10%; font-size:13px">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($packages as $no=>$package)
                                            @foreach ($hotels->where('id', $package->hotels_id) as $hotel)
                                                @php
                                                    $usd_crate = ceil($package->contract_rate / $usdrates->rate);
                                                    $usd_crate_mark = $usd_crate + $package->markup;
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
                                                    @foreach ($rooms->where('id',$package->rooms_id) as $room)
                                                        <td >
                                                            {{ $room->rooms }}
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        <b>{{ $package->name }}</b> <br>
                                                        - Minimum Stay: {{ $package->duration." Night" }}<br>
                                                    </td>
                                                    <td>
                                                        {{ date("d M y",strtotime($package->stay_period_start))." - ".date("d M y",strtotime($package->stay_period_end)) }}
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
            <div class="col-12 text-right m-r-18">
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
                var elementHTML = document.querySelector("#printTable");
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