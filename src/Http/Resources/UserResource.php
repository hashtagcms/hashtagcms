<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'emailVerifiedAt' => $this->email_verified_at,
            'facebookUserId' => $this->facebook_user_id,
            'googleUserId' => $this->google_user_id,
            'userType' => $this->user_type
        ];
        if (isset($this->roles)) {
            $data['roles'] = $this->roles->pluck("name");
        }
        //site_user
        if (isset($this->sites)) {
            $data['sites'] = $this->sites->pluck(["context"]);
        }
        if (isset($this->profile)) {
            $data['profile'] = [
                'fatherName' => $this->profile->father_name,
                'motherName' => $this->profile->mother_name,
                'idCardType' => $this->profile->id_card_type,
                'idCardNumber' => $this->profile->id_card_number,
                'mobile' => $this->profile->mobile,
                'dateOfBirth' => $this->profile->date_of_birth,
                'gender' => $this->profile->gender,
            ];
        }

        return $data;
    }
}
