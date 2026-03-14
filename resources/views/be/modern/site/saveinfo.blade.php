@include(htcms_admin_config('theme').'.common.saveinfo')
<script>
    //Whenever site is updated, we want to remove localStorage
    //allSites key is being used in GlobalSiteButton.vue component
    //It comes on top-bar when you have multiple site
    if(localStorage) {
        //localStorage.removeItem("allSites");
    }
</script>
