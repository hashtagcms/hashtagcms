<!-- slider_area_start -->
@php
    $rnm = rand ( 1 , 2);
@endphp
<section class="hero" id="hero">    
    <div class="hero-inner hero-bg-{{$rnm}}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 hero-text">
                {!! htcms_trans("hashtagcms::modules.hero_text_1") !!}
                </div>
            </div>
            <div class="row">
                            <div class="col-lg-4 hero-text">
                            {!! htcms_trans("hashtagcms::modules.hero_text_2") !!}
                            </div>
            </div>
        </div>
    </div>    
</section>
