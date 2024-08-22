<?php

namespace hoo\io\common\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class HmLoginRequest extends FormRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        return match ($action_name) {
            'login:POST' => [
                'name' => 'bail|required',
                'password' => 'bail|required',
            ],
            default => [],
        };
    }
}
