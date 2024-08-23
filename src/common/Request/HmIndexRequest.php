<?php

namespace hoo\io\common\Request;

use Illuminate\Support\Str;

class HmIndexRequest extends BaseRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        switch ($action_name) {
            case 'runCommand:GET':
                $rules = [
                    'submitTo' => 'bail|required',
                ];
                break;
            case 'runCommand:POST':
                $rules = [
                    'value' => 'bail|required',
                ];
                break;
            case 'runCode:GET':
                $rules = [
                    'submitTo' => 'bail|required',
                ];
                break;
            case 'runCode:POST':
                $rules = [
                    'value' => 'bail|required',
                ];
                break;
            default:
                $rules = [];
                break;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'value' => '请输入命令',
        ];
    }
}
