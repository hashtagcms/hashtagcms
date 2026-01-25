<search-bar
            controller-name="{{request()->module_info->controller_name}}"
            selected-params="{{json_encode(isset($searchParams) ? $searchParams : [])}}"
            fields="{{json_encode($fieldsName)}}"
            action-fields="{{json_encode($actionFields)}}"
></search-bar>
