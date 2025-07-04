<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            color: #1e1e1e;
        }
        table.booking_info{
            width: 100%;
            padding: 0;
            margin: 0;
            margin-top: 12px;
            border-collapse: collapse;
        }
        table.booking_info tr td{
            padding-bottom: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <table border="0" style="width: 100%; padding: 0px; margin: 0; margin-bottom: 16px; border-collapse: collapse; border-bottom: 2px solid gray;">
        <tr>
            <td style="width: 70%; padding-bottom: 10px;">
                <h1 style="margin: 0; font-size: 18px; font-weight: 600; margin-bottom: 6px; color: darkslategray;">{{$companyProfile->name}}</h1>
                <p style="margin: 0; font-size: 14px; font-weight: 400; margin-bottom: 2px;">{{$companyProfile->address}}</p>
                <p style="margin: 0; font-size: 14px; font-weight: 400; margin-bottom: 2px;"><strong>Email:</strong> {{$companyProfile->email}}</p>
                <p style="margin: 0; font-size: 14px; font-weight: 400; margin-bottom: 2px;"><strong>Phone:</strong> {{$companyProfile->phone}}</p>
            </td>
            <td style="width: 30%; text-align: right; padding-bottom: 10px;">
                @if($companyProfile->logo && file_exists(public_path($companyProfile->logo)))
                <img src="{{public_path($companyProfile->logo)}}" alt="Company Logo" style="width: 80px; height: auto;">
                @endif
            </td>
        </tr>
    </table>

    <p style="font-size: 14px; margin: 0px;"><strong>Dear {{ $flightBookingDetails->traveller_name }},</strong></p>
    <p style="font-size: 14px; margin: 0px; margin-top: 4px; margin-bottom: 16px; line-height: 20px;">
        Thank you for choosing {{$companyProfile->name}}.
        Your @if($flightBookingDetails->flight_type == 1) one way @else round trip @endif

        {{DB::table('city_airports')->where('airport_code', $flightBookingDetails->departure_location)->first()->city_name}} ({{ $flightBookingDetails->departure_location }}) -
        {{DB::table('city_airports')->where('airport_code', $flightBookingDetails->arrival_location)->first()->city_name}} ({{ $flightBookingDetails->arrival_location }})

        @if($flightBookingDetails->flight_type == 2) and back flight @endif
        has been confirmed on
        @php
            $departure = $bookingResSegs ? $bookingResSegs[0]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
            $departureDateTime = explode('T', $departure);
            if(isset($departureDateTime[0]) && isset($departureDateTime[1])){
                echo date('l, jS \of F Y', strtotime($departureDateTime[0])) . ' - ' . substr($departureDateTime[1], 0, 5);
            }
        @endphp
    </p>

    <table class="booking_info" border="0">
        <tr>
            <td style="width: 50%;"><strong>Booking Date:</strong> {{ date('Y-m-d h:i a', strtotime($flightBookingDetails->created_at)) }}</td>
            <td style="width: 50%;"><strong>Booking Email:</strong> {{ $flightBookingDetails->traveller_email }}</td>
        </tr>
        <tr>
            <td><strong>Booking No:</strong> {{ $flightBookingDetails->booking_no }}</td>
            <td><strong>Booking Contact:</strong> {{ $flightBookingDetails->traveller_contact }}</td>
        </tr>
        <tr>
            <td><strong>PNR ID:</strong> {{ $flightBookingDetails->pnr_id }}</td>
            <td>
                <strong>Total Passangers:</strong>
                @if ($flightBookingDetails->adult)
                    {{ $flightBookingDetails->adult }} Adult
                @endif
                @if ($flightBookingDetails->child)
                    {{ $flightBookingDetails->child }} Child
                @endif
                @if ($flightBookingDetails->infant)
                    {{ $flightBookingDetails->infant }} Infant
                @endif
            </td>
        </tr>
        <tr>
            <td>
                <strong>Status:</strong>
                @if ($flightBookingDetails->status == 0)
                    <span style="color: goldenrod; font-weight: 600">Booking Requested</span>
                @endif
                @if ($flightBookingDetails->status == 1)
                    <span style="color: green; font-weight: 600">Booking Done</span>
                @endif
                @if ($flightBookingDetails->status == 2)
                    <span style="color: green; font-weight: 600">Ticket Issued</span>
                @endif
                @if ($flightBookingDetails->status == 3)
                    <span style="color: red; font-weight: 600">Booking Cancelled</span>
                @endif
                @if ($flightBookingDetails->status == 4)
                    <span style="color: red; font-weight: 600">Ticket Cancelled</span>
                @endif
            </td>
            <td>
                @if($flightPassangers[0]->ticket_no == null && $flightBookingDetails->last_ticket_datetime)
                    <strong>Ticket Deadline:</strong> {{ date("jS M-y, h:i a", strtotime($flightBookingDetails->last_ticket_datetime)) }}
                @endif
            </td>
        </tr>
    </table>


    <table border="0" style="width: 100%; padding: 0; margin: 0; margin-top: 15px; border-collapse: collapse;">

        <tr>
            <th colspan="4" style="text-align: center; font-size: 14px; background-color: lightblue; padding: 4px 0px; border-radius: 4px">Flight Itinery</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left; font-size: 14px; padding: 4px 0px;">{{ $flightBookingDetails->departure_location }} - {{ $flightBookingDetails->arrival_location }}</th>
        </tr>

        @foreach ($flightSegments as $index => $segment)
            <tr>
                <td style="width: 30%; font-size: 14px; padding: 5px 10px 5px 0; border-right: 1px solid gray; text-align: left;">

                    <table border="0" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td>
                                @if($segment->carrier_operating_code && file_exists(public_path('airlines_logo/'.strtolower($segment->carrier_operating_code).'.png')))
                                <img src="{{ public_path('airlines_logo/'.strtolower($segment->carrier_operating_code).'.png') }}" alt="Airline Logo" style="max-height: 50px; display: block;">
                                @endif
                            </td>
                            <td>
                                @php
                                    $airlineInfo = DB::table('airlines')
                                        ->where('iata', $segment->carrier_operating_code)
                                        ->where('active', 'Y')
                                        ->first();
                                @endphp
                                <p style="margin: 0; margin-bottom: 2px;">{{ $airlineInfo ? $airlineInfo->name : 'N/A' }}</p>
                                <p style="margin: 0; margin-bottom: 2px;">{{ $segment->carrier_operating_code }}-{{ $segment->carrier_operating_flight_number }}</p>
                                <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Aircraft: {{ getAircraftName($segment->carrier_equipment_code) }}</p>
                                <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{ getCabinClass($segment->cabin_code) }} ({{$segment->booking_code}}) Class</p>
                                <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">
                                    Airlines PNR:
                                    @php
                                        if($flightBookingDetails->airlines_pnr) {
                                            $pnrList = explode(',', $flightBookingDetails->airlines_pnr);
                                            $airlinePNR = isset($pnrList[$index]) ? $pnrList[$index] : 'N/A';
                                            echo $airlinePNR;
                                        }
                                    @endphp
                                </p>
                            </td>
                        </tr>
                    </table>

                </td>
                <td style="width: 25%; font-size: 14px; padding: 5px 10px; border-right: 1px solid gray;">
                    @php
                        $departureLocation = DB::table('city_airports')->where('airport_code', $segment->departure_airport_code)->first();
                        $departure = $bookingResSegs ? $bookingResSegs[$index]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                        $departureDateTime = explode('T', $departure);
                    @endphp
                    <p style="margin: 0; margin-bottom: 2px;">{{ $departureLocation->city_name }} ({{ $departureLocation->city_code }})</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{ $departureLocation->airport_name }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Terminal - {{ $segment->departure_terminal }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">@if(isset($departureDateTime[1])){{substr($departureDateTime[1], 0, 5)}}@endif</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">@if(isset($departureDateTime[0])){{date('D, j M Y', strtotime($departureDateTime[0]))}}@endif</p>
                </td>
                <td style="width: 25%; font-size: 14px; padding: 5px 10px; border-right: 1px solid gray;">
                    @php
                        $arrivalLocation = DB::table('city_airports')->where('airport_code', $segment->arrival_airport_code)->first();
                        $arrival = $bookingResSegs ? $bookingResSegs[$index]['Product']['ProductDetails']['Air']['ArrivalDateTime'] : null;
                        $arrivalDateTime = explode('T', $arrival);
                    @endphp
                    <p style="margin: 0; margin-bottom: 2px;">{{ $arrivalLocation->city_name }} ({{ $arrivalLocation->city_code }})</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">{{ $arrivalLocation->airport_name }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Terminal - {{ $segment->arrival_terminal }}</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">@if(isset($arrivalDateTime[1])){{substr($arrivalDateTime[1], 0, 5)}}@endif</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">@if(isset($arrivalDateTime[0])){{date('D, j M Y', strtotime($arrivalDateTime[0]))}}@endif</p>
                </td>
                <td style="width: 20%; font-size: 14px; padding: 5px 10px;">

                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Checked: @if($segment->baggage_allowance) {{ $segment->baggage_allowance }} @else N/A @endif</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Cabin: @if($segment->cabin_baggage) {{ $segment->cabin_baggage }} @else N/A @endif</p>
                    <p style="margin: 0; margin-bottom: 2px; font-size: 12px;">Duration:<br>
                        @php
                            $minutes = (int) $segment->elapsed_time;
                            $hours = intdiv($minutes, 60);
                            $remainingMinutes = $minutes % 60;
                            echo "{$hours} hour {$remainingMinutes} min";
                        @endphp
                    </p>
                </td>
            </tr>

            @if(isset($flightSegments[$index+1]) && $flightSegments[$index+1]->departure_airport_code == $flightBookingDetails->arrival_location)
            <tr>
                <th colspan="4" style="text-align: left; font-size: 14px; padding: 4px 0px;">{{ $flightBookingDetails->arrival_location }} - {{ $flightBookingDetails->departure_location }}</th>
            </tr>
            @endif

            @if(isset($flightSegments[$index+1]) && $flightSegments[$index+1]->departure_airport_code != $flightBookingDetails->arrival_location)
            <tr>
                <td colspan="4" style="background: lightgoldenrodyellow; text-align: center; font-size: 14px; padding: 4px 0px; color: brown;">
                    @php
                        $firstArrival = new DateTime($arrivalDateTime[0].''.$arrivalDateTime[1]);
                        $secondDepartureRes = $bookingResSegs ? $bookingResSegs[$index+1]['Product']['ProductDetails']['Air']['DepartureDateTime'] : null;
                        $secondDepartureDateTime = explode('T', $secondDepartureRes);
                        $secondDeparture = new DateTime($secondDepartureDateTime[0].''.$secondDepartureDateTime[1]);
                        $interval = $firstArrival->diff($secondDeparture);
                        echo $interval->h . " hrs " . $interval->i . " mins Transit in " . $arrivalLocation->city_name . " (" . $arrivalLocation->airport_code . ")";
                    @endphp
                </td>
            </tr>
            @endif

        @endforeach

    </table>

    <table border="0" style="width: 100%; padding: 0; margin: 0; margin-top: 15px; border-collapse: collapse; ">
        <tr>
            <th colspan="5" style="text-align: center; font-size: 14px; background-color: lightgreen; padding: 4px 0px;">Flight Passangers</th>
        </tr>
        <tr>
            <th style="width: 20%; text-align: center; font-size: 13px; padding: 4px 4px;">Ticket No</th>
            <th style="width: 10%; text-align: center; font-size: 13px; padding: 4px 4px;">Type</th>
            <th style="width: 35%; text-align: left; font-size: 13px; padding: 4px 4px;">Travellers Name</th>
            <th style="width: 20%; text-align: center; font-size: 13px; padding: 4px 4px">Passport/NID</th>
            <th style="width: 15%; text-align: center; font-size: 13px; padding: 4px 4px">DOB</th>
        </tr>
        @foreach ($flightPassangers as $flightPassanger)
        <tr>
            <td style="text-align: center; font-size: 12px; padding: 2px 4px;">@if($flightPassanger->ticket_no){{$flightPassanger->ticket_no}}@else N/A @endif</td>
            <td style="text-align: center; font-size: 12px; padding: 2px 4px;">{{$flightPassanger->passanger_type}}</td>
            <td style="text-align: left; font-size: 12px; padding: 2px 4px;">{{$flightPassanger->title}} {{$flightPassanger->first_name}} {{$flightPassanger->last_name}}</td>
            <td style="text-align: center; font-size: 12px; padding: 2px 4px;">{{$flightPassanger->document_no}}</td>
            <td style="text-align: center; font-size: 12px; padding: 2px 4px;">{{$flightPassanger->dob}}</td>
        </tr>
        @endforeach
    </table>

    @if($flightBookingDetails->status == 1 || $flightBookingDetails->status == 3)
    <h5 style="margin: 20px 0px 6px 0px; padding: px; font-size: 12px;">Booking Note:</h5>
    <ul style="padding: 0px; margin: 0px; padding-left: 15px">
        <li style="font-size: 12px; margin-bottom: 2px;">Please recheck with the spelling of your name with Travel documents(passport/NID).</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Please check Flight Itinerary(Destination/Airlines/Travel Date/Flight Number/Timings) as per your query</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Please issue the ticket or cancel the booking within the last ticketing time mentioned.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Fare can be change without any notice by the airlines.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Tickets are non endorsable, non reroutable.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Fare are not confirm until the final ticket has been issued.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Fare are not valid. subject to seat availability.</li>
    </ul>
    @endif

    @if($flightBookingDetails->status == 2 || $flightBookingDetails->status == 4)
    <h5 style="margin: 20px 0px 6px 0px; padding: px; font-size: 12px;">Travel Note:</h5>
    <ul style="padding: 0px; margin: 0px; padding-left: 15px">
        <li style="font-size: 12px; margin-bottom: 2px;">Check in counter will open before 1.30 hours of domestic and 3 hours of international flight departure.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Passenger reporting late for check in may be refused to board on flight. Please bring a valid photo ID.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Check in counter will be closed before 30 minutes of domestic and 60 minutes of international flight departure.</li>
        <li style="font-size: 12px; margin-bottom: 2px;">Boarding gate will be closed before 20 minutes of domestic and 30 minutes of international flight departure.</li>
    </ul>
    @endif

</body>
</html>
