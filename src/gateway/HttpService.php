<?php

namespace hoo\io\gateway;


use GuzzleHttp\Exception\GuzzleException;
use hoo\io\common\Exceptions\HooException;
use hoo\io\http\HHttp;
use hoo\io\http\Resources\InnerResource;

class HttpService
{

    private static $config_domain = 'config::';
    private static $input_domain = 'input::';

    /**
     * 服务代理
     * @param $request
     * @return InnerResource
     */
    public function gateway($request)
    {
        # 代理地址提取
        list($input,$_,$gateway_host,$gateway_api,$gateway_method,$_,$header) = $this->getGatewayInfo($request);

        # 携带参数 作用域:参数:算法 解析
        $input = $this->getGatewayArg($input);

        $data = $this->send($gateway_host,$gateway_api,$gateway_method,$input,$header);
        return $data;
    }

    /**
     * 第三方接口代理
     * @param $gateway_host
     * @param $gateway_api
     * @param $gateway_method
     * @param $input
     * @param array $headers
     * @return mixed
     * @throws GuzzleException
     * @throws HooException
     */
    public function send($gateway_host, $gateway_api, $gateway_method, $input, array $headers=[]): mixed
    {
        # 如果末尾有斜杠 则去除这个斜杠
        $gateway_host = rtrim($gateway_host,'/');
        # 如果首位没有斜杠 则补充一个斜杠
        $gateway_api = '/'.ltrim($gateway_api,'/');

        $gateway_url = $gateway_host.$gateway_api;

        $http = new HHttp();
        $res = $http->request($gateway_method,$gateway_url,
            [
                'headers' => $headers,
                'json' => $input
            ]
        );
        return json_decode($res->getBody()->getContents(),true);
    }

    /**
     * 获取代理信息
     * @param $request
     * @return array
     * @throws \Exception
     */
    public function getGatewayInfo($request): array
    {
        /**
         * 规定代理地址信息 存放位置
         * header
         * 1.header内gateway-mid             接口需执行的中间件
         * 2.header内gateway-host            代理服务host
         * 3.header内gateway-api             代理接口
         * 4.header内gateway-method          代理请求方式
         * 5.header内gateway-data-model      代理服务 数据处理模型
         * form-data
         * 3.form-data内header
         * header = [
         *      gateway-mid
         *      gateway-host
         *      gateway-api
         *      gateway-method
         *      gateway-data-model
         * ]
         */
        $input = $request->input();
        if($request->header('gateway-api')){
            $gateway_mid = $request->header('gateway-mid');
            $gateway_host = $request->header('gateway-host');
            $gateway_api = $request->header('gateway-api');
            $gateway_method = $request->header('gateway-method');
            $gateway_data_model = $request->header('gateway-data-model');

            $header = array_map(function ($v){
                return $v[0]??null;
            },$request->header());

        }else{
            $input = $request->input();
            $header = $input['header']??[];
            if(is_json($header)){
                $header = json_decode($header,true);
            }
            $gateway_mid = $header['gateway-mid']??'';
            $gateway_host = $header['gateway-host']??'';
            $gateway_api = $header['gateway-api']??'';
            $gateway_method = $header['gateway-method']??'';

            $gateway_data_model = $header['gateway-data-model']??'';

            unset($input['header']);
        }

        if(empty($gateway_host) or empty($gateway_api) or empty($gateway_method)){
            throw new HooException('缺失代理信息：gateway-host；gateway-api或gateway-method！');
        }

        unset($header['gateway-mid']);
        unset($header['gateway-host']);
        unset($header['gateway-api']);
        unset($header['gateway-method']);
        unset($header['gateway-data-model']);

        if(config('hoo-io.GATE_MODE') == 'strict'){
            if(empty($this->is_in_domain($gateway_host))){
                throw new HooException('gateway-host 信息不合法！');
            }
        }

        $gateway_mid = $this->getArg($gateway_mid);
        $gateway_host = $this->getArg($gateway_host);
        $gateway_api = $this->getArg($gateway_api);
        $gateway_method = $this->getArg($gateway_method);
        $gateway_data_model = $this->getArg($gateway_data_model);

        return [$input,$gateway_mid,$gateway_host,$gateway_api,$gateway_method,$gateway_data_model,$header];
    }

    /**
     * 代理携带参数解析
     * 作用域:参数:算法 解析
     * @param $input
     * @return mixed
     */
    public function getGatewayArg($input): mixed
    {
        # 作用域:参数:算法 解析
        foreach ($input as $key=>&$value){
            $value = $this->getArg($value);
        }

        # 忽略传递的input
        foreach (config('hoo-io.GATE_IGNORE_INPUT') as $item){
            if(empty($item)){
                continue;
            }
            if(isset($input[$item])){
                unset($input[$item]);
            }
        }

        return $input;
    }

    /**
     * 获取参数 通过格式 转换为参数
     * 值域::数据key::算法
     * 例如 input::username::md5
     * @param $value
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed|string
     */
    private function getArg($value)
    {
        # true 是字符串 且 前6位为input:
        if(is_string($value)){
            # 分割字符串
            $value_ = explode('::',$value);
            $arg = $value_[1]??null;
            $alg = $value_[2]??null;

            # true 存在参数
            if(!empty($arg)){
                if(strncmp($value,self::$input_domain,6)===0){             # 数据取值 值域在input
                    $value = $input[$value_[1]]??'';
                }elseif (strncmp($value,self::$config_domain,7)===0){      # 数据取值 值域在config
                    $value = config(config('hoo-io.GATE_MODE_DEFAULT_STRICT_CONFIG').'.'.$value_[1])??'';
                }
            }

            # true 存在算法 且 值不为空
            if(!empty($alg) and !empty($value)){
                if ($alg == 'md5') {
                    $value = md5($value);
                }
            }
        }
        return $value;
    }

    /**
     * 判断入参是否在作用域内取值
     * @param $value
     * @return bool true 在作用域内取值  false 不在作用域中取值
     */
    private function is_in_domain($value): bool
    {
        if (strpos($value, "::") !== false) {
            return true;
        } else {
            return false;
        }
    }
}
