<!DOCTYPE html>
<html>

<head>
    <title>Laporan Produksi </title>
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
        <div class="card">
            <div class="card-header">Kertas Kerja Produksi
                <strong>#{{ $data['sequence'] }}</strong>
                <a class="btn btn-sm btn-secondary d-print-none float-right mr-1" href="#"
                    onclick="javascript:window.print();" data-abc="true">
                    <i class="fa fa-print"></i> Print</a>
                <!-- <a class="btn btn-sm btn-info d-print-none float-right mr-1" href="#" data-abc="true">
                            <i class="fa fa-save"></i> Save</a> -->
            </div>
            <div class="card-body">
                <div class="justify-between md:flex">
                    <div class="mb-4 w-full px-6 md:mb-0">
                        <h2 class="mb-5 text-2xl">Kertas Kerja Produksi</h2>

                        <div class="mb-8 flex items-center justify-between">
                            <div>
                                <span class="text-xl">Production Order #</span>{{ $data['sequence'] }}<br />
                                <span>Tanggal Oder</span>:
                                {{ date('d F Y', strtotime($data['order_date'])) }}<br />
                            </div>

                        </div>

                        <div class="flex">
                            <div class="w-4/12 py-2">
                                <div class="text-left font-medium">Shift</div>
                            </div>
                            {{-- <div class="w-1/12 py-2">
                                <div class="text-left font-medium">:</div>
                            </div> --}}
                            <div class="w-7/12 py-2">
                                <div class="text-left font-medium">
                                  :  {{ strtoupper($data['shift']) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex">
                            <div class="w-4/12 py-2">
                                <div class="text-left font-medium">Penanggung Jawab</div>
                            </div>
                            {{-- <div class="w-1/12 py-2">
                                <div class="text-left font-medium">:</div>
                            </div> --}}
                            <div class="w-7/12 py-2">
                                <div class="text-left font-medium">
                                   :  {{ strtoupper($data['pic_name']) }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-5 border border-t-2 border-gray-200"></div>

                        <div class="flex">
                            <div class="w-1/3 py-2">
                                <div class="text-left font-medium">
                                    Bahan baku yang di pergunakan
                                </div>
                            </div>
                            <div class="border-black-500 w-2/3 border-2 border-solid">
                                @foreach ($data['input'] as $key => $item)
                                    <div class="my-1 ml-4 flex justify-between">
                                        <ul class="list-disc">
                                            <li class="text-left font-medium">
                                                <div class="flex">
                                                    <span class="w-96">
                                                        {{ $item['item']['name'] }}
                                                    </span>
                                                    <span class="w-fit">
                                                        {{ $done_production ? $item['real_quantity'] : $item['estimate_quantity'] }}
                                                    </span>
                                               <span class="ml-2">
                                                        {{ strtoupper($item['item']['unit']['name']) }}
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex">
                            <div class="w-1/3 py-2">
                                <div class="text-left font-medium">
                                    Mesin yang di pergunakan
                                </div>
                            </div>
                            <div class="border-black-500 w-2/3 border-2 border-solid">
                                @foreach ($data['machine'] as $key => $machine)
                                    <div class="my-1 ml-4 flex justify-between">
                                        <ul class="list-disc">
                                            <li class="text-left font-medium">
                                                <div class="flex">
                                                    <span class="w-96">
                                                        {{ $machine['machine']['name'] }}
                                                    </span>
                                                    <span class="w-fit">
                                                        {{ $machine['usage_meter'] }}
                                                    </span>
                                                     <span class="ml-2">
                                                        {{ strtoupper($machine['machine']['unit']) }}

                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex">
                            <div class="w-1/3 py-2">
                                <div class="text-left font-medium">Alat lainnya</div>
                            </div>
                            <div class="border-black-500 w-2/3 border-2 border-solid">
                                @foreach ($data['overhead'] as $key => $overhead)
                                    <div class="my-1 ml-4 flex justify-between">
                                        <ul class="list-disc">
                                            <li class="text-left font-medium">
                                                <div class="flex">
                                                    <span class="w-96">
                                                        {{ $overhead['overhead']['name'] }}
                                                    </span>
                                                     <span class="w-fit">
                                                        {{ $overhead['usage_meter'] }}
                                                    </span>
                                                    <span class="ml-2">
                                                        {{ strtoupper($overhead['overhead']['unit']) }}
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="flex">
                            <div class="w-1/3 py-2">
                                <div class="text-left font-medium">Pegawai / Operator</div>
                            </div>
                            <div class="border-black-500 w-2/3 border-2 border-solid">
                                @foreach ($data['pegawai'] as $key => $pegawai)
                                    <div class="my-1 ml-4 flex justify-between">
                                        <ul class="list-disc">
                                            <li class="text-left font-medium">
                                                {{ $pegawai['pegawai']['name'] }}
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="flex">
                            <div class="w-1/3 py-2">
                                <div class="text-left font-medium">Output Produksi</div>
                            </div>
                            <div class="border-black-500 w-2/3 border-2 border-solid">
                                @foreach ($data['output'] as $key => $item)
                                    <div class="my-1 ml-4 flex justify-between">
                                        <ul class="list-disc">
                                            <li class="text-left font-medium">
                                                <div class="flex">
                                                    <span class="w-96">
                                                        {{ $item['item']['name'] }}
                                                    </span>
                                                    <span class="w-fit">
                                                        {{ $done_production ? $item['real_quantity'] : $item['target_quantity'] }}
                                                    </span>
                                                    <span class="ml-2">
                                                        {{ strtoupper($item['item']['name']) }}
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="mt-5 border border-t-2 border-gray-200"></div>

                        <div class="flex">
                            <div class="w-4/12">
                                <div class="text-left font-medium">Target Produksi</div>
                            </div>
                            {{-- <div class="w-1/12">
                                <div class="text-left font-medium">:</div>
                            </div> --}}
                            <div class="w-7/12">
                                <div class="text-left font-medium">
                                   : {{ date('d F Y', strtotime($data['target_date'])) }}
                                </div>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="w-4/12">
                                <div class="text-left font-medium">Catatan</div>
                            </div>
                            {{-- <div class="w-1/12">
                                <div class="text-left font-medium">:</div>
                            </div> --}}
                            <div class="w-7/12">
                                <div class="text-left font-medium">
                                    : {{ $data['notes'] }}
                                </div>
                            </div>
                        </div>



                        <div class="mb-5 border border-t-2 border-gray-200"></div>

                        <div class="mb-8">
                            <span>Kertas kerja ini agar dilaksanakan sesuai dengan Prosedur
                                yang berlaku.</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-1xl w-1/2 text-center leading-none">
                                <span class="">Maker</span>
                            </div>
                            <div class="text-1xl w-1/2 text-center leading-none">
                                <span class="">Bagian Produksi</span>
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-between">
                            <div class="text-1xl w-1/2 text-center leading-none">
                                <span class="">{{ $data['user']['name'] }}</span>
                            </div>
                            <div class="text-1xl w-1/2 text-center leading-none">
                                <span class="">{{ $pic_production ? $pic_production : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</html>
