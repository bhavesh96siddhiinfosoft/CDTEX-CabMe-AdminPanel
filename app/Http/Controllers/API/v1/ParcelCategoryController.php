<?php

namespace App\Http\Controllers\API\v1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ParcelCategory;
use App\Models\ParcelDeliveryCharges;
use App\Models\Zone;
use Illuminate\Http\Request;
use Validator;
use DB;

class ParcelCategoryController extends Controller
{
  public function __construct()
  {
    $this->limit = 20;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function getData(Request $request)
  {
    $output = [];
    $parcelCategory = ParcelCategory::where('status', '=', 'yes')->get();
    if (count($parcelCategory) > 0) {
      foreach ($parcelCategory as $row) {
        $row->id = (string)$row->id;
        if ($row->image != '') {
          if (file_exists(public_path('assets/images/parcel_category' . '/' . $row->image))) {
            $row->image = asset('assets/images/parcel_category') . '/' . $row->image;
          } else {
            $row->image = asset('assets/images/placeholder_image.jpg');
          }
        }
        $output[] = $row;
      }
      //$output[]=$row;
      if (!empty($output)) {
        $response['success'] = 'success';
        $response['error'] = null;
        $response['message'] = 'Parcel Category fetch successful';
        $response['data'] = $output;
      } else {
        $response['success'] = 'Failed';
        $response['error'] = 'Error while fetch data';
      }
    } else {
      $response['success'] = 'Failed';
      $response['error'] = 'No Data Found';
      $response['message'] = null;
    }
    return response()->json($response);
  }

  /**
   * List parcel categories with zone-based base fare per row.
   */
  public function getParcelCategoryWithBaseFare(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'lat_source'  => 'required|numeric',
      'lng_source'  => 'required|numeric',
      'distance'        => 'required|numeric|min:0',
      'parcel_weight'   => 'nullable|numeric|min:0',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => 'Failed',
        'error'   => $validator->errors()->first(),
        'message' => $validator->errors()->first(),
        'data'    => null,
      ]);
    }

    $pickupLat = (float) $request->get('lat_source');
    $pickupLng = (float) $request->get('lng_source');
    $distance  = (float) $request->get('distance');
    $weight    = (float) $request->get('parcel_weight', 0);

    $zoneId   = Helper::getUserZoneId($pickupLat, $pickupLng);
    $zoneName = $zoneId ? optional(Zone::find($zoneId))->name : null;

    $charge = null;
    if ($zoneId) {
      $charge = ParcelDeliveryCharges::where('zone_id', $zoneId)->first();
    }
    if (!$charge) {
      $charge = ParcelDeliveryCharges::whereNull('zone_id')->first();
    }

    if ($charge) {
      $perKm = (float) $charge->delivery_charge_parcel;
      $perKg = (float) $charge->parcel_per_weight_charge;
      $baseFare = ($distance * $perKm) + ($weight * $perKg);
      $baseFare = number_format($baseFare, 2, '.', '');
      $pricingSource = $charge->zone_id ? 'zone' : 'default';
    } else {
      $baseFare = '';
      $pricingSource = 'none';
    }

    $categories = ParcelCategory::where('status', 'yes')->get();
    $output = [];
    foreach ($categories as $row) {
      $row->id = (string) $row->id;
      if (!empty($row->image) && file_exists(public_path('assets/images/parcel_category/' . $row->image))) {
        $row->image = asset('assets/images/parcel_category/' . $row->image);
      } else {
        $row->image = asset('assets/images/placeholder_image.jpg');
      }

      $row->base_fare         = (string) $baseFare;
      $row->pricing_source    = $pricingSource;
      $row->matched_zone_id   = $zoneId !== null ? (string) $zoneId : '';
      $row->matched_zone_name = $zoneName ?? '';
      $row->per_km_charge     = $charge ? (string) $charge->delivery_charge_parcel : '';
      $row->per_kg_charge     = $charge ? (string) $charge->parcel_per_weight_charge : '';
      $output[] = $row;
    }

    return response()->json([
      'success' => 'Success',
      'error'   => null,
      'message' => 'Successfully fetch data',
      'data'    => $output,
    ]);
  }
}
