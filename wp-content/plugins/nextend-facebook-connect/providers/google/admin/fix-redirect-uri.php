<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>
<ol>
    <li><?php printf(__('Navigate to <b>%s</b>', 'nextend-facebook-connect'), '<a href="https://console.developers.google.com/apis/" target="_blank">https://console.developers.google.com/apis/</a>'); ?></li>
    <li><?php printf(__('Log in with your %s credentials if you are not logged in', 'nextend-facebook-connect'), 'Google'); ?></li>
    <li><?php _e('Click on the "<b>Credentials</b>" in the left hand menu', 'nextend-facebook-connect'); ?></li>
    <li><?php printf(__('Under the "<b>OAuth 2.0 Client IDs</b>" section find your Client ID: <b>%s</b>', 'nextend-facebook-connect'), $provider->settings->get('client_id')); ?></li>
    <li><?php printf(__('Add the following URL to the "<b>Authorised redirect URIs</b>" field: <b>%s</b>', 'nextend-facebook-connect'), $provider->getLoginUrl()); ?></li>
    <li><?php _e('Click on "<b>Save</b>"', 'nextend-facebook-connect'); ?></li>
</ol>