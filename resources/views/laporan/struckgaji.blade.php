<!DOCTYPE html>
<html>

<head>
    <title>Slip Gaji Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
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
            <div class="card col-6 mx-auto">

                <div class="card-header">Tanggal
                    <strong>{{ date('d F Y', strtotime($tanggal)) }}</strong>
                    <a class="btn btn-sm btn-secondary d-print-none float-right mr-1" href="#"
                        onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                    <!-- <a class="btn btn-sm btn-info d-print-none float-right mr-1" href="#" data-abc="true">
                            <i class="fa fa-save"></i> Save</a> -->
                </div>
                <div class="card-body">
                    <div class="my-4">
                        <span class="h4">Slip Gaji Karyawan</span>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-3">Nama Karyawan:</h6>

                        </div>
                        <div>
                            <strong>{{ $pegawai->name }}</strong>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-3">Tanggal Penggajian:</h6>

                        </div>
                        <div>
                            <strong>{{ date('d F Y', strtotime($gaji->start_date)) }}</strong> s.d <strong>{{ date('d F Y', strtotime($gaji->end_date)) }}</strong>
                        </div>
                    </div>

                    <div class="table-responsive-sm mt-4">
                        <table class="table-striped table">
                            <thead>
                                <tr>
                                    <th style="width: 2px;%"></th>
                                    <th class="center">Keterangan</th>
                                    <th class="center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="left">1</td>
                                    <td class="left">Jumlah Jam Kerja</td>
                                    <td class="left">{{ $gaji->jam_kerja }} Jam</td>
                                </tr>
                                <tr>
                                    <td class="left">2</td>
                                    <td class="left">Gaji Pokok per Jam</td>
                                    <td class="left">Rp. {{ number_format($gaji->gaji, 0) }}</td>
                                </tr>
                                <tr>
                                    <td class="left">3</td>
                                    <td class="left font-semibold">Total Gaji (1 x 2)</td>
                                    <td class="text-right font-semibold">Rp. {{number_format($gaji->jam_kerja * $gaji->gaji)}}</td>
                                </tr>
                                   <tr>
                                    <td class="left">4</td>
                                    <td class="left">Uang Makan</td>
                                    <td class="left">Rp. {{ number_format($gaji->uang_makan, 0) }}</td>
                                </tr>
                                   <tr>
                                    <td class="left">5</td>
                                    <td class="left">Bonus</td>
                                    <td class="left">Rp. {{ number_format($gaji->bonus, 0) }}</td>
                                </tr>
                                      <tr>
                                    <td class="left">6</td>
                                    <td class="left">Potongan</td>
                                    <td class="left">(Rp. {{ number_format($gaji->Potongan, 0) }})</td>
                                </tr>
                                <tr>
                                    <td class="left">7</td>
                                    <td class="left font-bold">Total Diterima (3 + 4 + 5 - 6)</td>
                                    <td class="text-right font-bold">Rp. {{number_format($gaji->total)}}</td>
                                </tr>
                            </tbody>
                            {{-- <tfoot>
                                     <tr>
                                        <th colspan="7" class="text-right">Total Bayar</th>
                                        <th class="text-left">{{number_format($total)}}</th>
                                        <th class="text-left"></th>
                                     </tr>
                                </tfoot> --}}
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
