<div class="container" style="margin-top: 100px;margin-bottom: 100px">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('success'))
                <div class="alert alert-success" role="alert" id="success_msg" style="margin-bottom: 10;">
                    {{session('success')}} <span title="Close" style="float:right; cursor: pointer" onclick="document.getElementById('success_msg').style.display = 'none'">
                                    <i class="fa fa-times"></i>
                                </span>
                </div>
            @endif
            @php
            $name = $user['name'];
            @endphp
            <div class="card">
                <div class="card-header">Profile</div>
                <div class="card-body">
                    @if($user != null)
                    <form method="POST" action="/profile/store">
                        @csrf
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" required class="form-control" id="name" name="name" aria-describedby="name" placeholder="Please enter your name" value="{{$name}}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="father_name">Father's Name</label>
                                        <input type="text" required class="form-control" id="father_name" name="father_name" aria-describedby="father_name" placeholder="Please enter your father's name" value="{{$profile['father_name']}}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="mother_name">Mother's Name</label>
                                        <input type="text" required class="form-control" id="mother_name" name="mother_name" aria-describedby="mother_name" placeholder="Please enter your mother's name" value="{{$profile['mother_name']}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input type="email" disabled class="form-control" id="email" name="email" aria-describedby="email" placeholder="Please enter your email" value="{{$user['email']}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" required class="form-control" id="mobile" name="mobile" aria-describedby="name" placeholder="Please enter your mobile number" value="{{$profile['mobile']}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="date" required class="form-control" id="date_of_birth" name="date_of_birth" aria-describedby="date_of_birth" placeholder="Please enter your date of birth" value="{{$profile['date_of_birth']}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        {!! FormHelper::select('gender', $genders, array('class'=>'form-control', 'required'=>'required'), $profile['gender'], 'plain_array') !!}
                                    </div>
                                </div>

                            </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Submit') }}
                                </button>

                            </div>
                        </div>
                    </form>
                    @else
                        <p>You need to <a href="/login?redirect=/profile">login</a>  to view your profile.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

