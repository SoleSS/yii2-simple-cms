<?php
namespace soless\cms\helpers;

use \yii\helpers\Url;

class AMP {
    public static function encode($content, $filesRoot = null) {
        $return = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
        $return = preg_replace('/\<span\>(.*)\<\/span\>/i', '$1', $return);

        $medias = [];

        preg_match_all('/<img[^>]+>/i',$return, $raw_imgs);
        foreach( $raw_imgs[0] as $img_tag ) {
            $tmp = [];
            preg_match('/src=[\"\']+([^"]*)[\"\']+/i', $img_tag, $tmp);
            preg_match('/class=[\"\']+([^"]*)[\"\']+/i', $img_tag, $classes);
            $class = !empty($classes) ? 'class="'. $classes[1] .'" ' : '';

            if (!isset($tmp[1])) continue;

            $imageSize = [];
            if (Url::isRelative($tmp[1])) {
                try {
                    $imageSize = getimagesize($filesRoot . $tmp[1]);
                } catch (\Exception $exception) {
                    \Yii::error($exception);
                }
            } else {
                try {
                    $imageSize = getimagesize($tmp[1]);
                } catch (\Exception $exception) {
                    \Yii::error($exception);
                }
            }

            $return = str_replace(
                $img_tag,
                '<amp-img '. $class .'src="'. $tmp[1] .'" height="'. (isset($imageSize[1]) ? $imageSize[1] : 0) .'" width="'. (isset($imageSize[0]) ? $imageSize[0] : 0) .'" layout="responsive"></amp-img>',
                $return
            );

            $medias[] = [
                'type' => 'image',
                'path' => $tmp[1],
                'width' => (isset($imageSize[0]) ? $imageSize[0] : null),
                'height' => (isset($imageSize[1]) ? $imageSize[1] : null)
            ];
        }

        preg_match_all('/<iframe .*src=".*youtube.*"[^>]+>.*<\/iframe>/i', $return, $raw_iframes);
        $iframes = [];
        $res_ytvideos = [];
        foreach( $raw_iframes[0] as $i => $iframe_tag ) {
            if (!empty($iframe_tag)) {
                $tmp = [];
                $width = [];
                $height = [];
                preg_match('/src=[\"\']+([^"]*)[\"\']+/i', $iframe_tag, $tmp);
                preg_match('/width=\"(\d+)\"/i', $iframe_tag, $width);
                preg_match('/height=\"(\d+)\"/i', $iframe_tag, $height);

                $autoplay = strpos($iframe_tag, 'autoplay') === false ? false : true;
                $loop = strpos($iframe_tag, 'loop') === false ? false : true;
                $controls = strpos($iframe_tag, 'controls') === false ? false : true;

                $iframe_data = ['tag' => $iframe_tag, 'url' => $tmp[1], 'width' => (!empty($width) ? $width[1] : 608), 'height' => (!empty($height) ? $height[1] : 360)];
                $iframes[] = $iframe_data;

                $videourl = [];
                preg_match('/.*you.*\/embed\/(.*)/i', $tmp[1], $videourl);
                $videoIdParts = explode('?', $videourl[1]);
                $videoid = $videoIdParts[0];

                $res_ytvideos[] = ['orig' => $iframe_data['tag'], 'videoid' => $videoid, 'height' => $iframe_data['height'], 'width' => $iframe_data['width'], ];
                $return = str_replace($iframe_data['tag'], '<amp-youtube '.
                    ($autoplay ? 'autoplay ' : '') .
                    ($loop ? 'data-param-loop=1 data-param-playlist='.$videoid.' ' : 'data-param-loop=0 ') .
                    ($controls ? 'data-param-controls=1 ' : 'data-param-controls=0 ') .
                    'data-param-rel=0 data-param-version=3 data-param-showinfo=0 '.
                    'data-videoid="'. $videoid .'" height="'. $iframe_data['height'] .'" width="'. $iframe_data['width'] .'" layout="responsive"></amp-youtube>', $return);

                $medias[] = [
                    'type' => 'youtube-video',
                    'id' => $videoid,
                    'width' => (!empty($width) ? $width[1] : 608),
                    'height' => (!empty($height) ? $height[1] : 360),
                    'params' => [
                        'autoplay' => $autoplay,
                        'loop' => $loop,
                        'controls' => $controls
                    ],
                ];
            }
        }

        preg_match_all('/<iframe[^>]+>.*<\/iframe>/i', $return, $raw_iframes);
        $iframes = [];
        $res_iframes = [];
        foreach( $raw_iframes[0] as $i => $iframe_tag ) {
            if (!empty($iframe_tag)) {
                $tmp = [];
                $width = [];
                $height = [];
                preg_match('/src=[\"\']+([^"]*)[\"\']+/i', $iframe_tag, $tmp);
                preg_match('/width=\"(\d+)\"/i', $iframe_tag, $width);
                preg_match('/height=\"(\d+)\"/i', $iframe_tag, $height);
                $iframe_data = ['tag' => $iframe_tag, 'url' => $tmp[1], 'width' => (!empty($width) ? $width[1] : 608), 'height' => (!empty($height) ? $height[1] : 360)];
                $iframes[] = $iframe_data;

                $res_iframes[] = ['orig' => $iframe_data['tag'], 'url' => $iframe_data['url'], 'height' => $iframe_data['height'], 'width' => $iframe_data['width'], ];
                $return = str_replace($iframe_data['tag'], '<amp-iframe src="'. $iframe_data['url'] .'" height="'. $iframe_data['height'] .'" width="'. $iframe_data['width'] .'" layout="responsive" sandbox="allow-scripts allow-same-origin" allowfullscreen><amp-img src="/files/global/iframe.png" width="400" height="273" layout="fill" placeholder></amp-img></amp-iframe>', $return);

                $medias[] = [
                    'type' => 'iframe',
                    'url' => $iframe_data['url'],
                    'width' => $iframe_data['width'],
                    'height' => $iframe_data['height']
                ];
            }
        }

        preg_match_all('/<a.+href="(([\w\/]+)\.(mp3|ogg|flac|aac))"(.+)?>.+<\/a>/im', $return, $raw_audios);
        foreach ( $raw_audios[0] as $i => $audio_tag) {
            $params = [
                'type' => null,
            ];
            try {
                $params['type'] = mime_content_type($filesRoot . $raw_audios[1][$i]);
            } catch (\Exception $exception) {
                \Yii::error($exception);
            }

            $return = str_replace($audio_tag, '<amp-audio src="'. $raw_audios[1][$i] .'"></amp-audio>', $return);

            $medias[] = [
                'type' => 'audio',
                'url' => $raw_audios[1][$i],
                'params' => $params,
            ];
        }

        return [
            'content' => $return,
            'medias' => $medias
        ];
    }
}