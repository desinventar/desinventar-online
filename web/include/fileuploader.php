<?php
/**
 * Handle file uploads via XMLHttpRequest
 */
namespace DesInventar\Legacy;

use QqUploadedFileXhr;
use QqUploadedFileForm;

class QqFileUploader
{
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    public function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new QqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new QqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
                break;
            case 'm':
                $val *= 1024;
                break;
            case 'k':
                $val *= 1024;
                break;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    public function handleUpload($uploadDirectory, $replaceOldFile = false)
    {
        $iReturn = 1;
        $answer = array();
        if (!is_writable($uploadDirectory)) {
            $answer['error'] = "Server error. Upload directory isn't writable.";
            $iReturn = -1;
        }

        if (!$this->file) {
            $answer['error'] = 'No files were uploaded.';
            $iReturn = -1;
        }

        $size = $this->file->getSize();
        if ($size == 0) {
            $answer['error'] = 'File is empty';
            $iReturn = -1;
        }

        if ($size > $this->sizeLimit) {
            $answer['error'] = 'File is too large';
            $iReturn = -1;
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        $ext = $pathinfo['extension'];

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            $answer['error'] = 'File has an invalid extension, it should be one of '. $these . '.';
            $iReturn = -1;
        }

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
            $answer['filename'] = $filename . '.' . $ext;
            $answer['success'] = true;
        } else {
            $answer['error'] = 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered';
        }
        return $answer;
    }
}
