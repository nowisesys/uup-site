<?php

// 
// The publisher contains page responsible information. Publisher information is typical
// provided site wide from the template directory. The publisher information are available
// for an page using the $this->publisher member variable.
// 
// Author: @author@
// Date:   @datetime@
// 

return array(
        "contact" => array(
                "href" => "http://directory.example.com/info/?id=4775",
                "name" => _("Page Responsible")
        ),
        "editor"  => array(
                "href" => "http://www.example.com/webmaster",
                "name" => _("Webmaster")
        ),
        "copying" => sprintf("2017-%s", date('Y')),
        "updated" => getlastmod()
);
