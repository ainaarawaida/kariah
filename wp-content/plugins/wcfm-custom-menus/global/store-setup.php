<?php


    if(isset($_GET['store-setup'])){
       
        
        add_action( 'wp_footer', 'luq_stylestoresetup' );
      
        function luq_stylestoresetup(){
            ?>

        <script>
        jQuery( document ).ready( function( $ ) {


        setTimeout(function() {
            if($('h1#wc-logo a span:contains("Store")').html()){
                $('h1#wc-logo a span:contains("Store")').html($('h1#wc-logo a span:contains("Store")').html().replace('Store', 'Kariah'));
                $('ol.wc-setup-steps li:contains("Store")').html($('ol.wc-setup-steps li:contains("Store")').html().replace('Store', 'Kariah'));
                $('div.wc-setup-content p:contains("store")').html($('div.wc-setup-content p:contains("store")').html().replace('store', 'Kariah'));
            }
                    }, 100 );
        });

            </script>    
        <?php 
        }


        if(isset($_GET['step']) && $_GET['step'] == 'store'){

            add_action( 'wp_footer', 'luq_stylestoresetupstep' );
      
            function luq_stylestoresetupstep(){
                ?>
    
            <script>
            jQuery( document ).ready( function( $ ) {
    
    
                $(window).on('load', function() {
                        
                        $('div.wc-setup-content h1:contains("Store")').html($('div.wc-setup-content h1:contains("Store")').html().replace('Store', 'Kariah'));
                        $('p.gravatar.wcfm_title strong:contains("Store")').html($('p.gravatar.wcfm_title strong:contains("Store")').html().replace('Store', 'Kariah'));
                        $('p.store_name.wcfm_title.wcfm_ele strong:contains("Shop")').html($('p.store_name.wcfm_title.wcfm_ele strong:contains("Shop")').html().replace('Shop', 'Kariah'));
                        //$('p.store_email.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.store_name.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        $('p.store_email.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah Email");
                        $('p.phone.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah Phone");
                        $('p.street_1.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah Street 1");
                        $('p.street_2.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah Street 2");
                        $('p.city.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah City");
                        $('p.zip.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah Poscode ");
                        $('p.country.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah Country");
                        $('p.state.wcfm_title.wcfm_ele strong:contains("Store")').html("Kariah State");
                        $('p.withdrawal_setting_break_1 strong:contains("Store")').html("Kariah Location");
                        $('p.shop_description.wcfm_title strong:contains("Shop")').html("Kariah Description");
                        
                        //$('p.phone.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.phone.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        //$('p.street_1.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.street_1.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        //$('p.street_2.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.street_2.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        //$('p.city.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.city.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        //$('p.zip.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.zip.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                       // $('p.country.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.country.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        //$('p.state.wcfm_title.wcfm_ele strong:contains("Store")').html($('p.state.wcfm_title.wcfm_ele strong:contains("Store")').html().replace('Store', 'Kariah'));
                        
                       
                               
                               });
            });
    
                </script>    
            <?php 
            }


            
        }
        


        if(isset($_GET['step']) && $_GET['step'] == 'policy'){
            
            add_filter( 'wcfm_vendor_settings_fields_policies', 'wcfm_vendor_settings_fields_policiesluq',10,1) ;
            function wcfm_vendor_settings_fields_policiesluq($data){
              // deb($data);
               if(isset($data['wcfm_shipping_policy'])){
                   unset($data['wcfm_shipping_policy']);
               }
               if(isset($data['wcfm_refund_policy'])){
                unset($data['wcfm_refund_policy']);
            }
               
               
                return $data ; 
            }
        }


        if(isset($_GET['step']) && $_GET['step'] == 'seo'){

            add_action( 'wp_footer', 'luq_stylestoresetupseo' );
      
            function luq_stylestoresetupseo(){
                ?>
    
            <script>
            jQuery( document ).ready( function( $ ) {
    
    
                $(window).on('load', function() {
                        
                        $('div.wc-setup-content h1:contains("Store")').html("Kariah SEO setup");
                        
                       
                               
                               });
            });
    
                </script>    
            <?php 
            }


            
        }


        if(isset($_GET['step']) && $_GET['step'] == 'social'){

            add_action( 'wp_footer', 'luq_stylestoresetupsocial' );
      
            function luq_stylestoresetupsocial(){
                ?>
    
            <script>
            jQuery( document ).ready( function( $ ) {
    
    
                $(window).on('load', function() {
                        
                        $('div.wc-setup-content h1:contains("Store")').html("Kariah Social setup");
                        
                       
                               
                               });
            });
    
                </script>    
            <?php 
            }


            
        }





    }
            // your code goes here



