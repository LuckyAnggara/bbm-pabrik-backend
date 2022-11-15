<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public static function store($data, $is_po)
    {
        if ($is_po == true) {
            $shipping = Shipping::create([
                'is_po' => $is_po,
                'production_order_id' => $data['id'],
                'driver_name' => $data['data']['driver_name'],
                'police_number' => $data['data']['police_number'],
                'man_power_name' => $data['data']['man_power_name'],
                'shipping_date' => $data['data']['shipping_date'],
            ]);
        } else {
            $shipping = Shipping::create([
                'is_po' => $is_po,
                'master_exit_item_id' => $data['master_exit_item_id'],
                'driver_name' => $data['driver_name'],
                'police_number' => $data['police_number'],
                'man_power_name' => $data['man_power_name'],
                'shipping_date' => $data['shipping_date'],
            ]);
        }
        if ($shipping) {
            return $shipping;
        } else {
            return null;
        }
    }
}
