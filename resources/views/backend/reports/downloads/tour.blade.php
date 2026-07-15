<!DOCTYPE html>
<html>
    <head>
        <title>Download Data Tour Prices</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/css/style.css">
    </head>
    @can('isAdmin')
        <body class="bg-white">
            <div class="container" style="padding: 24px 0">
                <div id="printTable" class="m-b-18 p-18">
                    <div class="mb-30">
                        <div class="row">
                            <div class="col-12">
                                <div class="h3">
                                    Hotel Prices
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="pb-20">
                                    <table class="data-table table table-bordered table-hover nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 1%; font-size:13px;">No</th>
                                                <th style="width: 10%; font-size:13px;">Tour Package</th>
                                                <th style="width: 10%; font-size:13px;">Type</th>
                                                <th style="width: 15%; font-size:13px;">Duration</th>
                                                <th style="width: 10%; font-size:13px">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data_tours as $no=>$tour)
                                                @php
                                                    $usd_crate = ceil($tour->contract_rate / $usdrates->rate);
                                                    $usd_crate_mark = $usd_crate + $tour->markup;
                                                    $taxs = ceil(($usd_crate_mark * $tax->tax)/100);
                                                    $usd_prate = $usd_crate_mark + $taxs;
                                                @endphp
                                               
                                                        <tr> 
                                                           <td>{{ ++$no }}</td>
                                                           <td>{{ $tour->name }}</td>
                                                           <td>{{ $tour->type }}</td>
                                                           <td>{{ $tour->duration }}</td>
                                                           <td>{{ "$ ".$usd_prate }}</td>
                                                           
                                                        </tr>
                                                  @endforeach 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden-print row m-b-18">
                    <div class="col-12 text-right m-r-18">
                        <button onclick="printFunction()">Print</button>
                        <button id="printButton" onclick="savePDF()">Download PDF</button>
                        <a href="/hotels-admin">
                            <button>Cancel</button>
                        </a>
                    </div>
                </div>
            </div>
            <script>
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
            </script>
            <script>
                function printFunction() { 
                    var css = '@page { size: landscape; }',
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
    @endcan
</html>