<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@1,800&display=swap" rel="stylesheet">
</head>
<style>
    @media print {
        @page {
            margin: 0;
        }
    }

    body,
    html {

        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Nunito', sans-serif;
    }

    .print-area-container {
        display: block;
        width: 100%;
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 0;
        margin-top: 20px;
        padding: 0;
    }




    .data-block {
        padding-left: 30px;
        padding-right: 30px;
        padding-bottom: 15px;
    }


    .small-data-line {
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 8px;
    }

    .data-line {
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 12px;
    }

    .big-data-line {

        text-align: left;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 18px;


    }

    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .justify {

        text-align: justify;

    }

    .barcode {

        text-align: center;
        padding-top: 5px;
    }
</style>

<body>

    <div class="print-area-container">

        <div class="data-block">
            <p class="big-data-line center">DROP-OFF RECEIPT</p>



        </div>

        <div class="data-block">
            <p class="data-line left">{{ $company_profile->name ?? '' }}</p>
            <p class="data-line left">
                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone) }}</p>
            <p class="data-line left">{{ $company_profile->email }}</p>
            <p class="data-line left">{{ $company_profile->address }}</p>

        </div>



    </div>

    <div class="data-block">
        <p class="big-data-line center">REPAIR #{{ $repair->id }}</p>



        <p class="data-line center">{{ date_format($repair->created_at, 'M d, Y') }}</p>
    </div>

    <div class="data-block">
        @if (isset($repair->customer_data))
            <p class="big-data-line">CUSTOMER</p>
            <p class="data-line">{{ $repair->customer_data->company }}</p>
            <p class="data-line">{{ $repair->customer_data->first_name }} {{ $repair->customer_data->last_name }}</p>
            <p class="data-line">
                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone) }}</p>
            <p class="data-line">{{ $repair->customer_data->email }}</p>
            <p class="data-line">{{ $repair->customer_data->address }} {{ $repair->customer_data->city }}
                {{ $repair->customer_data->state }} {{ $repair->customer_data->zip }}</p>
        @endif
    </div>

    <div class="data-block">
        <p class="big-data-line">REPAIR</p>
        <p class="data-line">{{ $repair->target }}</p>
        <p class="data-line">{{ $repair->request }}</p>
    </div>

    <div class="data-block">
        @if (isset($repair->estimate))
            <p class="big-data-line right">Estimate ${{ number_format((float) $repair->estimate, 2, '.', '') }}</p>
        @endif
    </div>




    @if (!$jobs->isEmpty())
        <div class="data-block">
            <p class="big-data-line">JOBS</p>
            @foreach ($jobs as $job)
                <p class="data-line">{{ $job->data }}</p>
                <p class="data-line">{{ date_format($job->created_at, 'M d, Y') }}</p>
            @endforeach
        </div>
    @endif
    </div>

    <div class="data-block">

        <div class="barcode">
            @php echo DNS1D::getBarcodeSVG('REP'.$repair->id, 'C39',2,50,'black' ,false); @endphp
        </div>


    </div>

    <div class="data-block">

        <p class="small-data-line justify">{{ $company_profile->terms }}</p>

    </div>

</body>

</html>
