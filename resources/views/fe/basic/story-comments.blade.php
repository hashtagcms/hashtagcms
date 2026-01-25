@if(isset($isBlogHome) && $isBlogHome == 1)

@else
    @php
        $commentsCount = isset($data) ? sizeof($data) : 0;
        $hasComments =  $commentsCount > 0;
    @endphp
    <section class="section-comments">
        <div class="container">
            @if($hasComments)
                <hr/>
                <fieldset>
                    <legend>
                        <h3>Comments <span class="badge badge-dark">{{$commentsCount}}</span></h3>
                    </legend>
                    <div class="row mb-3 mt-3">
                        <div class="col-lg-12 text-right mt-1">

                        </div>
                    </div>
                    @foreach($data as $comment)
                        <div class="row">
                            <div class="col-lg-2 mb-5 text-center image-column">
                                <div class="rounded-circle image" style="width:75px;height:75px">
                                    @php
                                        $email = md5($comment['email']);
                                    @endphp
                                    <img src="https://www.gravatar.com/avatar/{{$email}}?d=&s=75}}" />
                                </div>
                                <span class="author">
                            {{$comment['name']}}
                        </span>
                                <span class="said">said on</span>
                                <span class="date"><span class="fa fa-calendar-o"></span> {{getFormattedDate($comment['created_at'])}}</span>
                            </div>
                            <div class="col-lg-6">
                                <div style="" class="" role="tooltip" x-placement="right">
                                    <div class="arrow" style="top: 16px;"></div>
                                    <h3 class="popover-header"></h3>
                                    <div class="popover-body">{{$comment['comment']}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </fieldset>
            @else
            <legend style="margin-bottom: 100px; display: none">
            <span class="first-text">
                Be the first to write a comment.
            </span>
                </legend>
            @endif
        </div>
    </section>
@endif
