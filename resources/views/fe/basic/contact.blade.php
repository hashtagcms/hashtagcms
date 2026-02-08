<section style="margin-top:30px">
    <div class="container mt-30">
        <div class="card">
            <div class="card-header">{{htcms_trans("hashtagcms::modules.Contact Us")}}</div>
            <div class="card-body">
                <form data-form="contact-form" action="/common/contact" method="post">
                    @csrf
                    <div class="form-group v-space">
                        <label for="name">{{htcms_trans("hashtagcms::modules.Name")}}</label>
                        <input type="text" required placeholder="{{htcms_trans("hashtagcms::modules.Please enter your full name")}}" id="name" name="name" class="form-control input">
                    </div>
                    <div class="form-group v-space">
                        <label for="email">{{htcms_trans("hashtagcms::modules.Email")}}</label>
                        <input type="email" required placeholder="{{htcms_trans("hashtagcms::modules.Please enter your email")}}" id="email" name="email" class="form-control input">
                    </div>
                    <div class="form-group v-space">
                        <label for="phone">{{htcms_trans("hashtagcms::modules.Phone")}}</label>
                        <input type="text" placeholder="{{htcms_trans("hashtagcms::modules.Please enter your phone number")}}" id="phone" name="phone" class="form-control input">
                    </div>
                    <div class="form-group v-space">
                        <label for="comment">{{htcms_trans("hashtagcms::modules.Comment")}}</label>
                        <textarea required placeholder="{{htcms_trans("hashtagcms::modules.Please tell us your query")}}" id="comment" name="comment" class="form-control input"></textarea>
                    </div>

                    <div class="form-group row v-space">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ htcms_trans('hashtagcms::modules.Submit') }}
                            </button>
                        </div>
                    </div>

                    <div class="alert col-12 mt-3" data-message-holder="contact-message-holder" style="display: none; background-color: lightyellow; color:#000">
                        <span class="message" data-class="contact-message"></span> 
                        <span data-class="contact-close" title="Close" class="pull-right pointer hand" style="background-color: #5f5f5f; color:#fff;padding:1px 5px">x</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        // Initialize the standard Contact component from HashtagCms SDK
        // This handles validation, AJAX submission, and message display automatically
        if (typeof HashtagCms !== 'undefined' && HashtagCms.Contact) {
            const contactForm = new HashtagCms.Contact({
                form: 'form[data-form="contact-form"]',
                messageHolder: 'div[data-message-holder="contact-message-holder"]'
            });
        }
    </script>
@endpush
