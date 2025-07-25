<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css"
        media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Laporan Penjualan</span>

        <form action="{{ route('laporan-penjualan') }}" method="get">
            {{-- <div class="row my-2 d-print-none">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tanggal Awal</label>
                <div class="form-group col-3">
                    <div class="input-group date" id="datetimepicker">
                        <input type="text" class="form-control" name="startdate" value="{{$startDate}}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                <label for="staticEmail" class="col-sm-2 col-form-label">Tanggal Akhir</label>
                <div class="form-group col-3">
                    <div class="input-group date" id="datetimepicker">
                        <input type="text" class="form-control" name="endDate" value="{{$endDate}}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div> --}}
            {{-- <p class="my-2">
                <span class="fs-4">Tanggal Data <span class="fw-bold">{{date('d F Y',
                        strtotime($tanggal))}}</span></span>
            </p> --}}
            {{-- <p>Total Semua Persediaan <span class="fw-bold "> Rp. {{number_format($totalSemuaPersediaan,0)}}</span>
            </p> --}}

            <div class="row d-print-none my-4">

                <div class="col-1 d-grid float-end">
                    <button type="button" class="btn btn-primary" onclick="window.print(); return false;">Print</button>
                </div>
            </div>

            {{$data}}
            {{-- TABLE --}}
            @php
            $totalTable = 0;
            @endphp
            <div class="my-4">
                <span class="fs-3">Data Tanggal {{$startDate}} s.d {{$endDate}}</span>

            </div>
            <div>
                @if($startDate == null || $endDate==null)
                @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nomor Faktur</th>
                            <th>Nama Pelanggan</th>
                            <th>Sales</th>
                            <th>Status</th>
                            <th>Sub Total</th>
                            <th>Ongkir</th>
                            <th>Pajak</th>
                            <th>Total</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if ($data->count() == 0)
                        <tr>
                            <td colspan="9">Tidak ada data</td>
                        </tr>
                        @else
                        @foreach($data as $key=> $p)

                        <tr>
                            <td>{{ 1 + $key }}</td>
                            <td>{{ Carbon\Carbon::parse($p->created_at)->format('d F Y') ?? '-' }}</td>
                            <td>{{ $p->nomor_faktur ?? '-' }}</td>
                            <td>{{ $p->nama_pelanggan ?? '-' }}</td>
                            <td>{{ $p->sales->name ?? '-' }}</td>
                            <td>{{ $p->status }}</td>
                            <td>{{ number_format($p->sub_total,0) }}</td>
                            <td>{{ number_format($p->ongkir,0) }}</td>
                            <td>{{ number_format($p->pajak,0) }}</td>
                            <td>{{ number_format($p->total,0) }}</td>
                        </tr>
                        @php
                        $totalTable += $p->total;
                        @endphp
                        @endforeach
                    <tfoot>
                        <tr>
                            <td colspan="9" style="background-color: #080000;" class="text-white">TOTAL</td>
                            <td style="background-color: #080000;" class="text-white">
                                {{number_format($totalTable,0)}}
                            </td>

                        </tr>
                    </tfoot>
                    @endif


                    </tbody>
                </table>
                @endif
            </div>
            {{-- PAGINATION --}}

            <div class="mx-auto col-12 d-print-none">
                {{-- <ul class="pagination pagination-lg justify-content-center">
                    <li class="page-item {{$persediaan->onFirstPage() == 1 ? 'disabled' : ''}}">
                        <a class="page-link" href="{{$persediaan->previousPageUrl()}}">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="{{$persediaan->url(10)}}">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link {{$persediaan->lastPage() == 1 ? 'disabled' : ''}}"
                            href="{{$persediaan->nextPageUrl()}}">Next</a>
                    </li>
                </ul> --}}
                {{-- <p>{{$persediaan->from()}} ke {{$persediaan->to()}} dari total {{$persediaan->total()}}</p> --}}

            </div>
        </form>
    </div>


</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $(function() {
        $('#datetimepicker').datetimepicker({
            format: 'DD MMMM YYYY'
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
</script>


</html>