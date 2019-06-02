<?php
namespace soless\cms\helpers;

use \yii\helpers\Url;
use \yii\httpclient\Client;

class Flickr {

    public static function albumPhotos($id, $apiKey, $endpoint = 'https://api.flickr.com/services/rest/') {
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
            ->setUrl($endpoint)
            ->setData([
                'method' => 'flickr.photosets.getPhotos',
                'api_key' => $apiKey,
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

    public static function curlPhotoRequest($id, $apiKey, $endpoint = 'https://api.flickr.com/services/rest/') {
        $curl = curl_init($endpoint);
        $curl_post_data = [
            'method' => 'flickr.photos.getSizes',
            'photo_id' => $id,
            'format' => 'json',
            'nojsoncallback' => '?',
            'api_key' => $apiKey
        ];

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);

        return $curl;
    }

    public static function photo($id, $apiKey, $service_url = 'https://api.flickr.com/services/rest/') {
        $curl = curl_init($service_url);
        $curl_post_data = [
            'method' => 'flickr.photos.getSizes',
            'photo_id' => $id,
            'format' => 'json',
            'nojsoncallback' => '?',
            'api_key' => $apiKey
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

        $decoded = json_decode($curl_response, true);

        $result = [];
        foreach ($decoded['sizes']['size'] as $size) {
            $result[$size['label']] = $size;
        }

        $result['id'] = $id;

        return $result;
    }

}