<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>

    <!-- Include jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
        integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous">
    </script>


    <style>
        #generatePDFButton {
            margin-top: 10px;
            margin-bottom: 20px;
            padding: 5px 10px;
            background-color: #2c243a;
            border-radius: 3px;
            color: #f1f1f1;
            cursor: pointer;
        }

        #generatePDFButton:hover {
            background-color: #433063;
            color: #f1f1f1;
        }


        #generatePDFButton:active {
            background-color: #433063;
            color: #f1f1f1;
        }
    </style>

    <style type="text/css">
        body,
        * {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            padding: 0;
            margin: 0;
            border: none;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 5px 10px;
        }

        table {
            background-color: #2c243b;
            width: 100%;
            table-layout: auto;
            margin: 10px 0 0 0;
            border: none;
            border: solid 1px;
        }

        table tr td {
            color: #444444;
            background-color: #f1f1f1;
        }

        table tr td,
        table tr th {
            text-align: center;
            font-size: 9pt;
            margin: 0;
        }

        table tr th {
            text-transform: uppercase;
            font-weight: bold;
            background-color: #2c243a;
            color: rgb(238, 238, 238);
            font-size: 10pt;
            padding: 5px;
        }

        table.mko-table tbody tr td:first-child {
            width: 35%;
            text-transform: uppercase;
            font-weight: bold;
            background-color: #4c4655;
            color: rgb(238, 238, 238);
            font-size: 10pt;
            padding: 5px;
        }

        .field-sum {
            background-color: #2c243b !important;
            color: whitesmoke !important;
        }

        .header h2 {
            font-size: 32px;
            text-transform: uppercase;
        }

        .header h4 {
            margin: 5px 0;
        }

        .title-sect {
            text-transform: uppercase;
            width: 100vh;
            text-align: center;
            font-weight: bolder;
            color: #2c243a;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        tbody tr td {
            font-weight: 700;
            text-transform: uppercase;
        }

        .limit-text {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body style="position: relative">
    <center class="header">
        <h2>{{ $store_name }}</h2>
        <h4>{{ $title }}</h4>
        <h5> <span class="date-element">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</span> | PDF &middot; Cleoun
            Render Engine &middot; V2.0.0
        </h5>
    </center>

    @foreach ($content as $content_title => $content_data)
        <h2 class="title-sect">{{ strtoupper($content_title) }}</h2>

        <table class='table table-bordered mko-table' style="width: 100%;">
            <thead>
                <tr>
                    <th>NAMA</th>
                    <th>NILAI</th>
                </tr>
            </thead>
            <tbody style="position: relative; width: 100%">


                @foreach ($content_data as $data_k => $data_v)
                    <tr>
                        <td>{{ $data_k }}</td>
                        <td>{{ $data_v }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="field-sum"> </td>
                </tr>

            </tbody>
        </table>
    @endforeach


    @foreach ($tables as $title => $data)
        <h2 class="title-sect">{{ $title }}</h2>

        <table class='table table-bordered margin-bottom: 0px'>
            <thead>
                <tr>
                    @foreach ($data['kolom'] as $kolom)
                        <th>{{ $kolom }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody style="position: relative; width: 100%" id="tabel_record_penjualan">

                @foreach ($data['data'] as $table_val)
                    <tr>
                        @foreach ($table_val as $key => $value)
                            @if ($key == 'amount' || $key == 'total_cost' || $key == 'total_price' || $key == 'paid')
                                <td style="font-size: 11px">Rp. {{ ubah_angka_int_ke_rupiah($value) }}</td>
                            @elseif ($key == 'created_at')
                                <td style="font-size: 11px">{{ \Carbon\Carbon::parse($value)->format('d-m-Y') }}
                                </td>
                            @else
                                <td style="font-size: 11px">{{ $value ?? '-' }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach


            </tbody>

        </table>

        @if (count($data['data'] ?? []) === 0)
            <div
                style="text-align:center; font-size: 18px; font-weight:bold; color: #2c243a; margin: 0px auto; background-color: #e8e8e8; padding: 10px">
                Tidak Terdapat Catatan!
            </div>
        @endif
    @endforeach

</body>

</html>
