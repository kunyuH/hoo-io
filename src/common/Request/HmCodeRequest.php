<?php

namespace hoo\io\common\Request;

use Illuminate\Support\Str;

class HmCodeRequest extends BaseRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        switch ($action_name) {
            case 'save:POST':
                $rules = [
                    'name' => 'bail|required',
                    'label' => 'bail|required',
                    'value' => 'bail|required',
                ];
                break;
            case 'details:GET':
                $rules = [
                    'id' => 'bail|required',
                ];
            case 'delete:POST':
                $rules = [
                    'id' => 'bail|required',
                ];
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
