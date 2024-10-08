<?php
$cdn = get_cdn().'/hm';

function getDir($path,$html='',$data=[])
{
    $logs = glob($path.'/*', GLOB_ONLYDIR);
    foreach ($logs as $log) {
        $html .= '<div style="margin-left: 20px">';
        $html .= '<button type="button" class="btn btn-link ky-req"
        data-type="post"
        data-href="'.jump_link('/hm/log-viewer/show-log').'"
        data-params=\'{"path":"'.urlencode($log).'"}\'
        data-title="log-viewer"
        data-width="1400px"
        data-height="800px"
        >
        <i class="bi bi-folder"></i> '.basename($log).'('.count(glob($log.'/*.log')).')
        </button>';

        list($children,$html) = getDir($log,$html);
        $data[] = [
            'name' => basename($log),
            'path' => $log,
            'children' => $children
        ];

        $html .= '</div>';

    }
    return [$data,$html];
}
$log = storage_path('logs');
$html = '<div style="margin-left: 20px">';
$html .= '<button type="button" class="btn btn-link ky-req"
        data-type="post"
        data-href="'.jump_link('/hm/log-viewer/show-log').'"
        data-params=\'{"path":"'.urlencode($log).'"}\'
        data-title="log-viewer"
        data-width="1400px"
        data-height="800px"
        >
        <i class="bi bi-folder"></i> '.basename($log).'('.count(glob($log.'/*.log')).')
        </button>';
list($data,$html) = getDir($log,$html);
$html .= '</div>';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php echo $html?>
            </div>
        </div>
    </div>
</div>
