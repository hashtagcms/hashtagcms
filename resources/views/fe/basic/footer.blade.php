<footer class="footer">
    <div class="container">
        <div class="footer-widgets">
            <div class="row">
                <div class="col-6 col-md-4">
                    <h5 class="widget-title">{{__('hashtagcms::links.support')}}</h5>
                    <ul class="list-footer">
                        <li class="nav-item"><a class="nav-link"
                                href="{{htcms_get_path('contact')}}">{{__('hashtagcms::links.contact_us')}}</a></li>
                        <li class="nav-item"><a class="nav-link"
                                href="{{htcms_get_path('support/tnc')}}">{{__('hashtagcms::links.tnc')}}</a></li>
                    </ul>
                </div>                
            </div>
        </div>
    </div> <!-- end container -->

    <div class="copyright">
        <div class="container text-center">
            <span class="copyright">
                &copy; @php echo date("Y") . " " . htcms_get_site_info("name"); @endphp
                <br>
            </span>
        </div>
    </div> <!-- end footer bottom -->
</footer> <!-- end footer -->