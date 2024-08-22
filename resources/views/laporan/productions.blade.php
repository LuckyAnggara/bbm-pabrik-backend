<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Laporan Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css" media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Laporan Produksi</span>

        <form action="{{ route('laporan-produksi') }}" method="get">
            <div class="row my-2 d-print-none">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tanggal</label>
                <div class="form-group col-3">
                    <div class="input-group date" id="datetimepicker">
                        <input type="text" class="form-control" name="tanggal" value="{{$tanggal}}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {{-- <p class="my-2">
                <span class="fs-4">Tanggal Data <span class="fw-bold">{{date('d F Y', strtotime($tanggal))}}</span></span>
            </p> --}}


            <div class="row d-print-none my-4">

                <div class="col-1 d-grid float-end">
                    <button type="button" class="btn btn-primary" onclick="window.print(); return false;">Print</button>
                </div>
            </div>


            {{-- TABLE --}}
            @php
            $totalTable = 0;
            @endphp

            <div class="my-4">
                <span class="fs-3">Data Tanggal {{$tanggal}}</span>
            </div>
            <div>
                @if($tanggal == null)
                @else
                <table class="table table-striped table-bordered table-responsive">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th style="width:10%">Nomor Produksi</th>
                            <th style="width:5%">Shift</th>
                            <th style="width:10%">Tanggal</th>
                            <th style="width:10%">Mesin</th>
                            <th style="width:20%">Operator</th>
                            <th style="width:20%">Bahan / Input</th>
                            <th style="width:20%">Hasil / Output</th>
                            <th style="width:5%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($data->count() == 0)
                        <tr>
                            <td colspan="10">Tidak ada data</td>
                        </tr>
                        @else
                        @foreach($data as $key=> $p)

                        <tr style="background-color: #ed6464;">
                        <tr>
                            <td>{{ 1 + $key }}</td>
                            <td>
                                <div class="row px-3"><span>{{ $p->sequence }}</span><span><strong>{{ $p->jenis->name }}</strong></span> </div>
                            </td>
                            <td>@if ($p->shift == 1) <span>PAGI</span> @else <span>MALAM</span> @endif </td>

                            <td>
                                <div class="row px-3">
                                    <span><strong>Pelaksanaan</strong></span><span>{{ date('d F Y', strtotime($p->order_date)) }}</span><span><strong>Selesai</strong></span><span>{{ date('d F Y', strtotime($p->target_date)) }}</span>
                                </div>
                            </td>
                            <td>

                                <ol class="">
                                    @foreach($p->machine as $key=> $machine)
                                    <li class=""><span>{{$machine->machine->name}}</span></li>
                                    @endforeach
                                </ol>
                            </td>
                            <td>
                                PIC :<span class="fw-bolder"> {{$p->pic_name}}</span>
                                <ol class="">
                                    @foreach($p->pegawai as $key=> $pegawai)
                                    <li class=""><span>{{$pegawai->pegawai->name}}</span></li>
                                    @endforeach
                                </ol>

                            </td>
                            <td>
                                <ol class="">
                                    @foreach($p->input as $key=> $input)
                                    <li class="">
                                        <div class="">


                                            <span class="fw-bolder">{{$input->item->name}}</span> <span>{{$input->real_quantity}}</span> <span>{{$input->item->unit->name}}</span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ol>
                            </td>

                            <td>
                                <ol class="">
                                    @foreach($p->output as $key=> $output)
                                    <li class=""><span class="fw-bolder">{{$output->item->name}}</span> <span>{{$input->real_quantity}}</span> <span>{{$input->item->unit->name}}</span></li>
                                    @endforeach
                                </ol>
                            </td>
                            <td>
                                <span>{{ $p->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                        <!-- <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="1" style="background-color: #080000;" class="text-white">TOTAL</td>
                            <td style="background-color: #080000;" class="text-white"> {{number_format($totalTable,0)}}</td>
                            <td style="background-color: #080000;" class="text-white">{{number_format($totalTable,0)}}</td>
                        </tr>
                    </tfoot> -->

                        @endif


                    </tbody>
                </table>
                @endif
            </div>
            {{-- PAGINATION --}}

            <div class="mx-auto col-12 d-print-none">
                {{-- <ul class="pagination pagination-lg justify-content-center">
                    <li class="page-item {{$persediaan->onFirstPage() == 1 ? 'disabled' : ''}}" >
                <a class="page-link" href="{{$persediaan->previousPageUrl()}}">Previous</a>
                </li>
                <li class="page-item"><a class="page-link" href="{{$persediaan->url(10)}}">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link {{$persediaan->lastPage() == 1 ? 'disabled' : ''}}" href="{{$persediaan->nextPageUrl()}}">Next</a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(function() {
        $('#datetimepicker').datetimepicker({
            format: 'DD MMMM YYYY'
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>


</html>