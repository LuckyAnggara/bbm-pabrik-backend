
             

             <!DOCTYPE html>
<html>

<head>
    <title>Faktur Produksi </title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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
            <div class="card">
                    <div class="card-header">Nomor Faktur
                        <strong>#{{$data->nomor_faktur}}</strong>
                        <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                            <i class="fa fa-print"></i> Print</a>
                        <!-- <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                            <i class="fa fa-save"></i> Save</a> -->
                    </div>
     <div class="container-fluid customFont">
            
                 <footer class="content-page">
                     <div class="row">
                         <div class="col-md-12">
                             <div class="card-box">
                                 <div class="panel-body">
                                     <div class="clearfix">
                                         <div class="widget-user row">
                                             <div class="col-9">
                                                 <div class="wid-u-info col-9">
                                                     <ul style="list-style-type:none">
                                                         <li>
                                                             <h3><strong>Berkah Baja Makmur</strong>
                                                             </h3>
                                                         <li>
                                                     </ul>
                                                     <ul style="list-style-type:none">
                                                         {{-- <li class="text-left"><?= nl2br($setting_perusahaan['alamat_perusahaan']); ?><br></li>
                                                         <li class="text-left" title=" Phone"> Telp : <?= $setting_perusahaan['nomor_telepon']; ?> / Fax : <?= $setting_perusahaan['nomor_fax']; ?></li>
                                                         <li class="text-left" title="Email"> Email : <?= $setting_perusahaan['alamat_email']; ?></li> --}}
                                                     </ul>
                                                 </div>
                                             </div>
                                             <div class="col-3 text-right">
                                                 <h4><b>Faktur Penjualan</b></h4>
                                                 <p>15 Januari 2024</p>
                                             </div>
                                         </div>

                                     </div>
                                     <hr>
                                     <div class="row">
                                         <div class="col-6">
                                             <div class="clearfix row m-t-10">
                                                 <ul class="col-3" style="list-style-type:none">
                                                     <li class="text-left m-b-3">Nama Pelanggan</li>
                                                     <li class="text-left m-b-3">Alamat</li>
                                                 </ul>
                                                 <ul class="col-1" style="list-style-type:none">
                                                     <li class="text-center m-b-3"> : </li>
                                                     <li class="text-center m-b-3"> : </li>
                                                 </ul>
                                                 <ul class="col-8" style="list-style-type:none">
                                                     <li class="text-left m-b-3">Jhon</li>
                                                     <li class="text-left m-b-3">adkjaskjdlkajsdaks dlakdjslkadjs alkdja lkdsasd</li>
                                                 </ul>
                                             </div>
                                             <!-- end row -->
                                         </div>
                                         <div class="col-2">
                                         </div>
                                         <div class="col-4">
                                             <div class="clearfix row m-t-10">
                                                 <ul class="col-4" style="list-style-type:none">
                                                     <li class="text-left m-b-3">Nomor Faktur</li>
                                                 </ul>
                                                 <ul class="col-1" style="list-style-type:none">
                                                     <li class="text-left m-b-3"> : </li>
                                                 </ul>
                                                 <ul class="col-7" style="list-style-type:none">
                                                     <li class="text-left m-b-3">#123123123</li>
                                                 </ul>
                                             </div>
                                             <!-- end row -->
                                         </div>
                                     </div>


                                     <div class="m-h-10"></div>

                                     <div class="row">
                                         <div class="col-md-12">
                                             <div class="table-responsive">
                                                 <table class="table table-bordered">
                                                     <thead class="">
                                                         <tr>
                                                             <th style="width: 5%">#</th>
                                                             <th style="width: 30%">Nama Item</th>
                                                             <th style="width: 5%">Satuan</th>
                                                             <th style="width: 5%">Quantity</th>
                                                             <th style="width: 10%">Harga</th>
                                                             <th style="width: 25%">Total</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                    @foreach ($data->detail as $key => $item)
                                    <tr>
                                                                <td class="center">1</td>
                                        <td class="text-left">{{$item->item->name}}</td>
                                        <td class="text-left">{{$item->item->unit->name}}</td>
                                        <td class="text-left">{{$item->jumlah}}</td>
                                        <td class="text-right">{{number_format($item->harga)}}</td>
                                        <td class="text-right">{{number_format($item->harga * $item->jumlah)}}</td>
                                        
                    
                                                          </tr>
                                    @endforeach
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="row">
                                         <div class="col-6">
                                             {{-- <div class="clearfix m-t-10">
                                                 <h4 class="text-inverse"><b><u>Note</u></b></h4>
                                                 <p>
                                                    asdasdasdasdasdasdsad
                                                 </p>
                                             </div> --}}
                                         </div>
                                         <div class="col-6">
                                             <div class="clearfix row m-t-10">
                                                 <ul class="col-5" style="list-style-type:none">
                                                     <li class="text-right m-b-3"><b>Subtotal</b></li>
                                                     <li class="text-right m-b-3">Diskon</li>
                                                     <li class="text-right m-b-3">Pajak (PPN 11%)</li>
                                                
                                                 </ul>
                                                 <ul class="col-1" style="list-style-type:none">
                                                     <li class="text-right m-b-3"><b> : </b></li>
                                                     <li class="text-right m-b-3"> : </li>
                                                     <li class="text-right m-b-3"> : </li>
                                                 </ul>
                                                 <ul class="col-4" style="list-style-type:none">
                                                     <li class="text-right m-b-3"><b>Rp. {{number_format($data->sub_total)}}</b></li>
                                                     <li class="text-right m-b-3">Rp. {{number_format($data->diskon)}}</li>
                                                     <li class="text-right m-b-3">Rp. {{number_format($data->pajak)}}</li>
                                                 </ul>
                                             </div>
                                             <hr>
                                             <div class="clearfix row">
                                                 <ul class="col-5" style="list-style-type:none">
                                                     <li class="text-right">
                                                         <b>Grand-Total</b>
                                                     </li>
                                                 </ul>
                                                 <ul class="col-1" style="list-style-type:none">
                                                     <li class="text-right">
                                                         <b> : </b>
                                                     </li>
                                                 </ul>
                                                 <ul class="col-4" style="list-style-type:none">
                                                     <li class="text-right">
                                                         <b>Rp. {{number_format($data->total)}}</b>
                                                     </li>

                                                 </ul>
                                             </div>
                                         </div>
                                     </div>
                                     <hr>
                                     <div class="row text-center">
                                         <div class="col-6">
                                             <p>Hormat Kami</p> <br>
                                             <p>asdasdasdsd</p>
                                         </div>
                                   
                                     </div>
                                     <hr>
                                     <div class="d-print-none">
                                         <div class="pull-right">
                                             <a id="print_lx" class="btn btn-inverse waves-effect waves-light" type="button"><i class="fa fa-print"></i> Lx Print </a>
                                             <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                                         </div>
                                         <div class="clearfix"></div>
                                     </div>
                                     </p>
                                 </div>
                             </div>
                         </div>
                         <!-- end row -->
                     </div> <!-- container -->
                 </footer>
             </div> <!-- end container -->
            </div>
</body>
</html>
            