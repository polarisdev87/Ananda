Agile Store Locator is a store finder Wordpress Plugin that renders stores list with google maps V3 API, the plugin has a complete backend management to manage Styles, Theme, Markers, Import/Export Stores and Manage Categories. The plugin has many features options such as:

- Mobile Friendly
- Multiple Theme
- Multiple Layouts
- Multiple map styles
- InfoWindow Styles
- Markers Clustering
- Category Marker
- Time Switch
- Distance Slider
- Category Selector
- Multiple Color Schemes
- Unit Selection (KM/Miles)
- Prompt GeoLocation

Please read the documentation with examples on  https://agilestorelocator.com/




Thank you for purchasing Agile Store Locator WordPress Plugin, please report if you found any problem that will be fixed immediately
support@agilelogix.com

FAQ:
When encounter these errors:
The uploaded file exceeds the upload_max_filesize directive in php.ini.

Update your PHP.ini
upload_max_filesize = 8M

------------------------------------------
The most basic error is because of multiple time inclusion of google maps, so if you facing such issue simply remove line number 102
from  wp-content\plugins\AgileStoreLocator\public\class-agile-store-locator-public.php

this line
wp_enqueue_script('google-map', 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');


How to verify it?
press "CTRL + U" from browser and search for the path below, if its added multiple times then you have multiple inclusion of map.

http://maps.googleapis.com/maps/api/js


------------------------------------------

HOW to use ASL Plugin?
Paste it in the Page or Post from admin Panel, make sure the width of the container is 1180px so it display correctly  (default container size).

------------------------------------------
For Chrome Prompt Location wouldn't work until HTTPS connection.
https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only
@Chrome requires HTTPS connection to prompt location.

------------------------------------------
Backup all files before installing the plugin, contact support@agilelogix.com for any help regarding the plugin.
