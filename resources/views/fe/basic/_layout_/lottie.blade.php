@php
    $lottieObj = app()->HashtagCms->layoutManager()->getFestivalObject();
    $hasLottie = is_array($lottieObj) && sizeof($lottieObj) >= 1;
@endphp
<script>
    let LottieHandler = {
        hideOnComplete: function (lottieRef, lottieObj) {
            if (lottieObj?.lottieHideOnComplete === true) {
                document.getElementById(lottieRef).addEventListener('frame', (frame)=> {
                    if(Math.round(frame.detail.seeker) === 100) {
                        frame.target.remove();
                    }
                });
            }

        }
    }
</script>
@if($hasLottie)
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    @foreach($lottieObj as $lottieO)
        @php
            $attributes = '';
            $styles = '';
            //loop controls autoplay
            foreach ($lottieO['lottieAttributes'] as $key => $value) {
                //$value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                if (is_bool($value)) {
                    $attributes .= ($value === true) ? $key ." " : "";
                } else {
                    $attributes .= "$key=$value ";
                }

            }
            //loop through lottieStyles
             foreach ($lottieO['lottieStyles'] as $key => $value) {
                $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                $styles .= $key.':'.$value.';';
            }
            $styles = "style=$styles";
             $lottiePlayer = "lottiePlayer_".$lottieO['id'];
        @endphp
        <dotlottie-player id="{{$lottiePlayer}}" src="{{htcms_get_media($lottieO['lottie'])}}"
                {{$attributes}}
                {{$styles}}></dotlottie-player>
        <script>
            LottieHandler.hideOnComplete("{!! $lottiePlayer !!}", {!! json_encode($lottieO) !!});
        </script>
    @endforeach
@endif