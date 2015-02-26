<?php

namespace php\http;

/**
 * Class FileBean is a container for uploaded files.
 * @package php\http
 */
class FileBean extends ParameterBean{

    private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function replace(array $files) {
        $this->parameters = array();
        $this->addParameter($files);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function setParameter($key, $value) {
        if (!is_array($value) && !$value instanceof UploadedFile) {
            throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
        }

        parent::setParameter($key, self::converFileInformation($value));
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function addParameter(array $files) {
        foreach ($files as $key => $value) {
            $this->setParameter($key, $value);
        }
    }

    /**
     * Converts uploaded files to UploadedFile instances.
     *
     * @param array|UploadedFile $file A (multi-dimensional) array of uploaded file information
     *
     * @return array A (multi-dimensional) array of UploadedFile instances
     */
    private static function convertFileInformation($file) {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        $file = self::fixFilesArray($file);
        if (is_array($file)) {
            $key = array_keys($file);
            sort($key);

            if ($key == self::$fileKeys) {
                if (UPLOAD_ERR_NO_FILE == $file['error']) {
                    $file = null;
                } else {
                    $file = new UploadedFile($file['tmp_name'], $file['name'],  $file['type'], $file['size'], $file['error']);
                }
            } else {
                $file = array_map(function ($x) {
                    return self::convertFileInformation($x);
                }, $file);
            }
        }

        return $file;
    }

    /**
     * Fixes a malformed PHP $_FILES array.
     *
     * PHP has a bug that the format of the $_FILES array differs, depending on
     * whether the uploaded file fields had normal field names or array-like
     * field names ("normal" vs. "parent[child]").
     *
     * This method fixes the array to look like the "normal" $_FILES array.
     *
     * It's safe to pass an already converted array, in which case this method
     * just returns the original array unmodified.
     *
     * @param array $data
     *
     * @return array
     */
    private static function fixFilesArray($data) {
        if (!is_array($data)) {
            return $data;
        }

        $keys = array_keys($data);
        sort($keys);

        if (self::$fileKeys != $keys || !array_key_exists('name', $data) || !is_array($data['name'])) {
            return $data;
        }

        $files = $data;
        foreach (self::$fileKeys as $key) {
            unset($files[$key]);
        }

        foreach (array_keys($data['name']) as $key) {
            $files[$key] = self::fixFilesArray(array(
               'error' => $data['error'][$key],
                'name' => $data['name'][$key],
                'type' => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size' => $data['size'][$key]
            ));
        }

        return $files;

    }
}