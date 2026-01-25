<section class="sample-module">
    <div class="container">
        <h2 class="alert-success alert">{{$moduleInfo['dataType']}} Module</h2>
        <p>
            This is a combination of service and query module. Sometime we need data from service and query both. <br />
            In this case data has two properties. ie. data and queryData
        </p>
        <p>
            <strong>Service URL:</strong>  https://picsum.photos/v2/list?page=2&limit=4
        </p>
        <p>
            <strong>Query:</strong>  select * from site_props where site_id=:site_id and group_name="SocialLinks" and is_public=1 and deleted_at is null;
        </p>
        <h3>Service Data</h3>
        <div class="row">
            @php
                $serviceData = $data['data'];
                $queryData = $data['queryData'];
            @endphp
            @if(sizeof($serviceData) == 0)
                <div class="alert alert-danger">Please check the logs. There is some error while loading the service.</div>
            @else
                @foreach($serviceData as $pic)
                    <div class="col-lg-5 mb-5">
                        <img height="150" src="{{$pic['download_url']}}">
                        {{$pic['author']}}
                    </div>
                @endforeach
            @endif
</div>
<div class="row">
<h3 class="ml-1">Query Data</h3>
</div>
<div class="row">
{{json_encode($queryData)}}
</div>
</div>
</section>
