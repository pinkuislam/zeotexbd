<html>

<head>

    <style>
        /**
            * Set the margins of the PDF to 0
            * so the background image will cover the entire page.
            **/
        @page {
            margin: 0cm 0cm;
        }

        /**
            * Define the real margins of the content of your PDF
            * Here you will fix the margins of the header and footer
            * Of your background image.
            **/
        body {
            margin-top: 1cm;
            margin-bottom: 1cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        /**
            * Define the width, height, margins and position of the watermark.
            center center no-repeat;opacity: 0.1;position: absolute;width: 100%;height: 100%;top: 250px;**/
        #watermark {
            position: absolute;
            bottom: 0px;
            left: 0px;
            /** The width and height may change
                    according to the dimensions of your letterhead
                **/
            width: 5.5cm;
            height: 5.5cm;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: green;
            /** Your watermark should be behind every content**/
            z-index: -1000;
            opacity: 0.1;
            /* margin: center; */
        }

        .container {
            position: relative;
            z-index: -1000;
            opacity: 0.1;
        }

        .center {
            position: absolute;
            top: 50%;
            width: 100%;
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>

<body>

    <main>
        <section class="content">
            <div class="box-body table-responsive">
                <h3 style="text-align: center">{{ 'Employee Attendance Report on ' . $attendance_date }}</h3>
                <hr><br>

                <table class="table table-bordered table-hover" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>In Time</th>
                            <th>Late</th>
                            <th>Out Time</th>
                            <th>Early Leave</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $emp)
                            <tr>
                                <td style="text-align: center;">{{ $emp->employee_no }}</td>
                                <td style="text-align: center;">{{ $emp->name }}</td>
                                <td style="text-align: center;">{{ $emp->department }}</td>
                                <td style="text-align: center;">{{ $emp->designation }}</td>

                                <td style="text-align: center;">
                                    {{ $emp->login_time == null ? 'Absent' : date('h:i A', strtotime($emp->login_time)) }}
                                </td>

                                <td style="text-align: center;">
                                    {{ $emp->login_time == null ? '' : $emp->is_late }}
                                </td>

                                <td style="text-align: center;">
                                    {{ $emp->login_time == null ? '' : date('h:i A', strtotime($emp->logout_time)) }}
                                </td>
                                <td style="text-align: center;">
                                    {{ ($emp->login_time == null ? '' : $emp->logout_time == null) ? '' : $emp->is_early }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>

</html>
