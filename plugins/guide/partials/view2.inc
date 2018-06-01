<?php

if ($this->params->hasParam('year')) {
        printf("Happy new year %d, %s", 
                $this->params->getParam('year'), 
                $this->session->user
        );
}
