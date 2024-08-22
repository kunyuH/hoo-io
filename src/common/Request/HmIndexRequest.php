<?php

namespace hoo\io\common\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class HmIndexRequest extends FormRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        return match ($action_name) {
            'runCommand:GET' => [
                'submitTo' => 'bail|required',
            ],
            'runCommand:POST' => [
                'value' => 'bail|required',
            ],
            'runCode:GET' => [
                'submitTo' => 'bail|required',
            ],
            'runCode:POST' => [
                'value' => 'bail|required',
            ],
            default => [],
        };
    }

    public function messages()
    {
        return [
            'value' => '请输入命令',
        ];
    }
}
