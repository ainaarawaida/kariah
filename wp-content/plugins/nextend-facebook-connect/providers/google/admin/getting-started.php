<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>

<div class="nsl-admin-sub-content">
    <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

    <p style="max-width:55em;"><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create a %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "Google", "Client ID", "Client secret"); ?></p>

    <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'Google App'); ?></h2>

    <ol>
        <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://console.developers.google.com/apis/" target="_blank">https://console.developers.google.com/apis/</a>'); ?></li>
        <li><?php printf(__('Log in with your %s credentials if you are not logged in', 'nextend-facebook-connect'), 'Google'); ?></li>
        <li><?php _e('If you don\'t have a project yet, you\'ll need to create one. You can do this by clicking on the blue "<b>Create</b>" button on the right side!  ( If you already have a project, click on the name of your project in the dashboard instead, which will bring up a modal and click <b>"New Project"</b>. )', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Name your project and then click on the "<b>Create</b>" button again', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Once you have a project, you\'ll end up in the dashboard.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Click the “<b>OAuth consent screen</b>” button on the left hand side.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Choose a <b>User Type</b> according to your needs. If you want to enable the social login with Google for any users with a Google account, then pick the External option!', 'nextend-facebook-connect'); ?>
            <ul>
                <li><?php printf(__('<b>Note:</b> We don\'t use sensitive or restricted scopes either. But if you will use this App for other purposes too, then you may need to go through an %1$s!', 'nextend-facebook-connect'), '<a href="https://support.google.com/cloud/answer/9110914" target="_blank">independent security review</a>'); ?></li>
            </ul>
        </li>
        <li><?php _e('Enter a name for your App to the "<b>Application name</b>" field, which will appear as the name of the app asking for consent.', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('Fill the "<b>Authorized domains</b>" field with your domain name probably: <b>%s</b> without subdomains!', 'nextend-facebook-connect'), str_replace('www.', '', $_SERVER['HTTP_HOST'])); ?></li>
        <li><?php _e('Save your settings!', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('On the left side, click on the "<b>%1$s</b>" menu point, then click the "<b>%2$s</b>" button in the top bar.', 'nextend-facebook-connect'), 'Credentials', '+ Create Credentials') ?></li>
        <li><?php _e('Choose the "<b>OAuth client ID</b>" option.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Select the "<b>Web application</b>" under Application type.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Enter a "<b>Name</b>" for your OAuth client ID.', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('Add the following URL to the "<b>Authorised redirect URIs</b>" field: <b>%s</b>', 'nextend-facebook-connect'), $provider->getLoginUrl()); ?></li>
        <li><?php _e('Click on the "<b>Create</b>" button', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('A modal should pop up with your credentials. If that doesn\'t happen, go to the Credentials in the left hand menu and select your app by clicking on its name and you\'ll be able to copy-paste the "<b>Client ID</b>" and "<b>Client Secret</b>" from there.', 'nextend-facebook-connect'); ?></li>
    </ol>

    <a href="<?php echo $this->getUrl('settings'); ?>"
       class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'Google App'); ?></a>

    <br>
    <div class="nsl-admin-embed-youtube">
        <div></div>
        <iframe src="https://www.youtube.com/embed/i01nbsbNMmw?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>