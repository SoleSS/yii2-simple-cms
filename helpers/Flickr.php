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
        $service_url = 'https://api.flickr.com/services/rest/';
        $curl = curl_init($service_url);
        $curl_post_data = [
            'method' => 'flickr.photos.getSizes',
            'photo_id' => $id,
            'format' => 'json',
            'nojsoncallback' => '?',
            'api_key' => '974939e14a63f018b06b446a3ffeb80e'
        ];

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            throw new \Exception(var_export($info));
        }
        curl_close($curl);

        $decoded = json_decode($curl_response);

        $i = 0;
        $result = [];
        foreach ($decoded->response['sizes']['size'] as $size) {
            $result[$size['label']] = $size;
            $result[$size['label']]['index'] = $i;
            $i++;
        }

        return $result;
    }

    /**
     * @param $id
     * @return array
     * @deprecated
     */
    public static function photoOld($id) {
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