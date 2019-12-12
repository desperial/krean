<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\base\NotSupportedException;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;
use Imagine\Image\Box;

/**
 *
 */
class ImageUpload extends Model
{

    public $image;

    /**
     * @param UploadedFile $file
     * @param $requiredName
     * @param bool $watermark
     * @return string
     */
    public function uploadFile($file, $requiredName, $watermark = true)
    {

        $file_ext = $file->extension;
        $filename = $requiredName . "." . $file_ext;
        $file_path = Yii::getAlias('@uploads') . "/";
        $file->saveAs($file_path . $filename);
        $file->saveAs($file_path . $requiredName . "_origin." . $file_ext);

        $this->generateThumbs($file_path, $requiredName, $file_ext,$watermark);
        if ($watermark)
            $this->setWatermark($file_path, $filename);
        return $filename;
    }

    /**
     * @param UploadedFile $file
     *
     */
    public static function uploadDocument($file, $charcode)
    {
        $file_ext = $file->extension;
        $file_name = explode(".", $file->name);
        unset ($file_name[count($file_name) - 1]);
        $file_name = implode(".", $file_name);
        $file_path = Yii::getAlias('@uploads') . "/documents/";
        $filename = $file_name . "_" . $charcode . "." . $file_ext;
        $file->saveAs($file_path . $filename);
        return $filename;
    }

    /**
     * @param UploadedFile $file
     * @param $charcode
     * @return string
     */
    public static function uploadVideo($file, $charcode)
    {
        $file_ext = $file->extension;
        $file_name = explode(".", $file->name);
        unset($file_name[count($file_name) - 1]);
        $file_name = implode(".", $file_name);
        $file_path = Yii::getAlias('@videos');
        $filename = $file_name . "_" . $charcode . "." . $file_ext;
        $file->saveAs($file_path . $filename);
        return $filename;
    }

    /**
     * @param UploadedFile $file
     * @param $charcode
     * @return string
     */
    public static function uploadRoom($file, $charcode)
    {
        $file_ext = $file->extension;
        $file_name = explode(".", $file->name);
        unset ($file_name[count($file_name) - 1]);
        $file_name = implode(".", $file_name);
        if (!is_dir(Yii::getAlias('@uploads') . "/malls"))
            mkdir(Yii::getAlias('@uploads') . "/malls", '755');
        $file_path = Yii::getAlias('@uploads') . "/malls/";
        $filename = $file_name . "_" . $charcode . "." . $file_ext;
        $file->saveAs($file_path . $filename);
        return $filename;
    }

    public function uploadFileBase64($file, $requiredName)
    {
        list($type, $file) = explode(';', $file);
        list(, $file) = explode(',', $file);
        $file = base64_decode($file);
        list(, $type) = explode('/', $type);


        $file_ext = $type;
        $filename = $requiredName . "." . $file_ext;
        $file_path = Yii::getAlias('@uploads') . "/";
        file_put_contents($file_path . $filename, $file);
        file_put_contents($file_path . $filename . "_origin." . $file_ext, $file);
        $this->setWatermark($file_path, $filename);
        $this->generateThumbs($file_path, $requiredName, $file_ext);
        return $filename;
    }

    public function unlinkUploaded($fileName)
    {
        $requiredFileInUploads = Yii::getAlias('@uploads') . '/' . $fileName;
        $file_name = explode(".", $fileName);
        $file_ext = $file_name[count($file_name) - 1];
        unset($file_name[count($file_name) - 1]);
        $file_name = implode(".", $file_name);
        if (file_exists($requiredFileInUploads)) {
            foreach (Yii::$app->params["thumbs"] as $name => $thumb) {
                if (file_exists(Yii::getAlias('@uploads') . '/' . $file_name . "_" . $name . "." . $file_ext))
                    unlink(Yii::getAlias('@uploads') . '/' . $file_name . "_" . $name . "." . $file_ext);
            }
            if (unlink($requiredFileInUploads)) {
                return true;
            }
            return true;
        } else
            return true;
        return false;
    }

    public function uploadRenterLogo($file, $requiredName)
    {

        $file_ext = $file->extension;
        $filename = $requiredName . "." . $file_ext;
        $file_path = Yii::getAlias('@uploads') . "/";
        $file->saveAs($file_path . $filename);

        return $filename;
    }

    public static function uploadRenterLogoStatic($file, $requiredName)
    {
        $translit = new Translit();
        $requiredName = $translit::translitText($requiredName);
        $file_ext = $file->extension;
        $filename = $requiredName . "." . $file_ext;
        $file_path = Yii::getAlias('@uploads') . "/";
        $file->saveAs($file_path . $filename);

        return $filename;
    }

    public function uploadMultiple()
    {
        if ($this->validate()) {
            foreach ($this->image as $file) {
                $filename = md5(uniqid($file->basename)) . '.' . $file->extension;

                $file->saveAs(Yii::getAlias('@frontend') . '/uploads/images/' . $filename);
            }
            return true;
        } else {
            return false;
        }
    }

    public static function checkRemoteFile($url)
    {
        $headers = array_change_key_case (get_headers ($url, 1) );
        if (substr ($headers ['content-type'], 0, 5) == 'image') {
            return true;
        }
        else {
            return false;
        }
//    return getimagesize($url);
    }

    public static function getThumb($name, $img)
    {
        if ($name) {
            $tmp_name = explode(".", $img);
            $ext = $tmp_name[count($tmp_name) - 1];
            unset($tmp_name[count($tmp_name) - 1]);
            $thumbName = implode(".", $tmp_name) . "_" . $name . "." . $ext;
        }
        else
            $thumbName = $img;
        if (file_exists(Yii::getAlias('@uploads') . '/' . $thumbName)) {
            return Yii::getAlias('@uploadsWeb') . '/' . $thumbName;
        } else {
            return Yii::getAlias('@uploadsWeb') . '/' . $img;
        }
    }

    public static function commitPhotoToAdvert($file,$advert,$counter)
    {
        $file_path = Yii::getAlias('@uploads') . "/".$file;
        $file = explode(".",$file);
        $new_file_name = Yii::getAlias('@uploads') . "/".$advert->charcode . '(' . $counter . ').'.end($file);
        if (rename($file_path,$new_file_name))
            return $advert->charcode . '(' . $counter . ').'.end($file);
        else
            return false;

    }
    private function setWatermark($file_path, $filename)
    {
        $thumbImagine = Image::getImagine()->open($file_path . $filename);
        $waterImagine = Image::getImagine()->open(Yii::getAlias('@frontend') . "/web/image/lori1.png");
        $thumbSize = $thumbImagine->getSize();
        $waterSize = $waterImagine->getSize();
        $t = 0.15;
        $width = round($t * $thumbSize->getWidth());
        $height = round(($width / $waterSize->getWidth()) * $waterSize->getHeight());
        $waterImagine->resize(new Box($width, $height));

        $waterImagine->save(Yii::getAlias('@frontend') . "/web/image/lori_tmp.png");

        $position = [round(($thumbImagine->getSize()->getWidth() - $waterImagine->getSize()->getWidth()) / 2), round(($thumbImagine->getSize()->getHeight() - $waterImagine->getSize()->getHeight()) / 2)];

        $img = Image::watermark($file_path . $filename, Yii::getAlias('@frontend') . "/web/image/lori_tmp.png", $position)->save($file_path . $filename);
        unlink(Yii::getAlias('@frontend') . "/web/image/lori_tmp.png");
    }

    private function generateThumbs($file_path, $file_name, $file_ext, $watermark = true)
    {
        foreach (Yii::$app->params["thumbs"] as $name => $thumb) {
            Image::thumbnail($file_path . $file_name . "." . $file_ext, intval($thumb["width"]), intval($thumb["height"]))->save($file_path . $file_name . "_" . $name . "." . $file_ext);
            if ($watermark)
                $this->setWatermark($file_path ,$file_name . "_" . $name . "." . $file_ext);
        }

    }

    public static function getImage($image)
    {
        return Yii::getAlias('@uploadsWeb') . '/' . $image;
    }

    public static function deleteFile($filename)
    {
        return unlink(Yii::getAlias('@frontend') . '/uploads/documents/' . $filename);
    }
}
