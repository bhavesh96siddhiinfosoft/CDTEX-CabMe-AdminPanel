<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCharges;
use App\Models\RentalVehicleType;
use App\Models\Settings;
use App\Models\VehicleType;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;

class VehicleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        if ($request->has('search') && $request->search != '' && $request->selected_search == 'libelle') {

            $search = $request->input('search');
            $types = DB::table('type_vehicule')
                ->where('type_vehicule.libelle', 'LIKE', '%' . $search . '%')
                ->where('type_vehicule.deleted_at', '=', NULL);
            $totalLength = count($types->get()); 
            $types = $types->paginate($perPage)->appends($request->all());


        } elseif ($request->has('search') && $request->search != '' && $request->selected_search == 'prix') {

            $search = $request->input('search');
            $types = DB::table('type_vehicule')
                ->where('type_vehicule.prix', 'LIKE', '%' . $search . '%')
                ->where('type_vehicule.deleted_at', '=', NULL);
            $totalLength = count($types->get());    
            $types = $types->paginate($perPage)->appends($request->all());
                


        } else {
            $totalLength = count(VehicleType::get());
            $types = VehicleType::paginate($perPage)->appends($request->all());

        }

        return view("vehicle.index",compact('types','totalLength','perPage'));
    }

    public function create()
    {
        $vehicle = VehicleType::all();
        $Settings = Settings::all();
        $zones = Zone::where('status', 'yes')->get();

        foreach ($Settings as $data)
            $delivery_distance = $data->delivery_distance;

        return view('vehicle.create', compact('vehicle', 'zones'))->with('delivery_distance', $delivery_distance);
    }

    public function store(Request $request)
    {
        if ($request->id > 0) {
            $image_validation = "mimes:jpeg,jpg,png";
        } else {
            $image_validation = "required|mimes:jpeg,jpg,png";
        }

        $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'image' => $image_validation,
            'charges.default.per_km' => 'required|numeric|min:0',
            'charges.default.minimum' => 'required|numeric|min:0',
            'charges.default.within_km' => 'required|numeric|min:0',
        ], [
            'libelle.required' => trans('lang.the_vehicle_type_field_is_required'),
            'image.required' => trans('lang.the_image_field_is_required'),
            'charges.default.per_km.required' => trans('lang.delivery_charges_per_miles_is_required'),
            'charges.default.minimum.required' => trans('lang.minimum_delivery_charges_is_required'),
            'charges.default.within_km.required' => trans('lang.minimum_delivery_charges_within_miles_is_required'),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $vehicle = new VehicleType;
        $vehicle->libelle = $request->input('libelle');
        $vehicle->status = !empty($request->input('status')) ? 'Yes' : 'No';

        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extenstion = $file->getClientOriginalExtension();
            $time = time() . '.' . $extenstion;
            $filename = 'image_vehicleType' . $time;
            $compressedImage = Helper::compressFile($file->getPathName(), public_path('assets/images/type_vehicle').'/'.$filename, 8);
            $vehicle->image = $filename;
        }
        $vehicle->creer = date('Y-m-d H:i:s');
        $vehicle->modifier = date('Y-m-d H:i:s');
        $vehicle->updated_at = date('Y-m-d H:i:s');
        $vehicle->save();

        $this->saveZoneCharges($vehicle->id, $request->input('charges', []));

        return redirect('vehicle-type')->with('message', trans('lang.vehicle_type_created_successfully'));
    }

    public function edit($id)
    {
        $type = VehicleType::find($id);
        $zones = Zone::where('status', 'yes')->get();

        $charges = DeliveryCharges::where('id_vehicle_type', $id)->get()
            ->keyBy(fn ($r) => $r->zone_id ?: 'default');

        $Settings = Settings::all();
        foreach ($Settings as $data)
            $delivery_distance = $data->delivery_distance;

        return view("vehicle.edit")
            ->with("type", $type)
            ->with('charges', $charges)
            ->with('zones', $zones)
            ->with('delivery_distance', $delivery_distance);
    }

    public function update(Request $request, $id)
    {
        if ($request->id > 0) {
            $image_validation = "mimes:jpeg,jpg,png";
        } else {
            $image_validation = "required|mimes:jpeg,jpg,png";
        }

        $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'image' => $image_validation,
            'charges.default.per_km' => 'required|numeric|min:0',
            'charges.default.minimum' => 'required|numeric|min:0',
            'charges.default.within_km' => 'required|numeric|min:0',
        ], [
            'libelle.required' => trans('lang.the_vehicle_type_field_is_required'),
            'image.required' => trans('lang.the_image_field_is_required'),
            'charges.default.per_km.required' => trans('lang.delivery_charges_per_miles_is_required'),
            'charges.default.minimum.required' => trans('lang.minimum_delivery_charges_is_required'),
            'charges.default.within_km.required' => trans('lang.minimum_delivery_charges_within_miles_is_required'),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $vehicle = VehicleType::find($id);
        if ($vehicle) {
            $vehicle->Libelle = $request->input('libelle');
            $vehicle->status = !empty($request->input('status')) ? 'Yes' : 'No';
            $vehicle->modifier = date('Y-m-d H:i:s');
            $vehicle->updated_at = date('Y-m-d H:i:s');
            if ($request->hasfile('image')) {
                $destination = public_path('assets/images/type_vehicle/' . $vehicle->image);
                if (File::exists($destination)) {
                    File::delete($destination);
                }
                $file = $request->file('image');
                $extenstion = $file->getClientOriginalExtension();
                $time = time() . '.' . $extenstion;
                $filename = 'image_vehicleType' . $time;
                $compressedImage = Helper::compressFile($file->getPathName(), public_path('assets/images/type_vehicle').'/'.$filename, 8);
                $vehicle->image = $filename;
            }
            $vehicle->save();

            $this->saveZoneCharges($id, $request->input('charges', []));

            return redirect('vehicle-type')->with('message', trans('lang.vehicle_type_updated_successfully'));
        }
    }

    protected function saveZoneCharges($vehicleTypeId, array $charges): void
    {
        foreach ($charges as $key => $row) {
            $zoneId = $key === 'default' ? null : (int) $key;
            $perKm = $row['per_km'] ?? null;
            $minimum = $row['minimum'] ?? null;
            $withinKm = $row['within_km'] ?? null;

            $allBlank = ($perKm === null || $perKm === '') && ($minimum === null || $minimum === '') && ($withinKm === null || $withinKm === '');

            $q = DeliveryCharges::where('id_vehicle_type', $vehicleTypeId);
            $zoneId === null ? $q->whereNull('zone_id') : $q->where('zone_id', $zoneId);
            $existing = $q->first();

            if ($allBlank) {
                if ($existing) $existing->delete();
                continue;
            }

            if ($existing) {
                $existing->delivery_charges_per_km = $perKm;
                $existing->minimum_delivery_charges = $minimum;
                $existing->minimum_delivery_charges_within_km = $withinKm;
                $existing->modifier = date('Y-m-d H:i:s');
                $existing->save();
            } else {
                DeliveryCharges::create([
                    'id_vehicle_type' => $vehicleTypeId,
                    'zone_id' => $zoneId,
                    'delivery_charges_per_km' => $perKm,
                    'minimum_delivery_charges' => $minimum,
                    'minimum_delivery_charges_within_km' => $withinKm,
                    'created' => date('Y-m-d H:i:s'),
                    'modifier' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function delete($id)
    {
        if ($id != "") {
            $id = json_decode($id);
            if (is_array($id)) {
                for ($i = 0; $i < count($id); $i++) {
                    $user = VehicleType::find($id[$i]);
                   
                    $destination = public_path('assets/images/type_vehicle/' . $user->image);
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }
                    $user->delete();
                    $DeliveryCharges = DeliveryCharges::where('id_vehicle_type',$id[$i]);
                    if($DeliveryCharges){
                        $DeliveryCharges->delete();
                    }
                }
            } else {

                $user = VehicleType::find($id);
                $destination = public_path('assets/images/type_vehicle/' . $user->image);
                if (File::exists($destination)) {
                    File::delete($destination);
                }
                $user->delete();
                $DeliveryCharges = DeliveryCharges::where('id_vehicle_type',$id);
                if($DeliveryCharges){
                    $DeliveryCharges->delete();
                }
            }

        }
        return redirect()->back();
    }

    public function toggalSwitch(Request $request)
    {
        $ischeck = $request->input('ischeck');
        $id = $request->input('id');
        $vehicle = VehicleType::find($id);

        if ($ischeck == "true") {
            $vehicle->status = 'Yes';
        } else {
            $vehicle->status = 'No';
        }
        $vehicle->save();

    }

}
