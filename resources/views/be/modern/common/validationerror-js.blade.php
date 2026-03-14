@push('scripts')
    @if ($errors->any())
        <script>window.error_messages = {!! json_encode($errors->messages(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};</script>
    @endif
    <script src="{{htcms_admin_asset('js/error-handler.js')}}"></script>
@endpush
