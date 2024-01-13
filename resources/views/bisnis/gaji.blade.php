<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penerimaan Gaji</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <style>
        .card {
            margin-bottom: 1.5rem;
        }

        .card {
            position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #c8ced3;
            border-radius: .25rem;
        }

        .card-header:first-child {
            border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
        }

        .card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: #f0f3f5;
            border-bottom: 1px solid #c8ced3;
        }
    </style>

</head>

<body>
    <div class="container-fluid mt-5">
            <div>
                <div class="card">

                      <div class="card-header">Tanggal
                        <strong>{{$tanggal}}</strong>
                        <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                            <i class="fa fa-print"></i> Print</a>
                        <!-- <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                            <i class="fa fa-save"></i> Save</a> -->
                    </div>
                              <div class="card-body">
                      

                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th class="center">Nama Pegawai</th>
                                        <th class="center">Gaji</th>
                                        <th class="center">Uang Makan</th>
                                        <th class="center">Bonus</th>
                                        <th class="center">Total</th>
                                        <th class="center">TTD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
 $total = 0;
@endphp
                                    @foreach ($data as $key => $item)
                                     @php
$total = $total + $item->bonus + $item->uang_makan + $item->bonus;
@endphp
                                    <tr>
                                        <td class="center">{{$key + 1}}</td>
                                        <td class="left">{{$item->pegawai->name}}</td>
                                        <td class="left">{{number_format($item->gaji)}}</td>
                                        <td class="left">{{number_format($item->uang_makan)}}</td>
                                        <td class="left">{{number_format($item->bonus)}}</td>
                                        <td class="left">{{number_format($item->bonus + $item->uang_makan + $item->bonus)}}</td>
                                        <td class=""></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                     <tr>
                                        <th colspan="5" class="text-right">Total Bayar</th>
                                        <th class="text-left">{{number_format($total)}}</th>
                                        <th class="text-left"></th>
                                     </tr>
                                </tfoot>
                            </table>
                        </div>
                     
                    </div>
                </div>
            </div>
    </div>

</body>

</html>