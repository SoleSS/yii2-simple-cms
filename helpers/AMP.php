<?php
namespace soless\cms\helpers;

class AMP {
    public static function encode($content) {
        $return = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
        $return = preg_replace('/\<span\>(.*)\<\/span\>/i', '$1', $return);

        preg_match_all('/<img[^>]+>/i',$return, $raw_imgs);
        foreach( $raw_imgs[0] as $img_tag ) {
            $tmp = [];
            preg_match('/src=[\"\']+([^"]*)[\"\']+/i', $img_tag, $tmp);

            $imageSize = [];
            try {
                $imageSize = getimagesize(\Yii::getAlias('@app') .'/web'. $tmp[1]);
            } catch (\Exception $exception) {
                // TODO: \Yii::error() ?
            }

            $return = str_replace(
                $img_tag,
                '<amp-img src="'. $tmp[1] .'" height="'. (isset($imageSize[1]) ? $imageSize[1] : 0) .'" width="'. (isset($imageSize[0]) ? $imageSize[0] : 0) .'" layout="responsive"></amp-img>',
                $return
            );
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
                $iframe_data = ['tag' => $iframe_tag, 'url' => $tmp[1], 'width' => (!empty($width) ? $width[1] : 608), 'height' => (!empty($height) ? $height[1] : 360)];
                $iframes[] = $iframe_data;

                $videourl = [];
                preg_match('/.*you.*\/embed\/(.*)/i', $tmp[1], $videourl);
                $videoid = $videourl[1];

                $res_ytvideos[] = ['orig' => $iframe_data['tag'], 'videoid' => $videoid, 'height' => $iframe_data['height'], 'width' => $iframe_data['width'], ];
                $return = str_replace($iframe_data['tag'], '<amp-youtube data-videoid="'. $videoid .'" height="'. $iframe_data['height'] .'" width="'. $iframe_data['width'] .'" layout="responsive"></amp-youtube>', $return);
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
                $return = str_replace($iframe_data['tag'], '<amp-iframe src="'. $iframe_data['url'] .'" height="'. $iframe_data['height'] .'" width="'. $iframe_data['width'] .'" layout="responsive" sandbox="allow-scripts allow-same-origin" allowfullscreen><amp-img src="/files/global/iframe.png" width="400" height="273" layout="fill" placeholder></amp-img></amp-iframe>', $return);;
            }
        }

        return $return;
    }
}