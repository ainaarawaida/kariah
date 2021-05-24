<?php


    if(isset($_GET['store-setup'])){
       
        
        add_action( 'wp_footer', 'luq_stylestoresetup' );
      
        function luq_stylestoresetup(){
            ?>

        <script>
        jQuery( document ).ready( function( $ ) {


        setTimeout(function() {
            $('h1#wc-logo a span:contains("Store")').html($('h1#wc-logo a span:contains("Store")').html().replace('Store', 'Kariah'));
            $('ol.wc-setup-steps li:contains("Store")').html($('ol.wc-setup-steps li:contains("Store")').html().replace('Store', 'Kariah'));
            $('div.wc-setup-content p:contains("store")').html($('div.wc-setup-content p:contains("store")').html().replace('store', 'Kariah'));
            
                    }, 100 );

        
        });

            </script>    
        <?php 
        }



    }
            // your code goes here



