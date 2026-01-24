
@php
$name = old('name');
$email = old('email');
$phone = old('phone');
$comment = old('comment');

@endphp
<section style="margin-top:30px">
    <div class="container mt-30">
        @if (session('success'))
            <div class="alert alert-success" role="alert" id="success" style="margin-bottom: 0;">
                {{session('success')}} <span title="Close" class="fa fa-close" style="float:right; cursor: pointer" onclick="document.getElementById('success').style.display = 'none'">&nbsp;</span>
            </div>
        @endif
            <div class="card">
                <div class="card-header">{{htcms_trans("hashtagcms::modules.Contact Us")}}</div>
                <div class="card-body">
                    <form action="/common/contact" method="post">
                        @csrf
                        <div class="form-group v-space">
                            <label for="name">{{htcms_trans("hashtagcms::modules.Name")}}</label>
                            <input type="text"  required placeholder="{{htcms_trans("hashtagcms::modules.Please enter your full name")}}" id="name" name="name" class="form-control input" value="{{$name}}">
                            @error('name')
                            <div class="error text-danger">{{ $errors->first('name') }}</div>
                            @enderror
                        </div>
                        <div class="form-group v-space">
                            <label for="email">{{htcms_trans("hashtagcms::modules.Email")}}</label>
                            <input type="email" required  placeholder="{{htcms_trans("hashtagcms::modules.Please enter your email")}}" id="email" name="email" class="form-control input" value="{{$email}}">
                            @error('email')
                            <div class="error text-danger">{{ $errors->first('email') }}</div>
                            @enderror
                        </div>
                        <div class="form-group v-space">
                            <label for="phone">{{htcms_trans("hashtagcms::modules.Phone")}}</label>
                            <input type="text" placeholder="{{htcms_trans("hashtagcms::modules.Please enter your phone number")}}" id="phone" name="phone" class="form-control input" value="{{$phone}}">
                            @error('phone')
                            <div class="error text-danger">{{ $errors->first('phone') }}</div>
                            @enderror
                        </div>
                        <div class="form-group v-space">
                            <label for="comment">{{htcms_trans("hashtagcms::modules.Comment")}}</label>
                            <textarea type="text" required placeholder="{{htcms_trans("hashtagcms::modules.Please tell us your query")}}" id="comment" name="comment" class="form-control input">{{$comment}}</textarea>
                            @error('comment')
                            <div class="error text-danger">{{ $errors->first('comment') }}</div>
                            @enderror
                        </div>

                        <div class="form-group row v-space">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ htcms_trans('hashtagcms::modules.Submit') }}
                                </button>

                            </div>
                        </div>
                    </form>


                </div>
            </div>
    </div>
</section>
