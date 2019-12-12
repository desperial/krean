<?php
/**
 * Created by PhpStorm.
 * User: chiff
 * Date: 18.11.2019
 * Time: 13:51
 */

namespace console\models;

use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\imagine\Image;

class Parser
{
    public static function getPage($url,$cookies_file = "")
    {
        $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        $timeout = 120;
        $cookies = Yii::getAlias("@console") . "/" . $cookies_file;
        $ch = \curl_init($url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
        $content = \curl_exec($ch);
        if (\curl_errno($ch)) {
            echo 'error:' . \curl_error($ch);
        } else {
            \curl_close($ch);
//            print_r($content);
            return $content;
        }
        return false;
    }

    public static function Login($url,$cookies_file,$post)
    {
        $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        $timeout = 120;
        $cookies = Yii::getAlias("@console") . "/cookies/" . $cookies_file;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
        } else {
            curl_close($ch);
//            print_r($content);
            return $content;
        }
        return false;
    }

    public static function getImage($link,$name,$dir = "",$ext = null)
    {
        if (!$ext) {
            $ext = explode(".",$link);
            $ext = $ext[count($ext)-1];
        }
        $filename = $name.".".$ext;
        $folder = Yii::getAlias("@uploads")."/".$dir;
        if (!is_dir($folder)) {
            try {
                FileHelper::createDirectory($folder, 0755, true);
            } catch (Exception $e) {
                die("something went terribly wrong! Try again later");
            }
        }
        if ($ext != "ru") {
            $img = Image::getImagine()->open($link);
            if ($img && $img->save($folder."/".$filename)) {
                return $dir."/".$filename;
            }
        }
        return false;
    }
}