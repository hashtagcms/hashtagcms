<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->
<header>
<span style="float:right; position: relative; top:0; padding: 10px">
    <?php
        if(htcms_get_language_id() === 1) {
        echo '<a href="/hi/web/">हिंदीं</a>';
    } else {
        echo '<a href="/en/web/">English</a>';
    }
    ?>
</span>
<!-- header-start -->
<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{htcms_get_path('/')}}"> {{htcms_get_site_info("name")}} </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px; overflow: auto">
                 @php
                    echo htcms_get_header_menu_html($data, null, null);
                 //echo is_array($data) ? "haan array hai" : "kya baar kar raha hai?";
                 @endphp
            </ul>
            <div class="d-flex">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll ms-1" style="--bs-scroll-height: 100px; overflow: auto">
                     @auth
                                        @if(strtolower(auth()->user()->user_type) == 'staff')
                                            <li class="nav-item">
                                                <a href="{{htcms_admin_path('dashboard')}}" class="nav-link" title="Admin"><i aria-hidden="true" class="fa fa-dashboard"></i> <span class="text">
                                                        {{__('hashtagcms::links.dashboard')}}
                                                    </span></a>
                                            @endif
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{htcms_get_path('profile')}}" class="nav-link" title="Profile"><i aria-hidden="true" class="fa fa-user"></i> <span class="text">
                                                        {{__('hashtagcms::links.profile')}}
                                                    </span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{htcms_get_path('logout')}}" class="nav-link" title="Logout"><i aria-hidden="true" class="fa fa-sign-out"></i> <span class="text">
                                                        {{__('hashtagcms::links.logout')}}
                                                    </span></a>
                                            </li>
                                    @endauth
                    @guest
                    <li class="nav-item">
                        <a href="{{htcms_get_path('login')}}" class="nav-link"><span>{{__('hashtagcms::links.login')}}</span></a>
                    </li>
                    <li class="nav-item">
                                <a href="{{htcms_get_path('register')}}" class="nav-link"><span>{{__('hashtagcms::links.register')}}</span></a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</nav>
</header> <!-- end navigation -->

