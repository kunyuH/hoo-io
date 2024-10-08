<?php

namespace hoo\io\monitor\ArcanedevLogViewer;

use Illuminate\Http\Request;

class ArcanedevLogViewerService
{
    /**
     * 日志view页面 静态资源链接替换，防止无法访问
     */
    public function replaceStaticResourceLink(Request $request,$response,$reqPath)
    {
        # log-viewer
        if(ho_fnmatchs(config('log-viewer.route.attributes.prefix').'/*',$reqPath)) {
            $html = $response->getContent();
            # 字符串替换
            $html = str_replace('https://maxcdn.bootstrapcdn.com', get_cdn() . '/log-viewer', $html);
            $response->setContent($html);
            return $response;
        }
        return $response;
    }
}
