<section class="sample-module">
    <div class="container">
        <div class="">
            <h2 class="alert-success alert">{{$moduleInfo['dataType']}} Module</h2>
            <p>
                This is a Service Later Module. {{$moduleInfo['dataHandler']}}
            </p>
            <div id="serviceLater" class="mb-3 p-3 btn btn-outline-primary">Click to load via ajax</div>
            <div id="serviceLaterContent" class="mb-3 p-3"></div>
        </div>

    </div>
</section>

<script>
    let url = '{{$moduleInfo["dataHandler"]}}';
    function loadServiceLater() {
        document.getElementById("serviceLater").innerHTML = "Please wait...";
        document.getElementById("serviceLaterContent").innerHTML = "";
        let params = {};
        function reqListener() {
            //console.log(this.responseText);
            document.getElementById("serviceLaterContent").innerHTML = this.responseText;
            document.getElementById("serviceLater").innerHTML = "Click to load via ajax";
        }
        const req = new XMLHttpRequest();
        req.addEventListener("load", reqListener);
        req.open("GET", url);
        req.send();
    }

    document.getElementById("serviceLater").addEventListener("click", loadServiceLater);

</script>
