<?php

/*
 * Copyright (C) 2016 Anders Lövgren (Computing Department at BMC, Uppsala University).
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// 
// Include required library functions:
// 
include "include/ui.inc";

// 
// Output extra HTML header, i.e. for search engines and page navigation.
// 
function print_headers()
{
        print "<meta name=\"description\" content=\"\">\n";
        print "<meta name=\"keywords\" content=\"\">\n";
}

// 
// Output content to the sidebar (on right hand side).
// 
function print_sidebar()
{
        print "<div class=\"boxhdr\">Sidebar Header</div>\n";
        print "<div class=\"box\">Sidebar Content</div>\n";
}

// 
// Output custom menu. You should only use this function for special
// purposes. In the normal case its better to create a standard.menu file
// in the same directory as the script.
// 
function print_menu()
{
        
}

// 
// This function returns info about publisher to display at bottom of the 
// page. You can either uncomment this function to define the info on
// per page basis, or copy template/publish.inc file in the same directory
// as this page.
// 
function get_publish_info()
{
        return array("mailto"    => array(
                        "href" => "yourname@domain.com",
                        "name" => "Your Name"
                ),
                "webmaster" => array(
                        "href" => "http://www.doamin.com/webmaster.php",
                        "name" => "Webmaster"
                ),
                "published" => 2008,
                "modified"  => getlastmod()
        );
}

// 
// Output page body. The following elements are styled by CSS: <h1><h3><h4><p><ul><li>
// 
function print_body()
{
        echo "<h1>Transitional page</h1>\n";
        echo "<p>This is the page body content of an page using the old (deprecated) temlate system based on e.g. print_body() and print_title() callback functions.</p>\n";
}

// 
// Output page title.
// 
function print_title()
{
        echo "Page Title";
}

// 
// Load the UI template and let it call the callback functions defined in this script or
// in the template support library. The argument is either standard or popup.
// 
load_ui_template("standard");
