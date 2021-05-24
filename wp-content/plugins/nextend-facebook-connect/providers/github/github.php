<?php

class NextendSocialProviderGitHub extends NextendSocialProviderDummy {

    protected $color = '#45668e';

    public function __construct() {
        $this->id    = 'github';
        $this->label = 'GitHub';
        $this->path  = dirname(__FILE__);
    }
}

NextendSocialLogin::addProvider(new NextendSocialProviderGitHub());