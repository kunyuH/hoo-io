<?php

namespace hoo\io\monitor\LaravelLogView\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\View;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

class HooLogViewerController extends LogViewerController
{
    /**
     * @var LaravelLogViewer
     */
    private $log_viewer;

    public function __construct()
    {
        $this->log_viewer = new LaravelLogViewer();
        parent::__construct();
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function index()
    {
        $folderFiles = [];
        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
            $folderFiles = $this->log_viewer->getFolderFiles(true);
        }
        if ($this->request->input('l')) {
            $this->log_viewer->setFile(Crypt::decrypt($this->request->input('l')));
        }

        if ($early_return = $this->earlyReturn()) {
            return $early_return;
        }

        $data = [
            'logs' => $this->log_viewer->all(),
            'folders' => $this->log_viewer->getFolders(),
            'current_folder' => $this->log_viewer->getFolderName(),
            'folder_files' => $folderFiles,
            'files' => $this->log_viewer->getFiles(true),
            'current_file' => $this->log_viewer->getFileName(),
            'standardFormat' => true,
            'structure' => $this->log_viewer->foldersAndFiles(),
            'storage_path' => $this->log_viewer->getStoragePath(),
        ];

        if ($this->request->wantsJson()) {
            return $data;
        }

        if (is_array($data['logs']) && count($data['logs']) > 0) {
            $firstLog = reset($data['logs']);
            if ($firstLog) {
                if (!$firstLog['context'] && !$firstLog['level']) {
                    $data['standardFormat'] = false;
                }
            }
        }

        $hoo_data = [];
        foreach ($data['structure'] as $log) {
            # 字符串替换
            $log = str_replace('\\', '/', $log);

            $key = str_replace($data['storage_path'], '', $log);
            # 第一位是斜杠则去除
            if(substr($key, 0, 1) == '/'){$key = substr($key, 1);}

            if(is_dir($log)){
                foreach (get_files($log) as $file){
                    $k = str_replace("{$data['storage_path']}/{$key}/", '', $file);
//                    # 第一位是斜杠则去除
//                    if(substr($k, 0, 1) == '/'){$k = substr($k, 1);}
                    $hoo_data['dir'][$key][$k] = $file;
                }
            }else{
                $hoo_data['file'][$key] = $log;
            }
        }
        $data['hoo_data'] = $hoo_data;
        return View::file(__DIR__ . "/../views/log.blade.php",$data)->render();
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    private function earlyReturn()
    {
        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
        }

        if ($this->request->input('dl')) {
            return $this->download($this->pathFromInput('dl'));
        } elseif ($this->request->has('clean')) {
            app('files')->put($this->pathFromInput('clean'), '');
            return $this->redirect(url()->previous());
        } elseif ($this->request->has('del')) {
            app('files')->delete($this->pathFromInput('del'));
            return $this->redirect($this->request->url());
        } elseif ($this->request->has('delall')) {
            $files = ($this->log_viewer->getFolderName())
                ? $this->log_viewer->getFolderFiles(true)
                : $this->log_viewer->getFiles(true);
            foreach ($files as $file) {
                app('files')->delete($this->log_viewer->pathToLogFile($file));
            }
            return $this->redirect($this->request->url());
        }
        return false;
    }


}
