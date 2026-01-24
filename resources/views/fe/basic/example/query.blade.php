<section class="sample-module">
    <div class="container">
        <div>
            <h2 class="alert alert-success">{{$moduleInfo['dataType']}} Module</h2>
            <p>
                {{__('hashtagcms::messages.desc_query_module')}}
            </p>
            <p>
                <strong>Query:</strong>  select * from comments where deleted_at is null limit 5
            </p>
            @if(isset($data))
                @foreach($data as $comment)
                <div class="row">
                    <div class="col-lg-2 mb-5 text-center image-column">
                        <span class="author">
                            {{$comment['name']}}
                        </span>
                    </div>
                    <div class="col-lg-6">
                        <div style="" class="">
                            <div class="arrow" style="top: 16px;"></div>
                            <h3 class="popover-header"></h3>
                            <div class="popover-body">{{$comment['comment']}}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div>No data available</div>
            @endif

        </div>

    </div>
</section>
