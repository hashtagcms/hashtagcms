<?php

use Illuminate\Support\Str;

class FormHelper
{
    /**
     * Supported Tags configuration
     * Defines behavior for specific tags
     */
    protected static array $supportedTag = [
        'input' => ['self_closed' => true],
        'textarea' => ['self_closed' => false],
        'label' => ['self_closed' => false],
        'file' => ['self_closed' => true],
        'checkbox' => ['self_closed' => true, 'selected' => 'checked'],
        'radio' => ['self_closed' => true, 'selected' => 'checked'],
        'img' => ['self_closed' => true],
        'br' => ['self_closed' => true],
        'hr' => ['self_closed' => true],
    ];

    /**
     * Standard HTML5 void elements that must be self-closing.
     */
    protected static array $voidElements = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 
        'link', 'meta', 'param', 'source', 'track', 'wbr'
    ];

    /**
     * Create a generic HTML element
     *
     * @param string $tag
     * @param string|null $content
     * @param array $attributes
     * @return string
     */
    public static function element(string $tag, ?string $content = null, array $attributes = []): string
    {
        // For standard usage, if content is provided, we pass it.
        // makeTag logic will prioritize 'text' or 'html' key for content if explicit,
        // otherwise it uses 'value' for backward compatibility with label/textarea.
        // To ensure $content is used as inner HTML, passes it as 'text' attribute key to makeTag 
        // which we will handle in makeTag logic.
        
        if ($content !== null) {
            $attributes['text'] = $content;
        }

        return self::makeTag($tag, [], $attributes);
    }

    /**
     * Create Input Text
     *
     * @param  string  $type
     * @param  string  $name
     * @param  mixed   $value
     * @param  array   $attributes
     * @param  string  $selected
     * @return string
     */
    public static function input(string $type = 'text', string $name = '', $value = '', array $attributes = [], $selected = ''): string
    {
        $arr['name'] = $name;
        $arr['id'] = $attributes['id'] ?? $name;
        $arr['type'] = $type;

        try {
            $arr['value'] = htmlentities((string) $value, ENT_QUOTES, 'UTF-8');
        } catch (\Exception $e) {
            $arr['value'] = $value;
        }

        switch ($type) {
            case 'checkbox':
                // Check if value equals 1 (loose comparison for backward compatibility or strict if desired)
                if ($value == 1) { 
                    $arr['checked'] = self::$supportedTag['checkbox']['selected'];
                }
                // Inline JS for backward compatibility
                $arr['onClick'] = 'this.value = (this.checked==true) ? 1 : 0';
                break;
            case 'radio':
                if ((string)$value === (string)$selected) {
                    $arr['checked'] = self::$supportedTag['radio']['selected'];
                }
                break;
        }

        return self::makeTag('input', $arr, $attributes);
    }

    /**
     * Create Label Tag
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $attributes
     * @return string
     */
    public static function label(string $name = '', string $value = '', array $attributes = []): string
    {
        $arr['name'] = $name;
        $arr['value'] = $value; // This is treated as content by makeTag
        $arr['for'] = $name;

        return self::makeTag('label', $arr, $attributes);
    }

    /**
     * Create Password Tag
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $attributes
     * @return string
     */
    public static function password(string $name = '', string $value = '', array $attributes = []): string
    {
        return self::input('password', $name, $value, $attributes);
    }

    /**
     * Create Checkbox Tag
     *
     * @param  string  $name
     * @param  int|string $value
     * @param  array   $attributes
     * @return string
     */
    public static function checkbox(string $name = '', $value = 0, array $attributes = []): string
    {
        return self::input('checkbox', $name, $value, $attributes);
    }

    /**
     * Create File Input Tag
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $attributes
     * @param  bool    $showPreview
     * @param  int|null $maxHeight
     * @param  int|null $maxWidth
     * @param  string  $innerClass
     * @param  bool    $isDeletable
     * @return string
     */
    public static function file(string $name = '', $value = '', array $attributes = [], bool $showPreview = false, $maxHeight = null, $maxWidth = null, string $innerClass = '', bool $isDeletable = true): string
    {
        $html = [];
        $html[] = self::input('file', $name, '', $attributes);
        $originalFileName = $value;

        if ($showPreview && !empty(trim((string)$value))) {

            $value = (filter_var($value, FILTER_VALIDATE_URL)) ? $value : htcms_get_media($value);

            $imageSupportedByBrowsers = htcms_admin_config('imageSupportedByBrowsers', true);
            $fileInfo = pathinfo($value);
            $extension = $fileInfo['extension'] ?? '';
            $isImage = (in_array(strtolower($extension), $imageSupportedByBrowsers));

            $imgWidth = ($maxWidth !== null) ? " width='$maxWidth' " : '';
            $imgHeight = ($maxHeight !== null) ? " height='$maxHeight' " : '';

            $maxHeightStyle = ($maxHeight === null) ? 'max-height:100px;' : "max-height:{$maxHeight}px;";
            $maxWidthStyle = ($maxWidth === null) ? '' : "max-width:{$maxWidth}px;";

            if ($isImage) {
                $tag = "<a target='_blank' href='{$value}'><img {$imgHeight} {$imgWidth} src='{$value}' alt='Preview' /></a>";
            } else {
                $filename = $fileInfo['filename'] ?? 'file';
                $tag = "<i class='fa fa-paperclip text-danger'></i>&nbsp;<a target='_blank' href='{$value}'>{$filename}.{$extension}</a>";
            }

            $deleteIcon = '';

            if ($isDeletable) {
                // Escaping variables in onclick for safety
                $safeName = htmlspecialchars($name, ENT_QUOTES);
                $safeFileName = htmlspecialchars((string)$originalFileName, ENT_QUOTES);
                $deleteIcon = "&nbsp;<i style='float:left; margin-right: 10px;' title='Delete' class='fa fa-trash-o hand' onclick='document.getElementById(\"__hashtagcms_{$safeName}__\").style.display=\"none\";document.getElementById(\"{$safeName}_deleted\").value=\"{$safeFileName}\"'></i>";
            }

            $html[] = "<div id='__hashtagcms_{$name}__' style='{$maxHeightStyle}{$maxWidthStyle};margin-top:10px;'>
                            <div class='col-sm-9 card;{$innerClass}'>
                                $tag $deleteIcon
                            </div>
                        </div>";
        }

        $html[] = "<input type='hidden' name='{$name}_deleted' id='{$name}_deleted' value='0' />";

        return implode('', $html);
    }

    /**
     * Create Textarea
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $attributes
     * @return string
     */
    public static function textarea(string $name = '', $value = '', array $attributes = []): string
    {
        $arr['name'] = $name;
        $arr['id'] = $attributes['id'] ?? $name;
        $arr['value'] = $value ?? '';
        $arr['rows'] = $attributes['rows'] ?? 4; //default

        return self::makeTag('textarea', $arr, $attributes);
    }

    /**
     * Get Safe Value
     *
     * @param  mixed $value
     * @param  mixed $defaultValue
     * @return mixed
     */
    public static function safeValue($value, $defaultValue = '')
    {
        return $value ?? $defaultValue;
    }

    /**
     * Create SELECT element
     *
     * @param  string  $name
     * @param  array|object  $options
     * @param  array|null   $attributes
     * @param  string|array $selected
     * @param  array|null   $keyValue
     * @param  string|null  $prependSelect
     * @return string
     */
    public static function select(string $name, $options, $attributes = null, $selected = '', $keyValue = null, $prependSelect = null): string
    {
        $attributes = $attributes ?? [];
        $keyValue = $keyValue ?? ['value' => 'id', 'label' => 'name'];
        $html = [];

        // Determine if multiple
        $isMultiple = false;
        if (Str::contains($name, '[]')) {
            $attributes['multiple'] = 'multiple';
            $isMultiple = true;
        }

        // Set ID if missing
        if (!isset($attributes['id'])) {
            $attributes['id'] = $name;
        }

        $html[] = "<select name='{$name}'";
        
        foreach ($attributes as $key => $attribute) {
             $attribute = htmlspecialchars((string)$attribute, ENT_QUOTES, 'UTF-8');
             $html[] = " {$key}='{$attribute}'";
        }
        $html[] = '>';

        if (!$isMultiple) {
            $prependOption = $prependSelect ?? 'Select ';
            if ($prependOption !== '') {
                $html[] = "<option value=''>{$prependOption}</option>";
            }
        }

        foreach ($options as $val => $row) {
            $optValue = '';
            $optLabel = '';

            if ($keyValue === 'plain_array') {
                $optValue = $row;
                $optLabel = ucfirst((string)$row);
            } else {
                // Handle objects
                $rowArray = (is_array($row)) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);

                $optValue = addslashes((string)($rowArray[$keyValue['value']] ?? ''));
                $parentStr = (isset($rowArray['parent_id']) && $rowArray['parent_id'] > 0) ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '';

                if (strpos($keyValue['label'], '.') > 0) {
                    $tmp = explode('.', $keyValue['label']);
                    $labelVal = $rowArray[$tmp[0]][$tmp[1]] ?? '';
                    $optLabel = $parentStr . $labelVal;
                } else {
                    $optLabel = $parentStr . ($rowArray[$keyValue['label']] ?? '');
                }
            }
            
            // Check selection
            $isSelected = '';
            if (!$isMultiple) {
                // Loose comparison might be needed if IDs are string vs int
                if ((string)$optValue === (string)$selected && (string)$selected !== '') {
                    $isSelected = "selected='selected' ";
                }
            } else {
                $selectedArray = is_array($selected) ? $selected : [];
                 if (in_array($optValue, $selectedArray)) {
                    $isSelected = "selected='selected' ";
                }
            }

            $html[] = "<option {$isSelected}value='{$optValue}'>{$optLabel}</option>";
        }

        $html[] = '</select>';

        return implode('', $html);
    }

    /**
     * Make Tags
     *
     * @param  string  $tagName
     * @param  array  $requiredAttributes
     * @param  array  $attributes
     * @return string
     */
    private static function makeTag(string $tagName = '', array $requiredAttributes = [], array $attributes = []): string
    {
        $mergedAttributes = array_merge($requiredAttributes, $attributes);
        $attrList = [];
        
        $value = '';
        
        // Determine self-closing status:
        // 1. Check strict config
        // 2. Check void elements list
        // 3. Default to false (standard open/close tag)
        if (isset(self::$supportedTag[$tagName])) {
            $isSelfClosed = self::$supportedTag[$tagName]['self_closed'];
        } else {
            $isSelfClosed = in_array(strtolower($tagName), self::$voidElements);
        }

        // Handling Content:
        // If not self-closed, we need to find the content.
        // Priority:
        // 1. 'text' attribute (explicit usage)
        // 2. 'value' attribute (legacy usage for textarea/label)
        if (!$isSelfClosed) {
            
            if (isset($mergedAttributes['text'])) {
                $value = $mergedAttributes['text'];
                unset($mergedAttributes['text']);
                // If we used 'text', we leave 'value' alone (it remains an attribute)
            } elseif (isset($mergedAttributes['value'])) {
                $value = $mergedAttributes['value'];
                unset($mergedAttributes['value']);
            }
        } else {
            // IF self-closed but 'text' is passed, it just disappears/is unused, which is correct.
            // But we must remove 'text' if validation is strict, though HTML ignores extra attrs.
            // We should remove 'text' anyway as it's not a valid HTML attribute usually.
            if (isset($mergedAttributes['text'])) {
                 unset($mergedAttributes['text']);
            }
            // 'value' remains as attribute for inputs
        }
        

        foreach ($mergedAttributes as $key => $val) {
            // Encode attribute values
            $val = htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
            $attrList[] = "{$key}='{$val}'";
        }
        
        $attributesString = implode(' ', $attrList);
        
        if (!$isSelfClosed) {
             return "<{$tagName} {$attributesString}>{$value}</{$tagName}>";
        }

        return "<{$tagName} {$attributesString} />";
    }
}


