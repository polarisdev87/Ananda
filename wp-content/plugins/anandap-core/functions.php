<?php

add_action( 'woocommerce_shipstation_shipnotify', 'anandap_shipstation_shipnotify', 10, 2);
function anandap_shipstation_shipnotify($order, $data) {
    $salesforce = new SalesforceSDK();
    $salesforce->update_tracking_number($order->get_id(), $data['tracking_number']);
}
