<section class="sample-module">
    <div class="container">
            <h2>{{$moduleInfo['dataType']}} Module</h2>
        <p>
            This is a service module. It returns data from a service.
        </p>
        <p>
            <strong>Service URL:</strong>  https://picsum.photos/v2/list?limit=4
        </p>
        @if(sizeof($data) == 0)
            <div class="alert alert-danger">Please check the logs. There is some error while loading the service.</div>
        @else
            <div class="row">
                @foreach($data as $pic)
                    <div class="col-lg-5 mb-5">
                        <img width="300" src="{{$pic['download_url']}}">
                        {{$pic['author']}}
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
