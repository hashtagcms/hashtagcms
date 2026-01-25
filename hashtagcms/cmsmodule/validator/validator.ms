<?php

namespace {{namespace}}Http\Requests\Admin;

use App\Core\Traits\Admin\Viewer;
use Illuminate\Foundation\Http\FormRequest;


class {{validator_name}} extends FormRequest
{
    use Viewer;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkPolicy('edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [
                {{validation_fields}}
        ];

        if($this->input("id")==0) {

            return $data;

        } else {

            return $data;
        }

    }

}
