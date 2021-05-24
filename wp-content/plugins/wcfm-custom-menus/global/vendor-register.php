<?php

function my_functionluqvendorregister(){
    global $wp, $wpdb ;
    //print_r($wp->query_vars);
    if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'vendor-register'){
       
        
        add_action( 'wp_footer', 'luq_stylestoresetupmy_functionluqvendorregister' );
      
        function luq_stylestoresetupmy_functionluqvendorregister(){
            ?>

        <script>
     

        jQuery( document ).ready( function( $ ) {


        setTimeout(function() {
            //$('h1#wc-logo a span:contains("Store")').html($('h1#wc-logo a span:contains("Store")').html().replace('Store', 'Kariah'));
            //$('ol.wc-setup-steps li:contains("Store")').html($('ol.wc-setup-steps li:contains("Store")').html().replace('Store', 'Kariah'));
            //$('div.wc-setup-content p:contains("store")').html($('div.wc-setup-content p:contains("store")').html().replace('store', 'Kariah'));
            if($('strong:contains("Store")').html()){
                $('strong:contains("Store")').html($('strong:contains("Store")').html().replace('Store', 'Kariah'));
                $('span.wcfm_store_slug:contains("store")').html($('span.wcfm_store_slug:contains("store")').html().replace('store', 'Kariah'));
            }
           
                    }, 100 );

        
        });

            </script>    
        <?php 
        }



    }
            // your code goes here
}
add_action( "template_redirect", "my_functionluqvendorregister" );

