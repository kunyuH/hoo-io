<?php

namespace hoo\io\common\Request;

use Illuminate\Support\Str;

class HmLoginRequest extends BaseRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        switch ($action_name) {
            case 'login:post':
                $rules = [
                    'email' => 'required|email',
                    'password' => 'required',
                ];
                break;
            default:
                $rules = [];
                break;
        }
        return $rules;
    }
}
