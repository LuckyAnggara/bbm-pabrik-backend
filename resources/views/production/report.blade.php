<!DOCTYPE html>
<html>

<head>
    <title>Larave Generate Invoice PDF - Nicesnippest.com</title>
</head>
<style type="text/css">
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

    .logo img {
        width: 45px;
        height: 45px;
        padding-top: 30px;
    }

    .logo span {
        margin-left: 8px;
        top: 19px;
        position: absolute;
        font-weight: bold;
        font-size: 25px;
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
</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Kertas Kerja Produksi</h1>

    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Production Sequeance Id - <span class="gray-color">{{$data['sequence']}}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Tanggal Order - <span class="gray-color">{{date("d F Y", strtotime($data['order_date']))}}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Status Produksi - <span class="gray-color">{{$data['status']}}</span></p>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Data Costumer</th>
                <th class="w-50">PIC</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p class="m-0 pt-5 text-bold w-100 mb-5">Nama - <span class="gray-color">{{strtoupper($data['customer_name'])}}</span></p>
                        <p class="m-0 pt-5 text-bold w-100">Alamat - <span class="gray-color">{{strtoupper($data['customer_name'])}}</span></p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p class="m-0 pt-5 text-bold w-100 mb-5">Sales / Penanggung Jawab - <span class="gray-color">{{strtoupper($data['pic_name'])}}</span></p>
                        <p class="m-0 pt-5 text-bold w-100 mb-5">Bagian Produksi - <span class="gray-color">{{strtoupper($data['pic_production'])}}</span></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <thead>
                <tr>
                    <th class="w-50">Bahan Baku</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                @foreach ($data['input'] as $key => $item)
                    <td>
                        <p>- {{$item['estimate_quantity']}} {{$item['item']['unit']['name']}} <b>{{ strtoupper($item['item']['name']) }}</b> </p>
                    </td>
                @endforeach
              </tr>
            </tbody>
        </table>

        <table class="table w-100 mt-10">
            <thead>
                <tr>
                    <th class="w-50">Hasil Produksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['output'] as $key => $item)
                 <tr>
                    <td>
                        <p>- {{$item['target_quantity']}} {{$item['item']['unit']['name']}} <b>{{ strtoupper($item['item']['name']) }}</b> </p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border mt-25"></div>
    <div class="mt-5">
        <div class="w-100 mt-10">
            <p class="m-0 pt-5 text-bold w-100">Tanggal Penyelesaian - <span class="gray-color">{{date("d F Y", strtotime($data['target_date']))}}</span></p>
            <p class="m-0 pt-5 text-bold w-100 mb-5">Catatan - <span class="gray-color">{{$data['notes']}}</span></p>
        </div>
    </div>

    <div class="border mt-25"></div>


    <p>Kertas kerja ini agar dilaksanakan sesuai dengan Prosedur yang berlaku</p>
    <div class="float-right mt-25 center w-50">
        <p class="mb-75">Maker</p>
        <p class="mt-25">{{$data['user']['name']}}</p>
    </div>




</html>