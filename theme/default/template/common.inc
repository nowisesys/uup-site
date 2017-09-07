<?php
/*
 * Copyright (C) 2016-2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

use UUP\Site\Utility\Content\Runtime;

// 
// Get runtime options:
// 
$show = new Runtime($this);

?>

<style>
    body, h1, h2, h3, h4, h5, h6 {
        font-family: "Lato", sans-serif
    }
    .w3-bar, h1, button {
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
            min-width: 400px;
        }
    }    
    .goog-te-gadget-simple {
        border: none;
    }
    .goog-te-menu-value > span {
        color: grey;
        font-size: 14px;
    }
    goog-te-menu-frame {
        width: 200px;
        max-width: 200px;
    }

    #topbar-menu a,
    #navbar-menu a {
        text-decoration: none;
    }
</style>

<!-- Navigation bar -->
<div id="topbar-menu" class="no-print w3-card-2 w3-slim w3-black">
    <div class="w3-bar w3-large">
        <span class="w3-left">
            <a class="w3-bar-item w3-hover-white" onclick="toggle_sidenav_open()" href="javascript:void(0)">
                <i class="fa fa-bars fa-lg"></i>
            </a>
            <?php if ($show->home) : ?>
                    <a class="w3-bar-item w3-hover-white" title="<?= _('Home') ?>" href="<?= $config->location ?>">
                        <i class="fa fa-home fa-lg"></i>
                    </a>
            <?php endif; ?>
            <?php $output->topmenu(false) ?>
        </span>
        <span class="w3-right" id="topbar-sitehome" style="overflow: hidden; display: none">
            <a href="<?= $config->location ?>" class="w3-bar-item w3-hover-white"><?= $config->name ?></a>
        </span>
        <span class="w3-right" id="topbar-tools">
            <?php if ($show->edit) : ?>
                    <a class="w3-bar-item w3-hover-white" title="<?= _('Open page editor') ?>" onclick="toggle_edit_open('<?= $config->edit['host'] ?>')" href="javascript:void(0)">
                        <i class="fa fa-pencil-square-o fa-lg"></i>
                    </a>
            <?php endif; ?>
            <?php if ($show->translate) : ?>
                    <a class="w3-bar-item w3-hover-white" title="<?= _('Translate this page') ?>" onclick="toggle_translate_open(this)" href="javascript:void(0)">
                        <i class="fa fa-globe fa-lg"></i>
                    </a>
            <?php endif; ?>
            <?php if ($show->search) : ?>
                    <a class="w3-bar-item w3-hover-white" id="search-button" title="<?= _('Search in content') ?>" onclick="toggle_search_open(this)" href="javascript:void(0)">
                        <i class="fa fa-search fa-lg"></i>
                    </a>
            <?php endif; ?>
            <?php if ($show->auth) : ?>
                    <a class="w3-bar-item w3-hover-white" title="<?= _('Open authentication box') ?>" onclick="toggle_logon_open(this)" href="javascript:void(0)">
                        <?php if ($session->authenticated()) : ?>
                                <i class="fa fa-lock fa-lg" id="logon-status"></i>
                        <?php else: ?>
                                <i class="fa fa-unlock-alt fa-lg" id="logon-status"></i>
                        <?php endif; ?>
                    </a>
            <?php endif; ?>
            <?php if ($show->topmenu) : ?>
                    <a class="w3-bar-item w3-hover-white w3-hide-large w3-hide-medium" onclick="toggle_navbar_open()" href="javascript:void(0)">
                        <i class="fa fa-ellipsis-v fa-lg"></i>
                    </a>
            <?php endif; ?>
        </span>
    </div>
</div>

<?php if ($show->auth) : ?>
        <!-- Logon status box -->
        <div id="logon-box" class="panel w3-row-padding w3-padding-16" style="display: none;">
            <div class="w3-container w3-right-align">
                <div class="w3-input w3-border-0" id="logon_status_element"></div>
                <?php if ($session->authenticated()) : ?>
                        <input class="w3-btn w3-blue w3-large" type="button" onclick="window.location = '<?= $config->url($config->auth['logon']) ?>'" value="<?= _('Details') ?>">
                        <input class="w3-btn w3-red w3-large" type="button" onclick="window.location = '<?= $config->url($config->auth['logoff']) ?>'" value="<?= _('Logoff') ?>">
                <?php else: ?>
                        <input class="w3-btn w3-orange w3-large" type="button" onclick="window.location = '<?= $config->url($config->auth['logon']) . '?user=1' ?>'" value="<?= _('Logon') ?>">
                <?php endif; ?>
            </div>
        </div>
<?php endif; ?>

<?php if ($show->translate) : ?>
        <!-- Language selection and translation -->
        <div id="language-box" class="panel w3-row-padding w3-padding-16" style="display: none">
            <div class="w3-container w3-right-align">
                <div class="w3-input w3-border" id="google_translate_element"></div>
                <input class="w3-btn w3-blue w3-large" type="button" onclick="#" value="<?= _('Google Translate') ?>">
            </div>
        </div>
<?php endif; ?>

<?php if ($show->search) : ?>
        <!-- Content search box -->
        <div id="search-box" class="panel w3-row-padding w3-padding-16" style="display: none">
            <div class="w3-container w3-right-align">
                <input class="w3-input w3-border" type="text" onchange="save_search(this)" placeholder="Type search text..">
                <input class="w3-btn w3-green w3-large search" type="button" onclick="send_search('<?= $config->site ?>')" value="<?= _('Search Site') ?>">
            </div>
        </div>
<?php endif; ?>

<?php if ($show->edit) : ?>
        <!-- Page edit box -->
        <div id="edit-box" class="panel w3-row-padding w3-padding-16" style="display: none">
            <div class="w3-container w3-right-align">
                <div class="w3-input w3-border-0"><?= _("Edit page content in inline mode") ?></div> 
                <?php if (!$this->params->hasParam('path')) : ?>
                        <input id="input-edit-save" class="w3-btn w3-deep-orange w3-large" type="button" onclick="edit_save(event)" value="<?= _('Save') ?>" title="<?php _("Save changes made on this page") ?>">
                        <input id="input-edit-open" class="w3-btn w3-blue-grey w3-large" type="button" onclick="edit_open(event)" value="<?= _('Open') ?>" title="<?php _("Open this location in editor") ?>">
                <?php else : ?>
                        <input id="input-edit-save" class="w3-btn w3-deep-orange w3-large" type="button" onclick="edit_save(event)" value="<?= _('Save') ?>" title="<?php _("Save changes made on this page") ?>">
                        <input id="input-edit-open" class="w3-btn w3-blue-grey w3-large" type="button" onclick="edit_close(event)" value="<?= _('Close') ?>" title="<?php _("Close edit mode for this location") ?>">
                <?php endif; ?>
            </div>
        </div>
<?php endif; ?>

<!-- Navigation menu (for small devices) -->
<div id="navbar-menu" class="w3-bar w3-animate-top w3-card-2 w3-xlarge w3-hide-medium w3-hide-large panel no-print" style="display: none">
    <?php $output->topmenu(true) ?>            
</div>

<!-- Main content overlay with 50% opaqueness when sidebar menu is opened -->
<div class="w3-overlay w3-animate-opacity" onclick="close_overlay()" style="cursor:pointer" id="page-overlay"></div>

<script>

        function content_replace(event, id, source) {
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function (x) {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById(id).innerHTML = this.responseText;
                }
            }

            xhr.open("GET", source, true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.send();

            event.preventDefault();
            event.stopPropagation();

            close_overlay();
            return false;
        }

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

        function toggle_panel_display(x, func) {
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

            if (open && func !== undefined) {
                func(x);
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
        // Page and site editor.
        // 
        function toggle_edit_open(host) {
            var editable = <?= $this->isEditable() ? 1 : 0 ?>;

            // 
            // Redirect to editing host if required:
            // 
            if (window.location.host !== host) {
                window.location.host = host;
                return false;
            }

            var x = document.getElementById("edit-box");
            toggle_panel_display(x);

            var edit = (x.style.display !== 'none');
            var page = document.getElementById("page-content");

            if (editable) {
                page.setAttribute("contenteditable", edit);
                page.focus();
            }
        }

        function edit_save(event) {
            var editable = <?= $this->isEditable() ? 1 : 0 ?>;

            if (!editable) {
                alert("This page is not editable using inline method");
                return;
            }

            var html = document.getElementById("page-content");
            var form = new FormData();

            form.append("content", html.innerHTML);

            var target = "";
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function (x) {
                if (this.readyState === 4 && this.status === 200) {
                    var json = JSON.parse(this.responseText);
                    if (json.status === 'failure') {
                        alert(json.message);
                    }
                }
            }

            xhr.open("POST", target);
            xhr.send(form);
        }

        function edit_open(event) {
            window.location = "<?= $config->url($config->edit['view']) ?>" + "?path=" + "<?= $this->params->path ?>";
            event.preventDefault();
        }

        function edit_close(event) {
            window.location = "<?= $config->url('/' . $this->params->getParam('path')) ?>";
            event.preventDefault();
        }

        // 
        // Translation.
        // 
        function toggle_translate_open(obj) {
            var x = document.getElementById("language-box");
            toggle_panel_display(x);
        }

        function google_translate_element_init() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                autoDisplay: false,
                multilanguagePage: true,
                layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL
            }, 'google_translate_element');

            var select = document.getElementsByClassName('goog-te-combo')[0];
            select.className += " w3-input w3-white w3-medium w3-border-grey w3-text-black";
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
        // Authenticate and logon status.
        // 
        function toggle_logon_open(obj) {
            var x = document.getElementById("logon-box");
            // 
            // Only display panel on open:
            // 
            toggle_panel_display(x, function (elem) {

                var target = "<?= $config->url($config->auth['logon'] . '?json=1') ?>";
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function (x) {
                    if (this.readyState === 4 && this.status === 200) {
                        var json = JSON.parse(this.responseText);
                        var stat = document.getElementById('logon_status_element');
                        var text = "";

                        switch (json.step) {
                            case 1:
                                text = 'You are not logged on';
                                break;
                            case 3:
                                text = 'Logged on as <b>' + json.data.user + '</b> using ' + json.auth[json.data.auth].desc;
                                break;
                                if (json.step === 3) {
                                }
                        }
                        stat.innerHTML = text;
                    }
                };

                xhr.open("GET", target, true);
                xhr.send();
            });
        }

        // 
        // Set navigation menu always on top when scrolling.
        // 
        var scrolling = false;
        window.addEventListener('scroll', function (e) {

            if (window.scrollY > 86) {
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
                sm.style.top = "43px";
            } else {
                tm.className = tm.className.replace(" w3-top", "");
                tt.style.display = "";
                ts.style.display = "none";
                sm.style.top = "auto"; // restore default pos
            }
        });

</script>

<?php if ($show->translate) : ?>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=google_translate_element_init"></script>
<?php endif; ?>
