<?php

namespace HashtagCms\Http\Traits;

use Illuminate\Support\Facades\Validator;
use HashtagCms\Models\Contact;
use HashtagCms\Models\Subscriber;

trait CommonLogic
{

    /**
     * Get Contact Validation Rules
     * @return array
     */
    public function getContactRules(): array
    {
        return [
            'name' => 'required|max:255|string',
            'email' => 'required|max:255|email',
            'phone' => 'nullable|max:16|string',
            'comment' => 'required|string',
            'site_id' => 'nullable|integer'
        ];
    }

    /**
     * Get Subscriber Validation Rules
     * @return array
     */
    public function getSubscriberRules(): array
    {
        return [
            'email' => 'required|max:255|email',
            'site_id' => 'nullable|integer'
        ];
    }

    /**
     * Validate Data
     * @param array $data
     * @param array $rules
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateData(array $data, array $rules): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, $rules);
    }

    /**
     * Save Contact Logic
     * @param array $data
     * @return Contact
     */
    public function saveContact(array $data)
    {
        $pattern = '/(script.*?(?:\/|&#47;|&#x0002F;)script)/ius';
        if (function_exists('sanitize')) {
            $data['comment'] = sanitize($data['comment']);
        } else {
            $data['comment'] = htmlspecialchars($data['comment']);
        }

        // whitelist fields
        $saveData = \Illuminate\Support\Arr::only($data, ['name', 'email', 'phone', 'comment', 'site_id']);

        return Contact::create($saveData);
    }

    /**
     * Save Subscriber Logic
     * @param array $data
     * @return array
     */
    public function saveSubscriber(array $data): array
    {
        if (Subscriber::where('email', $data['email'])->where('site_id', $data['site_id'])->count() == 0) {

            // whitelist fields
            $saveData = \Illuminate\Support\Arr::only($data, ['email', 'site_id']);

            Subscriber::create($saveData);
            return ['success' => true, 'message' => 'Thank you.'];
        } else {
            return ['success' => true, 'message' => 'You are already subscribed with us.'];
        }
    }

}
