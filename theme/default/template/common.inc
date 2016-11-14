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
// Common HTML for navigation menus. 
// 
// Provides an navigation menu including standard and topbar menu, Google translate 
// and search. Tune to suite your needs ;-)
// 

?>

<style>
    body, h1, h2, h3, h4, h5, h6 {
        font-family: "Lato", sans-serif
    }
    .w3-navbar, h1, button {
        font-family: "Montserrat", sans-serif
    }
    .fa-anchor, .fa-coffee {
        font-size:200px
    }
    @media screen {
        .main-content {
            margin-left: 200px;
        }
    }
    @media print {
        .no-print {
            display: none;
            visibility: hidden;
        }     
        .main-content {
            margin-left: 10px;
        }
    }
    @media (max-width: 650px) {
        .panel {
            width: 100%;
        }
    }
    @media (min-width: 650px) {
        .panel {
            min-width: 550px;
        }
    }
    .goog-te-gadget-simple {
        border: none;
    }
    .goog-te-menu-value > span {
        color: grey;
        font-size: 14px;
    }
</style>

<!-- Navigation bar -->
<div id="topbar-menu" class="no-print w3-card-2 w3-slim w3-black">
    <div style="overflow:auto;">
        <ul class="w3-navbar w3-left" style="overflow: hidden;">
            <li class="w3-opennav w3-left">
                <a class="w3-hover-white w3-theme w3-padding-16 w3-large" onclick="toggle_sidenav_open()" href="javascript:void(0)">
                    <i class="fa fa-bars fa-lg"></i>
                </a>
            </li>
            <li>
                <a href="<?= $config->location ?>" class="w3-padding-16 w3-large w3-white w3-hide-small"><?= _('Home') ?></a>
            </li>
            <?php $output->topmenu(false) ?>
        </ul>
        <ul id="topbar-sitehome" class="w3-navbar w3-right" style="overflow: hidden; display: none">
            <li>
                <a href="<?= $config->location ?>" class="w3-padding-16 w3-large w3-hover-light-grey"><?= $config->name ?></a>
            </li>
        </ul>
        <ul id="topbar-tools" class="w3-navbar w3-right" style="overflow: hidden; min-width: 200px">
            <li class="w3-opennav w3-right w3-hide-large w3-hide-medium">
                <a class="w3-hover-white w3-padding-16 w3-large w3-right" onclick="toggle_navbar_open()" href="javascript:void(0)">
                    <i class="fa fa-ellipsis-v fa-lg"></i>
                </a>
            </li>
            <li class="w3-right" id="search-button">
                <a class="w3-opennav w3-right w3-hover-white w3-padding-16 w3-large" title="<?= _('Search in content') ?>" onclick="toggle_search_open(this)" href="javascript:void(0)">
                    <i class="fa fa-search fa-lg"></i>
                </a>
            </li>
            <li class="w3-right">
                <a class="w3-hover-white w3-right w3-padding-16 w3-large" title="<?= _('Translate this page') ?>" onclick="toggle_translate_open(this)" href="javascript:void(0)">
                    <i class="fa fa-globe fa-lg"></i>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Language selection and translation -->
<div id="language-box" class="panel w3-row-padding w3-padding-16 w3-white" style="display: none">
    <div class="w3-twothird w3-margin-bottom">
        <div class="w3-input w3-border" id="google_translate_element"></div>
    </div>
    <div class="w3-third">
        <input class="w3-btn w3-blue w3-large" type="button" onclick="#" value="<?= _('Google Translate') ?>">
    </div>
</div>

<!-- Content search box -->
<div id="search-box" class="panel w3-row-padding w3-padding-16 w3-white" style="display: none">
    <div class="w3-twothird w3-margin-bottom">
        <input class="w3-input w3-border" type="text" onchange="save_search(this)" placeholder="Type search text..">
    </div>
    <div class="w3-third">
        <input class="w3-btn w3-green w3-large search" type="button" onclick="send_search('<?= $config->site ?>')" value="<?= _('Search Site') ?>">
    </div>
</div>

<!-- Navigation menu (for small devices) -->
<div id="navbar-menu" class="panel no-print w3-container w3-center w3-white" style="display: none">
    <ul class="w3-navbar w3-large w3-hide-medium w3-hide-large w3-theme w3-wide">
        <?php $output->topmenu(true) ?>            
    </ul> 
</div>

<!-- Main content overlay with 50% opaqueness when sidebar menu is opened -->
<div class="w3-overlay w3-animate-opacity" onclick="close_overlay()" style="cursor:pointer" id="page-overlay"></div>

<script>

        function close_overlay() {
            var xm = document.getElementById("sidenav-menu");
            var xo = document.getElementById("page-overlay");

            xm.style.display = 'none';
            xo.style.display = 'none';
        }

        function close_panels() {
            var i, x = document.getElementsByClassName("panel");
            for (i = 0; i < x.length; i++) {
                x[i].className = x[i].className.replace(" w3-show", "");
                x[i].style.display = "none";
            }
        }

        function open_panel(x) {
            x.style.display = "inline-block";
            x.className += " w3-show";
        }

        function toggle_panel_display(x) {
            var open = (x.style.display === 'none');

            if (x.className.indexOf("w3-right")) {
                x.className = x.className.replace(" w3-right", "");
            }
            if (x.className.indexOf("w3-left")) {
                x.className = x.className.replace(" w3-left", "");
            }

            if (window.innerWidth < 650) {
                x.className += " w3-left";
            } else {
                x.className += " w3-right";
            }

            if (open) {
                close_panels();
                open_panel(x);
            } else {
                close_panels();
            }
        }

        // 
        // Navigation menus.
        // 
        function toggle_navbar_open() {
            var x = document.getElementById("navbar-menu");
            toggle_panel_display(x);
        }

        function toggle_sidenav_open() {
            var xm = document.getElementById("sidenav-menu");
            var xo = document.getElementById("page-overlay");

            if (xm.style.display === 'none') {
                xm.style.display = 'block';
                xo.style.display = 'block';
            } else {
                xm.style.display = 'none';
                xo.style.display = 'none';
            }
        }

        // 
        // Translation
        // 
        function toggle_translate_open(obj) {
            var x = document.getElementById("language-box");
            toggle_panel_display(x);
        }

        function google_translate_element_init() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                multilanguagePage: true
            }, 'google_translate_element');
        }

        // 
        // Simple search.
        // 
        var search_string;

        function toggle_search_open(obj) {
            var x = document.getElementById("search-box");
            toggle_panel_display(x);
        }

        function save_search(input) {
            search_string = input.value;
        }

        function send_search(site) {
            var query = "https://www.google.se/#q=" + search_string + "+site:" + site;
            document.location = query;
        }

        // 
        // Set navigation menu always on top when scrolling.
        // 
        var scrolling = false;
        window.addEventListener('scroll', function (e) {

            if (window.scrollY > 20) {
                if (scrolling) {
                    return;
                } else {
                    scrolling = true;
                }
            } else {
                if (!scrolling) {
                    return;
                } else {
                    scrolling = false;
                }
            }

            var tm = document.getElementById("topbar-menu");
            var tt = document.getElementById("topbar-tools");
            var ts = document.getElementById("topbar-sitehome");
            var sm = document.getElementById("sidenav-menu");
            
            if (scrolling) {
                tm.className += " w3-top";
                tt.style.display = "none";
                ts.style.display = "inline-block";
                sm.style.top = "59px";
            } else {
                tm.className = tm.className.replace(" w3-top", "");
                tt.style.display = "";
                ts.style.display = "none";
                sm.style.top = "auto";          // restore default pos
            }
        });

</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=google_translate_element_init"></script>
