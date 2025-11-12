<?php
namespace App\Helpers;
class MapEngine
{
    static protected $_init = false;
    public static function scripts()
    {
        if(static::$_init) return;

        $html = '';
        $apiKey = setting_item('map_gmap_key');
        if (empty($apiKey)) {
            $apiKey = config('services.google.maps_api_key');
        }

        switch (setting_item('map_provider')) {
            case "gmap":
                $params = ['libraries=places'];
                if (!empty($apiKey)) {
                    array_unshift($params, 'key='.$apiKey);
                }
                $html .= sprintf("<script src='https://maps.googleapis.com/maps/api/js?%s'></script>", implode('&', $params));
                $html .= sprintf("<script src='https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js'></script>");
                $html .= sprintf("<script src='%s'></script>", url('libs/infobox.js'));
                break;
            case "osm":
                $html .= sprintf("<script src='%s'></script>", url('libs/leaflet1.4.0/leaflet.js'));
                $html .= sprintf("<link rel='stylesheet' href='%s'>", url('libs/leaflet1.4.0/leaflet.css'));
                break;
        }
        $html .= sprintf("<script src='%s'></script>", url('module/core/js/map-engine.js?_ver='.config('app.version')));

        static::$_init = true;

        return $html;
    }
}
