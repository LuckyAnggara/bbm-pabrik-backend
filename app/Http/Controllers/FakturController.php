<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\Item;
use App\Models\Mutation;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use stdClass;

class FakturController extends BaseController
{


    public function pembelian($id)
    {

        $result = Pembelian::where('id', $id)
            ->with(['detail.item.unit', 'user'])
            ->first();
        if ($result) {
            return view('faktur.pembelian', ['data' => $result, 'notes' => 'NOTES']);
        }
        return $this->sendError('Data not found');
    }

    public function penjualan($id)
    {

        $result = Penjualan::where('id', $id)
            ->with(['detail.item.unit', 'user','pelanggan'])
            ->first();
        if ($result) {
            return view('faktur.penjualan', ['data' => $result, 'notes' => 'NOTES']);
        }
        return $this->sendError('Data not found');
    }




    public function makeFaktur($id){
        $data = Penjualan::where('id', $id)
            ->with(['detail.item.unit', 'user','pelanggan'])
            ->first();
        // $tanggal_transaksi = $this->tgl_indo(date("Y-m-d-D", strtotime()));


        $pdf = new Fpdf('p', 'mm', 'letter');
        // membuat halaman baru
        $pdf->AddPage();
        $pdf->AddFont('Tahoma','B','tahomabd.php');
        $pdf->AddFont('Tahoma','','tahoma.php');
        // setting jenis font yang akan digunakan
        $pdf->SetFont('Tahoma', 'B', 11);
        // mencetak string
        $pdf->Cell(100, 6, 'Berkah Plastik Makmur', 0, 0, 'L');
        $pdf->Cell(96, 6, 'Faktur Penjualan', 0, 1, 'R');
        $pdf->SetFont('Tahoma', '', 8);
        $pdf->MultiCell(100, 5, nl2br('Jl. Raya Bandung Tasik Kp. Cipacing Ds Mekarsari Kec Cibatu'), 0, 'J');

        $pdf->Cell(100, 5, 'Telp : 085324884799', 0, 1, 'L');

        $pdf->Cell(196, 5, 'Email : bbmlimbangan@gmail.com', 0, 1, 'L');
        $pdf->Cell(196, 2,'', 'B', 1, 'L');
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(10, 3, '', 0, 1);

        $pdf->Cell(30, 5, 'Nama Pelanggan', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->pelanggan  != null? $data->pelanggan->name : $data->nama_pelanggan, 0, 0);
        $pdf->Cell(45);
        $pdf->Cell(20, 5, 'Nomor Faktur', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->nomor_faktur, 0, 1);

        $pdf->Cell(30, 5, 'Alamat', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->pelanggan ? $data->pelanggan->alamat : $data->alamat, 0, 0);
        $pdf->Cell(45);
        $pdf->Cell(20, 5, 'Tanggal Faktur', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->created_at->format('d F Y'), 0, 1);

        // header
        $pdf->Cell(196, 2,'', 'B', 1, 'L');
        $pdf->Cell(10, 5, '', 0, 1);
        $pdf->SetFont('Tahoma', 'B', 7);

        $pdf->Cell(7, 6, '#', 1, 0, 'C');
        $pdf->Cell(100, 6, 'Nama Item', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Qty', 1, 0, 'C');
        $pdf->Cell(20, 6, 'Harga', 1, 0, 'C');
        $pdf->Cell(35, 6, 'Total', 1, 1, 'C');
        $pdf->SetFont('Tahoma', '', 9);

        // foreach ($detail_order as $row){
        //     $pdf->Cell(20,6,$row->nim,1,0);
        //     $pdf->Cell(85,6,$row->nama_lengkap,1,0);
        //     $pdf->Cell(27,6,$row->no_hp,1,0);
        //     $pdf->Cell(25,6,$row->tanggal_lahir,1,1); 
        // }
        $no = 0;
        foreach ($data->detail as $key => $value) {
            
            $pdf->Cell(7, 5, $no, 1, 0, 'C');
            $pdf->Cell(100, 5, $value->item->name, 1, 0);
            $pdf->Cell(25, 5, $value->jumlah . ' ' .$value->item->unit->name  , 1, 0, 'C');
            $pdf->Cell(20, 5, number_format($value->harga), 1, 0, 'R');
            $pdf->Cell(35, 5, number_format($value->harga * $value->jumlah), 1, 0, 'R');
        }

        $pdf->Cell(10, 6, '', 0, 1);

        $pdf->SetFont('Tahoma', '', 8);
        $pdf->Cell(90, 5, 'Hormat Kami', 0, 0, 'C');
        $pdf->Cell(50, 5, '', 0, 0);
        $pdf->SetFont('Tahoma', '', 7);
        $pdf->Cell(30, 5, 'Sub Total', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(22, 5, 'Rp. '.number_format($data->sub_total), 0, 1, 'R');
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->SetFont('Tahoma', '', 8);
        $pdf->Cell(110);
        $pdf->SetFont('Tahoma', '', 7);
        $pdf->Cell(30, 5, 'Diskon', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(22, 5, '(Rp. '.number_format($data->diskon).')', 0, 1, 'R');
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->Cell(110);
        $pdf->SetFont('Tahoma', '', 7);
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(22, 5, '', 0, 1, 'R');
        $pdf->SetFont('Tahoma', '', 7);
        $pdf->Cell(90, 5,'(                             )',  0, 0, 'C');
        $pdf->Cell(50, 5, '', 0, 0);
        $pdf->Cell(30, 5, 'Ongkos Kirim', 'B', 0);
        $pdf->Cell(5, 5, ':', 'B', 0);
        $pdf->Cell(22, 5,'Rp. '.number_format($data->pajak),  'B', 1, 'R');
        $pdf->Cell(30, 5, '', 0, 0);
        $pdf->SetFont('Tahoma', 'B', 9);
        $pdf->Cell(110);
        $pdf->Cell(30, 5, 'Grand Total', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(22, 5, 'Rp. '.number_format($data->total),   0, 1, 'R');

        $pdf->SetFont('Tahoma', 'B', 7);
        $pdf->Cell(196, 3, '', 'B', 1);
        $pdf->Cell(10, 2, '', 0, 1);

        // $pdf->Cell(30, 7, 'Note :', 0, 0);
        // $pdf->MultiCell(90, 4,'asdasdsad', 0, 'J');
        // $pdf->Cell(90,6,nl2br($setting_perusahaan['catatan_faktur_cash']),1,0);
        
        $pdf->Output();
    }

    function makeSuratJalan($id)
        {
         $data = Penjualan::where('id', $id)
            ->with(['detail.item.unit', 'user','pelanggan'])
            ->first();
        // $tanggal_transaksi = $this->tgl_indo(date("Y-m-d-D", strtotime()));


              $pdf = new Fpdf('p', 'mm', 'letter');
        // membuat halaman baru
        $pdf->AddPage();
        $pdf->AddFont('Tahoma','B','tahomabd.php');
        $pdf->AddFont('Tahoma','','tahoma.php');
        // setting jenis font yang akan digunakan
        $pdf->SetFont('Tahoma', 'B', 11);
        // mencetak string
        $pdf->Cell(100, 6, 'Berkah Plastik Makmur', 0, 0, 'L');
        $pdf->Cell(96, 6, 'Surat Jalan', 0, 1, 'R');
        $pdf->SetFont('Tahoma', '', 8);
        $pdf->MultiCell(100, 5, nl2br('Jl. Raya Bandung Tasik Kp. Cipacing Ds Mekarsari Kec Cibatu'), 0, 'J');

        $pdf->Cell(100, 5, 'Telp : 085324884799', 0, 1, 'L');

        $pdf->Cell(196, 5, 'Email : bbmlimbangan@gmail.com', 0, 1, 'L');
        $pdf->Cell(196, 2,'', 'B', 1, 'L');
        // Memberikan space kebawah agar tidak terlalu rapat
        
            $pdf->Cell(10, 3, '', 0, 1);

            $pdf->Cell(30, 5, 'Nama Pelanggan', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->pelanggan  != null? $data->pelanggan->name : $data->nama_pelanggan, 0, 0);
        $pdf->Cell(45);
        $pdf->Cell(25, 5, 'Nomor Faktur', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->nomor_faktur, 0, 1);

        $pdf->Cell(30, 5, 'Alamat', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->pelanggan ? $data->pelanggan->alamat : $data->alamat, 0, 0);
        $pdf->Cell(45);
        $pdf->Cell(25, 5, 'Tanggal Faktur', 0, 0);
        $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->Cell(50, 5, $data->created_at->format('d F Y'), 0, 1);

            $pdf->Cell(30, 5, '', 0, 0);
            $pdf->Cell(5, 5, '', 0, 0);
            $pdf->Cell(50, 5, '', 0, 0);
            $pdf->Cell(45);
            $pdf->Cell(25, 5, 'No Surat Jalan', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(50, 5, $data->nomor_faktur, 0, 1);

            $pdf->Cell(30, 5, '', 0, 0);
            $pdf->Cell(5, 5, '', 0, 0);
            $pdf->Cell(50, 5, '', 0, 0);
            $pdf->Cell(45);
            $pdf->Cell(25, 5, 'No Polisi', 0, 0);
            $pdf->Cell(5, 5, ':', 0, 0);
            $pdf->Cell(50, 5, '', 0, 1);


            // header
            $pdf->Cell(196, 2, '', 'B', 1, 'L');
            $pdf->Cell(10, 5, '', 0, 1);
            $pdf->SetFont('Tahoma', '', 9);

            $pdf->Cell(15, 6, 'Jumlah', 1, 0, 'C');
            $pdf->Cell(15, 6, 'Satuan', 1, 0, 'C');
            $pdf->Cell(65, 6, 'Nama Barang', 1, 0, 'C');
            $pdf->Cell(100, 6, 'Keterangan', 1, 1, 'C');
            $pdf->SetFont('Tahoma', '', 9);

            // foreach ($detail_order as $row){
            //     $pdf->Cell(20,6,$row->nim,1,0);
            //     $pdf->Cell(85,6,$row->nama_lengkap,1,0);
            //     $pdf->Cell(27,6,$row->no_hp,1,0);
            //     $pdf->Cell(25,6,$row->tanggal_lahir,1,1); 
            // }
           foreach ($data->detail as $key => $value) {

                $pdf->Cell(15, 5, $value->jumlah, 1, 0, 'C');
                $pdf->Cell(15, 5, $value->item->unit->name , 1, 0, 'C');
                $pdf->Cell(65, 5, $value->item->name, 1, 0);
                $pdf->Cell(100, 5, '', 'R', 1);
            }


            $pdf->Cell(195, 5, '', 'T', 1);


            $pdf->Cell(10, 6, '', 0, 1);
            $pdf->SetFont('Tahoma', '', 8);
            // $pdf->Cell(50, 5, 'Sales', 0, 0, 'C');
            $pdf->Cell(50, 5, 'Supir', 0, 0, 'C');
            $pdf->Cell(95, 5, 'Gudang', 0, 0, 'C');
            $pdf->Cell(50, 5, 'Diterima Oleh', 0, 1, 'C');
            $pdf->Cell(110, 5, '', 0, 1);
            $pdf->Cell(110, 5, '', 0, 1);
            $pdf->Cell(110, 5, '', 0, 1);
            // $pdf->Cell(50, 5, '(                         )', 0, 0, 'C');
            $pdf->Cell(50, 5, '(                                 )', 0, 0, 'C');
            $pdf->Cell(95, 5, '(                                 )', 0, 0, 'C');
            $pdf->Cell(50, 5, '(                                 )', 0, 0, 'C');

        $pdf->Output();
    }
}