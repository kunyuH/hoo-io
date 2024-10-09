<?php

namespace hoo\io\monitor\ArcanedevLogViewer;

use Illuminate\Http\Request;

class ArcanedevLogViewerService
{
    /**
     * 日志view页面 静态资源链接替换，防止无法访问
     * 以及跳转链接修正
     */
    public function replaceStaticResourceLink(Request $request,$response,$reqPath)
    {
        # log-viewer
        if(ho_fnmatchs(config('log-viewer.route.attributes.prefix','log-viewer').'/*',$reqPath)) {
            $html = $response->getContent();

            $html = str_replace('/'.config('log-viewer.route.attributes.prefix','log-viewer').'/',
                jump_link('/'.config('log-viewer.route.attributes.prefix','log-viewer').'/'), $html);

            # 字符串替换
            $html = str_replace('https://maxcdn.bootstrapcdn.com', get_cdn() . '/log-viewer', $html);


            $response->setContent($html);
            return $response;
        }
        return $response;
    }
}
