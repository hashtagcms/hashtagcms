
<section class="section-blogs">
    <div class="container">
        @if(isset($data) && count($data) > 0)
            @php
                $data = $data[0];
                $hasContent = true;
                $categoryId = $data['category_id'];
                $contentId = $data['id'];

                $storyObj = htcms_get_shared_data('MODULE_STORY');
                $title = isset($storyObj) ? $storyObj[0]['name'] : htcms_get_category_info('name');
            @endphp
            <h1>{{$title}}</h1>
            {!! $data['page_content'] !!}
        @else
            @php
                $hasContent = false;
            @endphp

        @endif
    </div>
</section>

@php
    $user = auth()->user();
    if($user != null) {
        $name = $user->name. " ".$user->last_name;
        $email = $user->email;
    } else {
        $name = "";
        $email = "";
    }
    $name = old("name", $name);
    $email = old("email", $email);
    $comment = old("comment");
    $sessionResults = session('results');
    $successMessage = $sessionResults['message'] ?? "";
@endphp


@if($hasContent)
    @if($data['enable_comments'] == 1)
        <section class="section-comments">
            <div class="container">
                <h2>Post Comments</h2>
                <form class="comment-form relative" action="/comment/saveComment" method="post">
                    @csrf
                    <input type="hidden" name="category_id" value="{{$categoryId}}" />
                    <input type="hidden" name="page_id" value="{{$contentId}}" />
                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <label for="_htcms_form_comment_name_">Name</label>
                        </div>
                        <div class="col-sm-10">
                            <input id="_htcms_form_comment_name_" name="name" type="text" class="form-control" required placeholder="Please enter your name" value="{{$name}}" />
                        </div>
                        <div class="text text-danger"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <label for="_htcms_form_comment_email_">Email</label>
                        </div>
                        <div class="col-sm-10">
                            <input id="_htcms_form_comment_email_" type="email" name="email" class="form-control" required placeholder="Please enter your email  " value="{{$email}}" />
                        </div>
                        <div class="text text-danger"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <label for="_htcms_form_comment_comment_">Comment</label>
                        </div>
                        <div class="col-sm-10">
                            <textarea id="_htcms_form_comment_comment_" name="comment" class="form-control" rows="7">{{$comment}}</textarea>
                            <div class="text text-danger"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-10 text-center">
                            <button class="btn btn-lg btn-outline-secondary mb-1" type="submit">Submit</button>
                            <div class="alert text-success">
                                {{$successMessage}}
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </section>
    @endif
@endif
