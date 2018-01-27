<?php
namespace DesInventar\Legacy;

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class QqUploadedFileForm
{
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path)
    {
        if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return false;
        }
        return true;
    }

    public function getName()
    {
        return $_FILES['qqfile']['name'];
    }

    public function getSize()
    {
        return $_FILES['qqfile']['size'];
    }
}
