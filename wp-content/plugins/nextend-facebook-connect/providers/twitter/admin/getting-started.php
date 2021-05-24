<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>

<div class="nsl-admin-sub-content">
    <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

    <p style="max-width:55em;"><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create a %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "Twitter", "Consumer Key", "Consumer Secret"); ?></p>

    <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'Twitter App'); ?></h2>

    <ol>
        <li><?php printf(__('Navigate to <b>%s</b>', 'nextend-facebook-connect'), '<a href="https://developer.twitter.com/en/portal/projects-and-apps" target="_blank">https://developer.twitter.com/en/portal/projects-and-apps</a>'); ?></li>
        <li><?php printf(__('Log in with your %s credentials if you are not logged in.', 'nextend-facebook-connect'), 'Twitter'); ?></li>
        <li><?php _e('If you don\'t have a developer account yet, please apply one by filling all the required details! This is required for the next steps!', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('Once your developer account is complete, navigate back to <b>%s</b> if you aren\'t already there!', 'nextend-facebook-connect'), '<a href="https://developer.twitter.com/en/portal/projects-and-apps" target="_blank">https://developer.twitter.com/en/portal/projects-and-apps</a>'); ?>
        <li><?php _e('Click on <b>+ New Project</b>!', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Name your project, and go through the basic setup. You’ll need to select your use case and give a description.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Create a new app, or select an existing one, then click <b>Complete</b>', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('You’ll find your API key and secret on this page. Copy and paste the <b>API key</b> and the <b>API secret key</b> to the corresponding fields at Nextend Social Login and press <b>Save Changes</b>.', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('Click on the <b>App Settings</b> button at %s.', 'nextend-facebook-connect'), 'Twitter'); ?></li>
        <li><?php _e('Find the Apps section, and the app you created a few steps ago.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Click on the App settings icon. (The one that looks like a gear.)', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Click on the <b>Edit</b> button at <b>Authentication settings</b>.', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Switch on the <b>Enable 3-legged OAuth</b> option.', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('Add the following URL to the <b>Callback URLs</b> field: <b>%s</b>', 'nextend-facebook-connect'), $provider->getRedirectUriForApp()); ?></li>
        <li><?php printf(__('Enter your site\'s URL to the <b>Website URL</b> field: <b>%s</b>', 'nextend-facebook-connect'), site_url()); ?></li>
        <li><?php _e('If you want to get the email address as well, then don’t forget to enable the <b>Request email address from users</b> option. In this case you also need to fill the <b>Terms of service</b> and the <b>Privacy policy</b> fields with the corresponding URLs!', 'nextend-facebook-connect'); ?></li>
        <li><?php _e('Click on <b>Save</b>.', 'nextend-facebook-connect'); ?></li>
        <li><?php printf(__('Go back to Nextend Social Login and <b>Verify</b> your %s provider.', 'nextend-facebook-connect'), 'Twitter'); ?></li>
    </ol>

    <a href="<?php echo $this->getUrl('settings'); ?>"
       class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'Twitter App'); ?></a>

    <br>
    <div class="nsl-admin-embed-youtube">
        <div></div>
        <iframe src="https://www.youtube.com/embed/5m4kD11Ai2w?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>