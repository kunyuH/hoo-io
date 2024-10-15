<?php

namespace hoo\io\monitor\hm\Request;

use Illuminate\Support\Str;

class LogViewerRequest extends BaseRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        switch ($action_name) {
            case 'showLog:GET':
                $rules = [
                    'path' => 'bail|required',
                ];
                break;
            case 'details:GET':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            default:
                $rules = [];
                break;
        }
        return $rules;
    }
}
