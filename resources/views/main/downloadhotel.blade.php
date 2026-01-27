<!DOCTYPE html>
<html>

<head>
    <title>Download Data Hotel Prices</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
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
<body style="background-color: white !important;">
    <div class="container-print">
        <div id="printTable" class="m-b-18">
            <div class="mb-30">
                <div class="row hidden-print">
                    <div class="col-md-12">
                        <div class="heading">
                            Hotel Prices
                        </div>
                    </div>
                </div>
                
                <div class="row m-t-8">
                    <div class="col-12">
                        <div class="pb-20">
                            <table id="tbHotelNormal" class="data-table table table-bordered table-print">
                                <thead>
                                    <tr>
                                        <th style="width: 5%" class="text-center">No</th>
                                        <th style="width: 20%" class="text-center">Hotels</th>
                                        <th style="width: 20%" class="text-center">Room</th>
                                        <th style="width: 20%" class="text-center">Period Start</th>
                                        <th style="width: 20%" class="text-center">Period End</th>
                                        <th style="width: 15%" class="text-center">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_hotels as $no => $hotel)
                                        @php
                                            $h = 1;
                                        @endphp
                                        @foreach ($hotel->rooms->where('status', 'Active') as $room)
                                            @php
                                                $r = 1;
                                            @endphp
                                            @foreach ($room->prices->where('end_date', '>=', $now) as $price)
                                                @php
                                                    $usd_crate = ceil($price->contract_rate / $usdrates->rate);
                                                    $usd_crate_mark = $usd_crate + $price->markup;
                                                    $taxs = ceil(($usd_crate_mark * $tax->tax) / 100);
                                                    $usd_prate = $usd_crate_mark + $taxs;
                                                    $cp = count($room->prices->where('end_date', '>=', $now));
                                                    $jh = count($hr_prices->where('hotels_id', $price->hotels_id)->where('end_date', '>=', $now));
                                                @endphp
                                                <tr>
                                                    @if ($h == 1)
                                                        <td rowspan="{{ $jh }}" class="text-center">
                                                            {{ ++$no }}
                                                        </td>
                                                        <td rowspan="{{ $jh }}">
                                                            {!! $hotel->name !!}
                                                            @php
                                                                $h++;
                                                            @endphp
                                                        </td>
                                                    @endif
                                                    @if ($r == 1)
                                                        <td rowspan="{{ $cp }}">
                                                            {!! $room->rooms !!}
                                                            @php
                                                                $r++;
                                                            @endphp
                                                        </td>
                                                    @endif
                                                    <td class="text-center">
                                                        {{ date('d M y', strtotime($price->start_date)) }}<br>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ date('d M y', strtotime($price->end_date)) }}<br>
                                                    </td>
                                                    <td class="text-right">
                                                        {{ "$ " . $usd_prate }}<br>
                                                    </td>
                                                </tr>
                                            @endforeach
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
            <button class="btn btn-primary" onclick="printFunction()"><i class="icon-copy fa fa-print"
                    aria-hidden="true"></i> Print</button>
            {{-- <button class="btn btn-primary" id="printButton" onclick="savePDF()"><i class="icon-copy fa fa-download" aria-hidden="true"></i> Download PDF</button> --}}
            <a href="/hotels-admin">
                <button class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i>
                    Cancel</button>
            </a>
        </div>
    </div>

    {{-- <script>
        window.jsPDF = window.jspdf.jsPDF;
        var docPDF = new jsPDF({
            format: 'a4',
            unit: 'mm',
            orientation: 'p'
        });

        function savePDF() {
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
    </script> --}}
    {{-- <script>
                function searchByHotelName() {
                  var input, filter, table, tr, td, i, txtValue;
                  input = document.getElementById("searchByHotelName");
                  filter = input.value.toUpperCase();
                  table = document.getElementById("tbHotelNormal");
                  tr = table.getElementsByTagName("tr");
                  for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[1];
                    if (td) {
                      txtValue = td.textContent || td.innerText;
                      if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                      } else {
                        tr[i].style.display = "none";
                      }
                    }       
                  }
                }
            </script> --}}
    

</body>

</html>
