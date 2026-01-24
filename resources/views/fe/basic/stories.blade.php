@php
    $results = $data['results'];
    $hasData = count($results) > 0;
@endphp

<section class="section-blogs">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-6">
                @if($hasData)
                    @foreach($results as $index=>$story)
                        <div class="card shadow blog">
                            <div class="card-body">
                                <div>
                                    <div class="pull-right">
                                    <span class="blog-date shadow-common">
                                            <span class="fa fa-calendar-o icon"></span>
                                            <span class="text">{{getFormattedDate($story['createdAt'])}}
                                            </span>
                                    </span>
                                    </div>
                                    <h2><a href="{{htcms_get_path($story['categoryLinkRewrite'].'/'.$story['linkRewrite'])}}">{{$story['title']}}</a></h2>
                                </div>
                                {!!  $story['description'] !!}

                                <p class="more">
                                    <a href="{{htcms_get_path($story['categoryLinkRewrite'].'/'.$story['linkRewrite'])}}">Read More</a>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="pull-left author">
                                   @if(!empty($story['author'])) Author: {{$story['author']}} @endif &nbsp;
                                </div>
                                <div class="pull-right">
                                    <span class="fa fa-comment-o"></span> {{$story['commentsCount'] ?? 0}} Comments
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card shadow blog">
                        <div class="card-body">
                            There is no post for now.
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-3 col-6">
                <div style="border: 1px solid #c3c3c3; background: #f3f3f3; width: 350px; height: 250px; padding:5px">
                    Ad Content
                </div>
            </div>
        </div>
    </div>
</section>
