@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy dw dw-hotel"></i> Weddings
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active" aria-current="page">Wedding Venues</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="info-action">
                        @if (\Session::has('error'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('error') !!}</li>
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
                        <div class="col-md-4 mobile">
                            @if (count($activeweddings)>0 or count($draftweddings)>0 or count($archivedweddings)>0)
                                <div class="row">
                                    @if (count($activeweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <div class="height-100-p widget-style1">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="{!! $service->icon !!}"></i>
                                                        
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activeweddings->count() }} Weddings</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (count($draftweddings)>0)
                                    <div class="col-xl-12 mb-18">
                                        <div class="height-100-p widget-style1">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <div class="chart-icon">
                                                    <i class="{!! $service->icon !!}"></i>
                                                </div>
                                                <div class="widget-data">
                                                    <div class="widget-data-title">{{ $draftweddings->count() }} Weddings</div>
                                                    <div class="widget-data-subtitle">Draft</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if (count($archivedweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <div class="height-100-p widget-style1">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="{!! $service->icon !!}"></i>
                                                        
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="h4 mb-0">{{ $archivedweddings->count() }} Weddings</div>
                                                        <div class="weight-600 font-14">Archived</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="weddings" class="card-box m-b-18">
                                <div class="card-box-title">
                                    <div class="subtitle">Hotel</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="col-md-4">
                                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                    <input id="searchWeddingByName" type="text" onkeyup="searchWeddingByName()" class="form-control" name="search-wedding-location" placeholder="Search Hotel...">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                
                                <table id="tbWeddings" class="data-table table nowrap" >
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Hotel</th>
                                            <th class="text-center">CV/RV/PC</th>
                                            <th>Brocure</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($hotels as $no=>$hotel)
                                            @php
                                                $ceremony_venue = $ceremony_venues->where('hotels_id',$hotel->id);
                                                $reception_venue = $reception_venues->where('hotel_id',$hotel->id);
                                                $wedding_package = $weddings->where('hotel_id',$hotel->id);
                                                $contractWeddings = $contract_weddings->where('hotels_id',$hotel->id)->where('period_start','<=',$now)->where('period_end','>=',$now);
                                            @endphp
                                            <tr>
                                                <td>{{ ++$no }}<br>
                                                </td>
                                                <td>
                                                    {{ $hotel->name }}
                                                </td>
                                                <td class="text-center">
                                                    {{ count($ceremony_venue) }}/{{ count($reception_venue) }}/{{ count($wedding_package) }}
                                                </td>
                                                
                                                <td>
                                                    @if (count($contractWeddings)>0)
                                                        <div class="property-icon">
                                                            @foreach ($contractWeddings as $contract_wedding)
                                                                <div class="icon-list">
                                                                    <a href="#" data-target="#contract-wedding-pdf-{{ $contract_wedding->id }}" data-toggle="modal">
                                                                        <i class="icon-copy fa fa-file-pdf-o" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Detail {{ $contract_wedding->name }}"></i> 
                                                                    </a>
                                                                </div>
                                                                {{-- Modal Property PDF ----------------------------------------------------------------------------------------------------------- --}}
                                                                <div class="modal fade" id="contract-wedding-pdf-{{ $contract_wedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                                            <div class="modal-body pd-5">
                                                                                <embed src="storage/hotels/wedding-contract/{{ $contract_wedding->file_name }}" frameborder="10" width="100%" height="850px">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                
                                                <td class="text-right">
                                                    <div class="table-action">
                                                        <a href="/weddings-hotel-admin-{{ $hotel->id }}">
                                                            <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                        </a>
                                                    </div>
                                                </td>
                                                
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            @if (count($activeweddings)>0 or count($draftweddings)>0 or count($archivedweddings)>0)
                                <div class="row">
                                    @if (count($activeweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <div class="height-100-p widget-style1">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="{!! $service->icon !!}"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activeweddings->count() }} Weddings</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (count($draftweddings)>0)
                                    <div class="col-xl-12 mb-18">
                                        <div class="height-100-p widget-style1">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <div class="chart-icon">
                                                    <i class="{!! $service->icon !!}"></i>
                                                </div>
                                                <div class="widget-data">
                                                    <div class="widget-data-title">{{ $draftweddings->count() }} Weddings</div>
                                                    <div class="widget-data-subtitle">Draft</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if (count($archivedweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <div class="height-100-p widget-style1">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="{!! $service->icon !!}"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="h4 mb-0">{{ $archivedweddings->count() }} Weddings</div>
                                                        <div class="weight-600 font-14">Archived</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchWeddingByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchWeddingByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbWeddings");
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
</script>
