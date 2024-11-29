<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venues;
use DateTime;
use DB;

class VenuesController extends Controller
{
    //
    public function getVenues( Request $request){
      $venues = Venues::
      where('status', 1)->select('venues.id','name', 'venues.capacity AS SeatingCapacity', 'venues.name AS title', 'venues.id AS resourceId')->get()->toArray();
      return json_encode($venues);
    }
    public function venueAvailability(Request $request){
      $from =$request->from_date;
      $to = $request->to_date;
      $datetime = new DateTime($request->from_date);
      $start= $datetime->format('Y-m-d H:i:s');

      $datetime = new DateTime($request->to_date);
      $end= $datetime->format('Y-m-d H:i:s');

      $bookings = DB::table('hallBooking')
      ->leftjoin('event_bookings', 'event_bookings.id', '=', 'hallBooking.event_id')
      ->select(
          'hallBooking.event_id',
          'hallBooking.eventName',
          'hallBooking.startTime',
          'hallBooking.endTime',
          'event_bookings.event_name AS title',
          'hallBooking.startTime AS start',
          'hallBooking.endTime AS end',
          'hallBooking.booking_id',
          'hallBooking.venue AS resourceId',
          'hallBooking.externalVenue',
          'event_bookings.eventColor AS color',
          DB::raw('false as editable'),
          DB::raw("(
      SELECT name
      FROM employees
      WHERE user_id = event_bookings.faculty
      ) as courseDirector"),
          DB::raw("(
          SELECT name
          FROM employees
          WHERE user_id = event_bookings.coordinator
          ) as coursecordinator")
      )
      // ->whereBetween('hallBooking.startTimes', [$request->from_date, $request->to_date])
      ->where(function ($query) use ($from, $to) {
        $query->whereBetween('startTime', [$from, $to])
              ->orWhereBetween('endTime', [$from, $to])
              ->orWhere(function ($q) use ($from, $to) {
                  $q->where('startTime', '<=', $from)
                    ->where('endTime', '>=', $to);
              });
    })
      ->where('hallBooking.venue',$request->venue_id)
      ->where('event_bookings.status', '<', 2)
      ->where('hallBooking.cancelledOn', NULL)
      ->where('hallBooking.status', 1)->get()->toArray();

      if(count($bookings) > 0){
        return response()->json(['status' => 2,'bookings'=>$bookings],200);
      }
      else{
        return response()->json(['status' => 1,'bookings'=>$bookings],200);
      }
    }
}