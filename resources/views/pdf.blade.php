@php
    // TODO: need to pass width from controller

    use Illuminate\Support\Facades\Log;

    $page_margin = 18;
    $footer_height = 20;

    $row = 1;
    $column  = 1;
    $position = 0;

    $today = date("F Y");

    $line_height = 8;
    $brai = new BRAIHelpers($height, $line_height);
@endphp
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style>
        @page {
            margin: {{ $page_margin }}px;
            @if ($numbering !== false)
                 margin-bottom: {{ $footer_height + $page_margin }}px;
            @endif
        }

        body {
            color: black;
            counter-increment: page 1;
            counter-reset: page{{ $numbering - 1 }};
            font-family: {{ $font }};
            font-size: {{ $line_height }}px;
        }

        h1 {
            border-bottom: 0.5px solid black;
            font-size: 11px;
            margin: 0 0 10px;
            padding-bottom: 4px;
        }
        h3 {
            font-weight: normal;
            font-size: 10px;
            margin: 1px 0 3px;
            page-break-after: avoid;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .legend:after {
            page-break-after: always;
        }
        .legend > div {
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
            padding-top: 6px;
            width: 50%;
        }
        .legend > div:last-child {
            border-bottom: none;
        }
        .legend > div span {
            display: inline-block;
        }

        .meeting {
            border-spacing: 0;
            margin: 0 0 5px;
            padding: 0;
            page-break-inside: avoid;
            vertical-align: top;
            width: 100%;
        }
        .meeting td {
            margin: 0;
            padding: 0;
            vertical-align: top;
        }
        .meeting .time {
            width: 65px;
            text-align: right;
            padding-right: 5px;
        }
        .meeting .name {
            font-weight: bold;
        }

        .brai-day {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }
        .brai-day h1 {
            font-size: 20px;
        }

        footer {
            bottom: -{{ $footer_height }}px;
            height: {{ $footer_height }}px;
            left: 0;
            position: fixed;
            right: 0;
        }
        footer::after {
            border-top: 0.5px solid black;
            content: counter(page);
            left: 50%;
            margin-left: -20px;
            padding-top: 4px;
            position: absolute;
            text-align: center;
            width: 40px;
        }

        .brai-day-time-region {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            text-decoration-thickness: 2px;
            margin-bottom: 1px;
        }
        .brai-day-name {
            font-weight: bold;
        }
        .brai-day-types {
            margin-bottom: 10px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
            page-break-after: always;
        }
        .row:last-child:after {
            page-break-after: avoid;
        }
        .column {
            float: left;
            width: 30%;
            margin: 0 10px 10px;
        }

    </style>
</head>

<body>
@if ($numbering !== false)
    <footer>{{ $today  }}</footer>
@endif
<main>
    @if (!empty($options['legend']))
        @include('legend', compact('types_in_use', 'types'))
        <div class="row"></div>
    @endif

    @foreach ($days as $day => $meetings)
        @if ($loop->first)
            <div class="row"><div class="column"><?php print $brai->newDay($day); ?>
            @php
                $column = 1;
                $position = 1;
            @endphp
        @else
            @php
                print $brai->newDay($day);
                $position++;
            @endphp
        @endif

        @php
            $brai->next($row, $column, $position);
            $position++;
        @endphp
        @foreach ($meetings as $meeting)
            @php
                $num_meeting_lines = $brai->getNumMeetingLines($meeting);
                $brai->next($row, $column, $position, $day);
                $position += $num_meeting_lines;
            @endphp
            @include('brai-meeting', compact('meeting'))
        @endforeach
    @endforeach
    <?php echo "</div></div>"; ?>
</main>
</body>
</html>
