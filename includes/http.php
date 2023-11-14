<?php

$navex_order_url = get_post_meta( $post->ID, 'navex_order_export_url', true );

add_action( 'admin_enqueue_scripts', 'script_enqueuer' );

function script_enqueuer() {
   
   // Register the JS file with a unique handle, file location, and an array of dependencies
   wp_register_script( "navex_script", plugin_dir_url(__DIR__).'assets/js/navex.js', array('jquery') );
   
   // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
   wp_localize_script( 'navex_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   wp_enqueue_script( 'navex_script' );
}

function navex_ajax_functions() {
    add_action("wp_ajax_navex_post_order", "navex_post_order");
    add_action("wp_ajax_navex_delete_order", "navex_delete_order");
}
add_action( 'admin_init', 'navex_ajax_functions' );

function navex_post_order() {
   
   // nonce check for an extra layer of security, the function will exit if it fails
   if ( !wp_verify_nonce( $_REQUEST['nonce'], "navex_post_order_nonce")) {
      exit("Woof Woof Woof");
   }

   $orderId = intval($_REQUEST['orderId']);
   $formData = $_REQUEST['formData'];
   $orderCode = get_post_meta($orderId, 'navex_order_code');

   $baseUrl = get_option("navex_options")[
    "navex_base_url"
    ];

    if ($orderCode) {
        $baseUrl = get_option("navex_options")[
            "navex_update_base_url"
            ];
        $formData['code_barre'] = $orderCode;

    }

   $response = wp_remote_request( $baseUrl,
        array(
            'method' => 'POST',
            'body' => $_REQUEST['formData']
        )
    );

    $api_response = json_decode( wp_remote_retrieve_body( $response ), true );

    if ($api_response['lien']) {
        $url_components = parse_url($api_response['lien']);
        parse_str($url_components['code'], $params);
        if ( ! add_post_meta( $orderId, 'order_export_url', $api_response['lien'], true ) ) { 
            update_post_meta ( $orderId, 'order_export_url', $api_response['lien'] );
        }
        if ( ! add_post_meta( $orderId, 'navex_order_code', $params['code'], true ) ) { 
            update_post_meta ( $orderId, 'navex_order_code', $params['code'] );
        }
        echo $api_response['lien'];
    }

   die();
}

function navex_delete_order() {
   
   // nonce check for an extra layer of security, the function will exit if it fails
   if ( !wp_verify_nonce( $_REQUEST['nonce'], "navex_delete_order_nonce")) {
      exit("Woof Woof Woof");
   }
   $baseUrl = get_option("navex_options")[
        "navex_delete_base_url"
    ];

   $orderId = intval($_REQUEST['orderId']);
   $curl_post = 'code=' . $_REQUEST['code'];

   $response = wp_remote_request( $baseUrl,
        array(
            'method' => 'DELETE',
            'body' => $curl_post
        )
    );

    $body = wp_remote_retrieve_body($response);
    if ($body) {
        delete_post_meta( $orderId, 'order_export_url' );
        delete_post_meta( $orderId, 'navex_order_code' );
    }
    echo $body;

   die();
}