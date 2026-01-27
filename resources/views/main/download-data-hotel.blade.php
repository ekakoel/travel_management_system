@section('title', __('messages.Hotels'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @if (Auth::User()->position == "developer")
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="card-box m-b-18">
                    <div class="row align-items-center">
                        <div class="col-md-4 m-b-18 img-panel">
                            <img src="images/property/database.png" alt="Welcome">
                        </div>
                        <div class="col-md-8">
                        
                            <div class="welcome-title p-b-18">
                                Database
                            </div>
                            <div class="welcome-text m-b-18">
                                <p>
                                    On this page you can download data for each service provided by {{ config('app.business') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-action">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="row">
                <div class="col-12 text-right p-b-8">
                   <a href="/download"><button class="btn btn-danger"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <button class="btn btn-primary" onclick="savePDF()"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Download</button>
                  </div>
                </div>
                <div class="row">
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div id="printTable" class="col-md-12 m-b-18">
                        <div class="card-box mb-30">
                            <div class="row">
                                <div class="col-6">
                                    
                                    <div class="title">
                                        Data Hotels
                                    </div>
                                </div>
                            </div>
                            <div class="pb-20">
                                <table class="table hover" >
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Hotels</th>
                                            <th class="width:10%">Room</th>
                                            @foreach ($data_hotels as $dh)
                                                @foreach ($dh->rooms as $hr)
                                                        @php
                                                            $cp[] = count($hr->prices);
                                                            $jh = max($cp);
                                                        @endphp
                                                @endforeach
                                            @endforeach
                                            @for ($i = 0; $i < $jh; $i++)
                                                <th class="width:10%">Room</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data_hotels as $no=>$hotel)
                                            <tr>
                                                <td style="padding: 0 0 0 8px !important; border:1px solid #cdcdcd">{{ ++$no }}</td>
                                                <td style="padding: 0 0 0 8px !important; border:1px solid #cdcdcd">{!! $hotel->name !!}</td>  
                                                @foreach ($hotel->rooms as $room)
                                                    <td style="padding: 0 !important; border:1px solid #cdcdcd">
                                                        <div class="download-tabel-box-name float-left">
                                                        {{ $room->rooms }}<br>
                                                        </div>
                                                        @foreach ($room->prices->where('end_date','>=',$now)->sortBy('start_date') as $price)
                                                            @php
                                                                $usd_crate = ceil($price->contract_rate / $usdrates->rate);
                                                                $usd_crate_mark = $usd_crate + $price->markup;
                                                                $taxs = ceil(($usd_crate_mark * $tax->tax)/100);
                                                                $usd_prate = $usd_crate_mark + $taxs;
                                                            @endphp
                                                         <div class="download-tabel-box-date float-left">
                                                                {!! date("m/d",strtotime($price->start_date))." - ".date("m/d",strtotime($price->end_date)) !!}
                                                         </div>
                                                         <div class="download-tabel-box-price float-left">
                                                                {!! "$ ".$usd_prate !!}
                                                         </div>
                                                        @endforeach
                                                    </td>  
                                                @endforeach  
                                            </tr> 
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                   
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endif
    <script>
        window.jsPDF = window.jspdf.jsPDF;
            var docPDF = new jsPDF({
                format: 'a4',
                unit: 'mm',
                orientation: 'l'
            }
        );
        function savePDF(){
            var elementHTML = document.querySelector("#printTable");
            docPDF.html(elementHTML, {
                callback: function(docPDF) {
                    docPDF.save('Data Hotel Prices.pdf');
                },
                x: 15,
                y: 15,
                width: 270,
                windowWidth: 1024
            });
        }
    </script>
    <script>
        function printFunction() { 
            var css = '@page { size: landscape; }',
            head = document.getElementById('printTable'),
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
   
@endsection




