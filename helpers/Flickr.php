<?php
namespace soless\cms\helpers;

use \yii\helpers\Url;
use \yii\httpclient\Client;

class Flickr {

    public static function albumPhotos($id) {
        $result = [];

        $client = new Client([
            'requestConfig' => [
                'format' => Client::FORMAT_URLENCODED
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(\Yii::$app->params['flickr']['endpoint'] . '/rest')
            ->setData([
                'method' => 'flickr.photosets.getPhotos',
                'api_key' => \Yii::$app->params['flickr']['apiKey'],
                'photoset_id' => $id,
                'media' => 'photos',
                'format' => 'json',
                'nojsoncallback' => '?',
            ])
            ->send();

        if ($response->isOk) {
            $result = $response->data['photoset']['photo'];
        }

        return $result;
    }

    public static function photo($id) {
        $result = [];

        $client = new Client([
            'requestConfig' => [
                'format' => Client::FORMAT_URLENCODED
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(\Yii::$app->params['flickr']['endpoint'] . '/rest')
            ->setData([
                'method' => 'flickr.photos.getSizes',
                'api_key' => \Yii::$app->params['flickr']['apiKey'],
                'photo_id' => $id,
                'format' => 'json',
                'nojsoncallback' => '?',
            ])
            ->send();

        if ($response->isOk) {
            $i = 0;
            foreach ($response->data['sizes']['size'] as $size) {
                $result[$size['label']] = $size;
                $result[$size['label']]['index'] = $i;
                $i++;
            }
        }

        return $result;
    }
}