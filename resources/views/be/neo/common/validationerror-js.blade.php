@push('scripts')
    @if ($errors->any())
        <script>window.error_messages = JSON.parse('<?php echo json_encode($errors->messages()); ?>');</script>
        <script src="{{htcms_admin_asset('js/error-handler.js')}}"></script>
    @endif
@endpush
