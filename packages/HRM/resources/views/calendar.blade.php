@extends(config('hrm.layout_view'))

@push('styles')
<style>
    .calendar {
		margin: 0px 40px;
	}
	.calendar > .row > .calendar-day {
		font-family: 'Roboto', sans-serif;
		width: 14.28571428571429%;
		border: 1px solid rgb(235, 235, 235);
		border-right-width: 0px;
		border-bottom-width: 0px;
		min-height: 120px;
	}
	.calendar > .row > .calendar-day.head {
		min-height: 50px;
	}
	.calendar > .row > .calendar-day.calendar-no-current-month {
		color: rgb(200, 200, 200);
	}
	.calendar > .row > .calendar-day:last-child {
		border-right-width: 1px;
	}
	.calendar > .row:last-child > .calendar-day {
		border-bottom-width: 1px;
	}

    .calendar .calendar-day.weekend {
        background-color: rgb(250, 200, 200);
    }

	.calendar .calendar-day > time {
		position: absolute;
		display: block;
		bottom: 0px;
		left: 0px;
		font-size: 12px;
		font-weight: 300;
		width: 100%;
		padding: 10px 10px 3px 0px;
		text-align: right;
	}
	.calendar .calendar-day > .events {
		cursor: pointer;
	}
	.calendar .calendar-day > .events > .event {
		padding: 10px 5px;
        text-align: center;
	}
</style>
@endpush

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Calendar
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('oshnisoft-hrm.calendars.index') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="year" class="form-control">
                                        @foreach (years() as $y)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="month" class="form-control">
                                        @foreach (months() as $mk => $mv)
                                            <option value="{{ $mk }}" {{ $month == $mk ? 'selected' : '' }}>{{ $mv }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.calendars.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        @if ($records->count() > 0)
                        <h1 class="title text-center">{{ months($month) }} {{ $year }}</h1>

                        <div class="calendar">
                            <div class="row">
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">SAT DAY</h3>
                                </div>
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">SUN DAY</h3>
                                </div>
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">MON DAY</h3>
                                </div>
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">TUE DAY</h3>
                                </div>
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">WED DAY</h3>
                                </div>
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">THU DAY</h3>
                                </div>
                                <div class="col-xs-12 calendar-day head">
                                    <h3 class="text-center">FRI DAY</h3>
                                </div>
                            </div>

                            @php
                                $row = 1;
                                $firstDay = date("D", mktime(0, 0, 0, $month, 1, $year));
                                $firstDayNo = array_search($firstDay, weeks());
                                $previousMonthDays = previousMonthDays($year, $month);
                            @endphp

                            @foreach ($records as $key => $item)

                                @php
                                    $dataArr = explode('-', $item->date);
                                    $currentDay = date("D", mktime(0, 0, 0, $dataArr[1], $dataArr[2], $dataArr[0]));
                                @endphp

                                @if ($key == 0)
                                <div class="row">
                                    @for ($w = 1; $w < $firstDayNo; $w++)
                                    <div class="col-xs-12 calendar-day calendar-no-current-month">
                                        <time>{{ ($previousMonthDays - $firstDayNo + 1) + $w }}</time>
                                    </div>
                                    @php $row++; @endphp
                                    @endfor
                                @endif

                                <div class="col-xs-12 calendar-day{{ $item->status == 'Closed' ? ' weekend' : '' }}">
                                    <time datetime="{{ $item->date }}">{{ $dataArr[2] }}</time>

                                    <div class="events" onclick="getModal({{ $item }})">
                                        <div class="event">
                                            @if ($item->status == 'Open')
                                            <div class="datetime">
                                                <span class="glyphicon glyphicon-time"></span> {{ $item->in_time }} - {{ $item->out_time }}
                                            </div>
                                            @endif
                                            <p>{{ $item->note }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($row == 7)
                                @php $row = 0; @endphp
                                </div>
                                <div class="row">
                                @endif

                                @php $row++; @endphp
                            @endforeach


                            @if ($row > 1 && $row < 7)
                                @for ($r = 1; $r <= (8-$row); $r++)
                                <div class="col-xs-12 calendar-day calendar-no-current-month">
                                    <time>{{ $r }}</time>
                                </div>
                                @endfor
                            @endif
                        </div>
                        @else
                            <form method="POST" action="{{ route('oshnisoft-hrm.calendars.generate', $year) }}" class="text-center">
                                @csrf
                                <p>This year calendar not found is the database. Please generate calendar.</p>

                                <button type="submit "class="btn btn-success btn-flat btn-lg">Generate {{ $year }} Calendar</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('oshnisoft-hrm.calendars.update', 1) }}?year={{ $year }}&month={{ $month }}" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="id" required>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status" required>
                            @foreach (['Open', 'Closed'] as $sts)
                                <option value="{{ $sts }}">{{ $sts }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Note</label>
                        <textarea class="form-control" name="note" id="note"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Time</label>
                        <div class="input-group">
                            <span class="input-group-addon">In</span>
                            <input type="time" class="form-control" name="in_time" id="in_time">
                            <span class="input-group-addon">Out</span>
                            <input type="time" class="form-control" name="out_time" id="out_time">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-flat">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
<script>
    function getModal(item) {
        $('#id').val(item.id);
        $('#status').val(item.status);
        $('#note').val(item.note);
        $('#in_time').val(item.in_time);
        $('#out_time').val(item.out_time);

        $('#editModal').modal('show');
    }
</script>
@endpush
