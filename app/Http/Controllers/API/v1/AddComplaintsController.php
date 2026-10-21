<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Complaints;
use App\Models\ParcelOrder;
use App\Models\RentalOrder;
use App\Models\Requests;
use Illuminate\Http\Request;
use Validator;

class AddComplaintsController extends Controller
{
    
    /**
     * Two lookups share this endpoint. The apps ask for the complaint on one
     * booking (booking_type + booking_id). The web panel has no booking in
     * hand on its support screen, so it asks for every complaint raised on the
     * customer's own bookings instead (user_id + user_type). Callers that send
     * a booking keep the old behaviour untouched.
     */
    public function index(Request $request)
    {
        if (!$request->filled('booking_id')) {
            return $this->listForUser($request);
        }

        $response = array();
        $validator = Validator::make($request->all(), [
            'booking_type' => 'required|in:ride,parcel,rental',
            'booking_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response['success'] = 'Failed';
            $response['code'] = 404;
            $response['message'] = $validator->errors()->first();
            $response['data'] = null;
            return response()->json($response);
        }

        $booking_type = $request->booking_type;
        $booking_id = $request->booking_id;

        $complaint = Complaints::where('booking_id', $booking_id)->where('booking_type', $booking_type)->first();

        if ($complaint) {
            $response['success'] = 'success';
            $response['code'] = 200;
            $response['message'] = 'Complaint found successfully';
            $response['data'] = $complaint->toArray();
        } else {
            $response['success'] = 'Failed';
            $response['code'] = 404;
            $response['message'] = 'No complaint found for the given details';
            $response['data'] = null;
        }

        return response()->json($response);
    }

    /**
     * Every complaint raised on the bookings belonging to one customer.
     *
     * Complaints carry only a booking id and type, so ownership has to come
     * from the bookings themselves — all three tables key the customer on
     * id_user_app.
     */
    private function listForUser(Request $request)
    {
        $response = array();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'user_type' => 'required|in:customer',
        ]);

        if ($validator->fails()) {
            $response['success'] = 'Failed';
            $response['code'] = 404;
            $response['message'] = $validator->errors()->first();
            $response['data'] = null;
            return response()->json($response);
        }

        $userId = $request->user_id;

        $bookings = [
            'ride' => Requests::where('id_user_app', $userId)->pluck('id')->toArray(),
            'parcel' => ParcelOrder::where('id_user_app', $userId)->pluck('id')->toArray(),
            'rental' => RentalOrder::where('id_user_app', $userId)->pluck('id')->toArray(),
        ];

        // A customer with no bookings owns no complaints. Without this the
        // closure below would add no condition at all, and the query would
        // return every complaint in the system.
        if (empty(array_filter($bookings))) {
            $response["success"] = "success";
            $response["code"] = 200;
            $response["message"] = "No complaints found";
            $response["data"] = [];
            return response()->json($response);
        }

        $complaints = Complaints::where(function ($query) use ($bookings) {
            foreach ($bookings as $type => $ids) {
                if (empty($ids)) {
                    continue;
                }

                $query->orWhere(function ($owned) use ($type, $ids) {
                    $owned->where('booking_type', $type)->whereIn('booking_id', $ids);
                });
            }
        })->orderBy('id', 'desc')->get();

        $response['success'] = 'success';
        $response['code'] = 200;
        $response['message'] = 'Complaints fetched successfully';
        $response['data'] = $complaints->toArray();

        return response()->json($response);
    }

    public function register(Request $request)
    {
        $response = array();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'booking_type' => 'required|in:ride,parcel,rental',
            'booking_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response['success'] = 'Failed';
            $response['code'] = 404;
            $response['message'] = $validator->errors()->first();
            $response['data'] = null;
            return response()->json($response);
        }

        $booking_type = $request->booking_type;
        $booking_id = $request->booking_id;
        $title = $request->title;
        $description = $request->description;

        if (Complaints::where('booking_id', $booking_id)->where('booking_type', $booking_type)->exists()){
            return response()->json([
                'success' => 'Failed',
                'error' => 'Complaint Already Submitted',
            ]);
        }

        $complaint = Complaints::create([
            'title' => $title,
            'description' => $description,
            'status' => 'initiated',
            'booking_id' => $booking_id,
            'booking_type' => $booking_type,
        ]);

        if ($complaint) {
            $response['success'] = 'success';
            $response['code'] = 200;
            $response['message'] = 'Complaint added successfully';
            $response['data'] = $complaint->toArray();
        } else {
            $response['success'] = 'Failed';
            $response['code'] = 404;
            $response['message'] = 'Failed to add complaint';
            $response['data'] = null;
        }

        return response()->json($response);
    }
}
