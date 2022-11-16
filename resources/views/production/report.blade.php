<!DOCTYPE html>
<html>

<head>
    <title>Laporan Produksi </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <div class="md:flex justify-between p-10">
        <div class="md:mb-0 mb-4 w-full mr-5">
            <div class="card card-compact w-full">
                <h2 class="text-3xl mb-5 px-2">Kertas Kerja Produksi</h2>
                <div class="card-body">
                    <div class="flex items-center justify-between mb-8 px-3">
                        <div>
                            <span class="text-2xl">Production Order #</span>{{$data['sequence']}}<br />
                            <span>Tanggal Oder</span>:
                            {{date("d F Y", strtotime($data['order_date']))}}<br />
                        </div>
                        <div class="text-right">
                            <span class="text-gray-600 text-4xl">BBM. </span>
                        </div>
                    </div>

                    <div class="flex px-3">
                        <div class="w-4/12 px-3 py-2">
                            <div class="text-left font-medium">Nama Pelanggan</div>
                        </div>
                        <div class="w-1/12 px-3 py-2">
                            <div class="text-left font-medium">:</div>
                        </div>
                        <div class="w-7/12 px-3 py-2">
                            <div class=" text-left font-medium">
                                {{strtoupper($data['customer_name'])}}
                            </div>
                        </div>
                    </div>

                    <div class="flex px-3">
                        <div class="w-4/12 px-3 py-2">
                            <div class="text-left font-medium">Penanggung Jawab</div>
                        </div>
                        <div class="w-1/12 px-3 py-2">
                            <div class="text-left font-medium">:</div>
                        </div>
                        <div class="w-7/12 px-3 py-2">
                            <div class="text-left font-medium">
                                {{strtoupper($data['pic_name'])}}
                            </div>
                        </div>
                    </div>

                    <div class="border border-t-2 border-gray-200 mb-5 px-3"></div>

                    <div class="flex px-3">
                        <div class="w-1/3 px-3 py-2">
                            <div class="text-left font-medium">
                                Bahan baku yang di pergunakan
                            </div>
                        </div>
                        <div class="w-2/3 px-3 border-solid border-2 border-black-500">
                            @foreach ($data['input'] as $key => $item)
                            <div class="flex justify-between my-2">
                                <div class="text-left font-medium w-1/12">-</div>
                                <div class="text-left font-medium w-7/12">
                                    {{ $item['item']['name'] }}
                                </div>
                                <div class="text-left font-medium w-4/12">
                                    {{$done_production ? $item['real_quantity'] : $item['estimate_quantity']}}
                                    {{ strtoupper($item['item']['unit']['name']) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex px-3">
                        <div class="w-1/3 px-3 py-2">
                            <div class="text-left font-medium">
                                Mesin yang di pergunakan
                            </div>
                        </div>
                        <div class="w-2/3 px-3 border-solid border-2 border-black-500">
                            @foreach ($data['machine'] as $key => $machine)
                            <div class="flex justify-between my-2">

                                <div class="text-left font-medium w-1/12">-</div>
                                <div class="text-left font-medium w-7/12">
                                    {{ $machine['machine']['name'] }}
                                </div>
                                <div class="text-left font-medium w-4/12">
                                    {{$machine['usage_meter']}}
                                    {{ strtoupper($machine['machine']['unit']) }}
                                </div>
                            </div>

                            @endforeach
                        </div>
                    </div>

                    <div class="flex px-3">
                        <div class="w-1/3 px-3 py-2">
                            <div class="text-left font-medium">Alat lainnya</div>
                        </div>
                        <div class="w-2/3 px-3 border-solid border-2 border-black-500">
                            @foreach ($data['overhead'] as $key => $overhead)
                            <div class="flex justify-between my-2">

                                <div class="text-left font-medium w-1/12">-</div>
                                <div class="text-left font-medium w-7/12">
                                    {{ $overhead['overhead']['name'] }}
                                </div>
                                <div class="text-left font-medium w-4/12">
                                    {{$overhead['usage_meter']}}
                                    {{ strtoupper($overhead['overhead']['unit']) }}
                                </div>
                            </div>

                            @endforeach
                        </div>
                    </div>

                    <div class="flex px-3">
                        <div class="w-1/3 px-3 py-2">
                            <div class="text-left font-medium">Output Produksi</div>
                        </div>
                        <div class="w-2/3 px-3 border-solid border-2 border-black-500">
                            @foreach ($data['output'] as $key => $item)
                            <div class="flex justify-between my-2">

                                <div class="text-left font-medium w-1/12">-</div>
                                <div class="text-left font-medium w-7/12">
                                    {{ $item['item']['name'] }}
                                </div>
                                <div class="text-left font-medium w-4/12">
                                    {{ $done_production ? $item['real_quantity'] : $item['target_quantity']}}
                                    {{ strtoupper($item['item']['name']) }}
                                </div>
                            </div>

                            @endforeach
                        </div>

                    </div>

                    <div class="border border-t-2 border-gray-200 mt-5 px-3"></div>

                    <div class="flex px-3">
                        <div class="w-4/12 px-3">
                            <div class="text-left font-medium">Target Produksi</div>
                        </div>
                        <div class="w-1/12 px-3">
                            <div class="text-left font-medium">:</div>
                        </div>
                        <div class="w-7/12 px-3">
                            <div class=" text-right font-medium">
                                {{date("d F Y", strtotime($data['target_date']))}}
                            </div>
                        </div>
                    </div>
                    <div class="flex px-3">
                        <div class="w-4/12 px-3">
                            <div class="text-left font-medium">Catatan</div>
                        </div>
                        <div class="w-1/12 px-3">
                            <div class="text-left font-medium">:</div>
                        </div>
                        <div class="w-7/12 px-3">
                            <div class=" text-right font-medium">
                                <p class="text-justify whitespace-pre-wrap ">
                                    {{ $data['notes'] }}
                                </p>
                            </div>
                        </div>
                    </div>



                    <div class="border border-t-2 border-gray-200 mb-5 px-3"></div>

                    <div class="mb-8 px-3">
                        <span>Kertas kerja ini agar dilaksanakan sesuai dengan Prosedur
                            yang berlaku.</span>
                    </div>

                    <div class="flex justify-between items-center mt-4 px-3">
                        <div class="text-1xl leading-none w-1/2 text-center">
                            <span class="">Maker</span>
                        </div>
                        <div class="text-1xl leading-none w-1/2 text-center">
                            <span class="">Bagian Produksi</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-12 px-3">
                        <div class="text-1xl leading-none w-1/2 text-center">
                            <span class="">{{$data['user']['name']}}</span>
                        </div>
                        <div class="text-1xl leading-none w-1/2 text-center">
                            <span class="">{{$pic_production ? $pic_production : '-'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




</html>