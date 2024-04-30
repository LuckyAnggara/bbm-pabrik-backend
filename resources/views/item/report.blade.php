<!DOCTYPE html>
<html lang="en">


{{-- <head>
    <title>Items Report</title>
</head>
<style type="text/css">
 
</style> --}}

<head>
    <title>Laporan Persediaan </title>
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

           body {
        font-family: 'Roboto Condensed', sans-serif;
    }

    .m-0 {
        margin: 0px;
    }

    .mb-5 {
        margin-bottom: 5px;
    }

    .p-0 {
        padding: 0px;
    }

    .pt-5 {
        padding-top: 5px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .mt-25 {
        margin-top: 25px;
    }

    .mb-25 {
        margin-bottom: 25px;
    }

    .mb-75 {
        margin-bottom: 75px;
    }

    .mx-auto {
        margin: auto;
    }

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
    }

    .w-50 {
        width: 50%;
    }

    .w-85 {
        width: 85%;
    }

    .w-15 {
        width: 15%;
    }


    .gray-color {
        color: #5D5D5D;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    table tr,
    th,
    td {
        border: 1px solid #d2d2d2;
        border-collapse: collapse;
        padding: 7px 8px;
    }

    table tr th {
        background: #F4F4F4;
        font-size: 15px;
    }

    table tr td {
        font-size: 13px;
    }

    table {
        border-collapse: collapse;
    }

    .box-text p {
        line-height: 10px;
    }

    .float-left {
        float: left;
    }

    .float-right {
        float: right;
    }

    .center {
        text-align: center;
    }

    .total-part {
        font-size: 16px;
        line-height: 12px;
    }

    .total-right p {
        padding-right: 20px;
    }

    .item-center {
        align-items: center;
    }
    </style>

</head>

<body>
        <div class="container-fluid mt-5">
            <div>
                <div class="card">
                    <div class="card-header">Laporan Persediaan <span class="gray-color">{{ $from_date }} s.d {{ $to_date }}</span>
              <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                            <i class="fa fa-print"></i> Print</a>
                        <!-- <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                            <i class="fa fa-save"></i> Save</a> -->
                    </div>
   
   

    <div class="mx-auto table-section bill-tb w-100">
        <table class="table w-100 ">
            <thead>
                <tr>
                    <th class="center" style="width:5%">No</th>
                    @if ($warehouseShow === true)
                    <th style="width:35%">Nama</th>
                    {{-- <th style="width:15%">Gudang</th> --}}
                    @else
                    <th style="width:50%">Nama</th>
                    @endif
                    <th style="width:15%">Unit / Satuan</th>
                    <th style="width:15%">Tipe</th>

                    <th style="width:15%">Saldo Persediaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $item)
                <tr>
                    <td class="center">{{$key+1}}</td>
                    <td>{{strtoupper($item->name)}}</td>
                    {{-- @if ($warehouseShow === true)
                    <td>{{strtoupper($item->warehouse->name)}}</td>
                    @endif --}}
                    <td>{{strtoupper($item->type->name)}}</td>
                    <td>{{strtoupper($item->unit->name)}}</td>
                    <td>{{number_format($item->balance)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

                </div>
            </div>
        </div>

</body>

</html>