<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Laporan Absensi Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css" media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Laporan Absensi Pegawai</span>
            <p class="my-2">
                <span class="fs-4">Bulan <span class="fw-bold">{{ $bulan}}</span></span>
            </p>

              <p class="my-2">
                <span class="fs-4">Nama <span class="fw-bold">{{ $pegawai->name}}</span></span>
            </p>

            <div class="row d-print-none my-4">
                <div class="col-11">

                </div>

                <div class="col-1 d-grid float-end">
                    <button type="button" class="btn btn-primary" onclick="window.print(); return false;">Print</button>
                </div>
            </div>
            {{-- TABLE --}}
            <div class="row p-4">
               <table class="table table-striped">
                        <thead>
                            <th style="width:25%">Tanggal</th>
                            <th style="width:15%">Jam Masuk</th>
                            <th style="width:15%">Jam Keluar</th>
                            <th style="width:10%">Shift</th>
                            <th style="width:10%">Total Jam Kerja</th>
                        </thead>
                        <tbody>
                            @foreach($absensi as $absen)
                            <tr>
                                <!-- // PENJUALAN -->
                                <td>{{ date('d F Y', strtotime($absen->tanggal_data)) }}</td>
                                <td>{{ date('h:i', strtotime($absen->start_time)) }}</td>
                                <td>{{ date('h:i', strtotime($absen->end_time)) }}</td>
                                <td>{{$absen->shift_type}}</td>
                                <td>{{$absen->jamKerja}}</td>
                           
                            </tr>
                    
                            @endforeach
                        </tbody>
                    </table>

            </div>

        </form>
    </div>


</body>

<script src=" https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>



</html>