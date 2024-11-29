<?php

namespace App\Http\Controllers;

use App\Models\hallBooking;
use App\Models\Venues;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\eventBooking;
use DB;
use DateTime;

class eventBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $pageConfigs = ['myLayout' => 'horizontal'];

      return view('events.eventbooking',['pageConfigs'=> $pageConfigs]);

    }

    public function eventList()
    {

      $id= Auth::user()->id;

      $list = eventBooking::leftjoin("venue_booking","venue_booking.event_id","=","event_bookings.id")
      ->join("employees as emp","emp.user_id","=","event_bookings.faculty")
      ->select('event_bookings.*',DB::raw("DATE_FORMAT(event_bookings.booked_on, '%d-%b-%Y') as formatted_date"),'emp.name as faculty_name')->where('event_bookings.booked_by',$id)
      ->orderBy('event_bookings.status')->get();
        //  $queries = DB::getQueryLog();
        //   $last_query = end($queries);
        //   dd($queries);
      return response()->json(['data'=> $list]);

    }

    public function loadBookingsAllVenue(Request $request,  $event_id)
    {

      $datetime = new DateTime($request->start);
      $start= $datetime->format('Y-m-d H:i:s');

      $datetime = new DateTime($request->end);
      $end= $datetime->format('Y-m-d H:i:s');
        // $temporaryClosed = DB::table('venues')->where('staus', 2)->select('id as resourceId', 'name', 'inactive_from as start', 'inactive_to as end', 'reason_inactive')->get()->toArray();
        // foreach ($temporaryClosed as $halls) {
        //     $halls->color = "yellow";
        //     $halls->coursecordinator = "Nil";
        //     $halls->editable = false;
        //     $halls->courseDirector = "Nil";
        //     $halls->title = $halls->reason_inactive;
        // }
        // $permanantlyClosed = DB::table('venues')->where('VenueStatus', 0)->select('id as resourceId', 'Name', 'reason_inactive')->get()->toArray();
        // foreach ($permanantlyClosed as $halls) {
        //     $halls->color = "red";
        //     $halls->start = '2019-01-01';
        //     $halls->end = '2050-01-01';
        //     $halls->coursecordinator = "Nil";
        //     $halls->editable = false;
        //     $halls->courseDirector = "Nil";
        //     $halls->title = $halls->reason_inactive;
        // }
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
            ->whereBetween('hallBooking.startTime', [$start, $end])
            ->where('event_bookings.status', '<', 2)
            ->where('hallBooking.cancelledOn', NULL)
            ->where('hallBooking.status', 1)
            ->orwhere('hallBooking.event_id', 0)
            ->get()->toArray();
        foreach ($bookings as $booking) {
            $booking->title = $booking->title;
            if ($booking->event_id == 0) {
                $booking->title = $booking->eventName . " [Special]";
                $booking->color = "yellow";
            }
            if (($booking->event_id == $event_id) && ($event_id != 0)) {
                $booking->isEditable = true;
            }
            if (($booking->event_id == $event_id) && ($event_id == 0)) {
                $booking->isEditable = true;
            }
            if ($booking->event_id != $event_id) {
                $booking->isEditable = false;
                $booking->color = '#ccc';
            }


            if ($booking->resourceId == null || $booking->resourceId == 'null') {
                // $booking->resourceId =$booking->externalVenue;
                $booking->resourceId = $booking->externalVenue . $booking->event_id;
            }
            // print_r($booking);
        }

        // $bookings = array_merge($bookings, $permanantlyClosed, $temporaryClosed);
        return $bookings;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
          'type' => 'required|',
          'from_date' => 'required|',
          'to_date' => 'required|',


      ]);
      $user_id= Auth::user()->id;
      $date= date('Y-m-d H:i:s');

      $var = $request->input('from_date');
      $datef = str_replace('/', '-', $var);
      $from_date=  date('Y-m-d H:i:s', strtotime($datef));

      $vart = $request->input('to_date');
      $datet = str_replace('/', '-', $vart);
      $to_date=  date('Y-m-d H:i:s', strtotime($datet));


      if($request->input('type') == 'initial' ){

        $event_id = eventBooking::create(['from_date' => $from_date
        ,'to_date' => $to_date,'booked_by' => $user_id,'status' => 0,'booked_on' => $date
      ])->id;;

        if ($event_id) {
          return response()->json(['message' => "Created",'event_id'=>$event_id],200);
        } else {
          return response()->json(['message' => "Internal Server Error",'event_id'=>0], 500);

        }

      }
      else{

      }


    }

    /**
     * Display the specified resource.
     */
    public function show(hallBooking $hallBooking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(hallBooking $hallBooking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //

      $date= date('Y-m-d H:i:s');
      // $attendance_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('date'))));
      $event_name = $request->input('event_name');
      $host = $request->input('host');
      $venue_type = $request->input('venue_type');
      $no_of_participants = $request->input('no_of_participants');
      $venue_id = $request->input('venue_id');

      $var = $request->input('from_date');
      $datef = str_replace('/', '-', $var);
      $from_date=  date('Y-m-d H:i:s', strtotime($datef));

      $vart = $request->input('to_date');
      $datet = str_replace('/', '-', $vart);
      $to_date=  date('Y-m-d H:i:s', strtotime($datet));



      $event = eventBooking::find($id);
      $event->from_date = $from_date;
      $event->to_date = $to_date;
      $event->event_name = $event_name;
      $event->host = $host;
      $event->venue_type = $venue_type;
      $event->no_of_participants = $no_of_participants;
      $event->save();
      if($request->has('venue_id') ){
        $hall=DB::table('hallBooking')->insert([
          "event_id"=>$id,
          "venue"=>$venue_id,
          "startTime"=>$event->from_date,
          "endTime"=>$event->to_date

        ]);
      }
      if($event){
      return response()->json(['message' => "Created",'event_id'=>$event->id],200);
    } else {
      return response()->json(['message' => "Internal Server Error",'event_id'=>0], 500);

    }




    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(hallBooking $hallBooking)
    {
        //
    }
}