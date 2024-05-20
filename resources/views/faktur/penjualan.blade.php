<!DOCTYPE html>
<html>

<head>
    <title>Laporan Produksi </title>
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
                <div class="card-header">Nomor Faktur
                    <strong>#{{$data->nomor_faktur}}</strong>
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="{{url('/api/faktur/print/penjualan/'.$data->id)}}" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                    <!-- href="{{url('/api/faktur/print/suratjalan/'.$data->id)}}" data-abc="true" -->
                    <a data-toggle="modal" data-target="#staticBackdrop" class="btn btn-sm btn-info float-right mr-1 d-print-none">
                        <i class="fa fa-save"></i> Surat Jalan</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-4">
                            <h6 class="mb-3">Dari:</h6>

                            <div>
                                <strong>Berkah Baja Makmur</strong>
                            </div>
                            <!-- <div>42, Awesome Enclave</div>
                                <div>New York City, New york, 10394</div>
                                <div>Email: admin@bbbootstrap.com</div>
                                <div>Phone: +48 123 456 789</div> -->

                        </div>

                        <div class="col-sm-4">
                            <h6 class="mb-3">To:</h6>
                            <div>
                                <strong>{{$data->pelanggan ? $data->pelanggan->name : $data->nama_pelanggan}}</strong>
                            </div>
                            <div>{{$data->pelanggan ? $data->pelanggan->alamat : $data->alamat}}</div>
                            <div>Phone: {{$data->pelanggan ? $data->pelanggan->nomor_telepon : $data->nomor_telepon}}</div>
                        </div>

                        <div class="col-sm-4">
                            <h6 class="mb-3">Nomor Faktur:</h6>
                            <div>Invoice
                                <strong>#{{$data->nomor_faktur}}</strong>
                            </div>
                            <div>{{$data->created_at->format('d F Y')}}</div>
                            <!-- <div>VAT: NYC09090390</div>
                                <div>Account Name: BBBootstrap Inc</div>
                                <div>
                                    <strong>SWIFT code: 99 8888 7777 6666 5555</strong>
                                </div> -->
                        </div>

                    </div>

                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="center">#</th>
                                    <th>Nama Item</th>
                                    <th class="center">Quantity</th>
                                    <th class="right">Harga</th>
                                    <th class="right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->detail as $key => $item)
                                <tr>
                                    <td class="center">1</td>
                                    <td class="left">{{$item->item->name}}</td>
                                    <td class="left">{{$item->jumlah}} {{$item->item->unit->name}}</td>
                                    <td class="center">{{number_format($item->harga)}}</td>
                                    <td class="right">{{number_format($item->harga * $item->jumlah)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-center font-bold" colspan="2">TOTAL</td>
                                    <td class="center">{{$total_qty}} KG</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-5">{{$notes}}</div>
                        <div class="col-lg-4 col-sm-5 ml-auto">
                            <table class="table table-clear">
                                <tbody>
                                    <tr>
                                        <td class="left">
                                            <strong>Subtotal</strong>
                                        </td>
                                        <td class="right">Rp. {{number_format($data->sub_total)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Diskon</strong>
                                        </td>
                                        <td class="right">Rp. {{number_format($data->diskon)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Pajak</strong>
                                        </td>
                                        <td class="right">Rp. {{number_format($data->pajak)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Ongkos Kirim</strong>
                                        </td>
                                        <td class="right">Rp. {{number_format($data->ongkir)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="right">
                                            <strong>Rp. {{number_format($data->total)}}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{url('/api/faktur/print/suratjalan/'.$data->id)}}" method="get">

                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Print Surat Jalan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Masukan Nomor Kendaraan</label>
                            <input type="text" class="form-control" name="nomor_kendaraan" aria-describedby="emailHelp">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="cancel" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Print</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>