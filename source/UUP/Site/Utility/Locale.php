<?php

/*
 * Copyright (C) 2015-2016 Anders Lövgren (QNET/BMC CompDept).
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

namespace UUP\Site\Utility;

/**
 * GNU gettext and locale support. 
 * 
 * You need to create a POT file for each text domain. Use xgettext to initialize
 * a POT-file in the locale directory (requires at least one of the input file 
 * having strings tagged for translation):
 * 
 *   bash$> find public/proj -name *.php | xgettext -o locale/proj.pot -f-
 * 
 * Review the generated POT-file. Create new translation files (PO) using msginit:
 * 
 *   bash$> mkdir -p locale/sv_SE/LC_MESSAGES
 *   bash$> msginit -i locale/proj.pot -o sv_SE/LC_MESSAGES/proj.po -l sv_SE -v
 * 
 * Compile the translation files using msgfmt:
 * 
 *   bash$> msgfmt sv_SE/LC_MESSAGES/proj.po -o sv_SE/LC_MESSAGES/proj.mo
 * 
 * @property-read string $detected The detected locale entry.
 * 
 * @property-read string $locale The active locale.
 * @property-read string $language The active language (for display).
 * @property-read string $country The active country (for display).
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Locale
{

        /**
         * The system config.
         * @var Config 
         */
        private $_config;
        /**
         * The locale settings.
         * @var array 
         */
        private $_locale;
        /**
         * The locale code.
         * @var string 
         */
        private $_code;

        /**
         * Constructor
         * @param Config $config The system config.
         */
        public function __construct($config)
        {
                if (isset($config->locale)) {
                        $this->_config = $config;
                        $this->_locale = $config->locale;
                }
                if (isset($this->_locale)) {
                        $this->detect();
                }
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'detected':
                                return $this->_code;
                        case 'locale':
                                return $this->_locale['map'][$this->_code]['locale'];
                        case 'language':
                                return $this->_locale['map'][$this->_code]['lang'];
                        case 'country':
                                return $this->_locale['map'][$this->_code]['country'];
                }
        }

        /**
         * Detect and apply locale settings.
         * 
         * Only locales defined by the configuration (map or alias) are applied. The order of 
         * precedence for detection are:
         * <ol>
         * <li>Request parameter (lang).</li>
         * <li>Session cookie (lang).</li>
         * <li>Browser language preferences.</li>
         * <li>Config default locale (defaults.site).</li>
         * </ol>
         */
        public function detect()
        {
                if (($code = filter_input(INPUT_GET, 'lang'))) {
                        if ($this->setLanguage($code)) {
                                return true;
                        }
                } elseif (($code = filter_input(INPUT_COOKIE, 'lang'))) {
                        if ($this->setLanguage($code)) {
                                return true;
                        }
                } elseif (($code = $this->getPrefered())) {
                        if ($this->setLanguage($code)) {
                                return true;
                        }
                } else {
                        if ($this->setLanguage($this->_locale['default'])) {
                                return true;
                        }
                }

                return false;
        }

        /**
         * Select and apply locale.
         * @param string $code The locale code.
         * @return boolean
         */
        public function select($code)
        {
                return $this->setLanguage($code);
        }

        /**
         * Check if map or alias entry exist.
         * @param string $code The locale code.
         * @return boolean
         */
        private function hasLanguage($code)
        {
                if (isset($this->_locale['map'][$code])) {
                        return true;
                }
                if (isset($this->_locale['alias'][$code])) {
                        return true;
                }

                return false;
        }

        /**
         * Get locale code. 
         * @param string $code The locale code.
         * @return The entry key or false.
         */
        private function getLanguage($code)
        {
                if ($code == false) {
                        return $this->_locale['default'];
                } elseif (isset($this->_locale['map'][$code])) {
                        return $code;
                } elseif (isset($this->_locale['alias'][$code])) {
                        return $this->_locale['alias'][$code];
                } else {
                        return false;
                }
        }

        /**
         * Set locale, translation and cookie. 
         * 
         * @param string $code The locale code.
         * @return boolean
         */
        private function setLanguage($code)
        {
                if ($code == 'c') {
                        $code = $this->getPrefered();
                }

                if (!($code = $this->getLanguage($code))) {
                        return false;
                }
                if (!$this->setLocale($this->_locale['map'][$code]['locale'])) {
                        return false;
                }
                if (!$this->setTextDomain()) {
                        return false;
                }
                if (!setcookie("lang", $code, 0, $this->_config->location)) {
                        return false;
                }


                $this->_code = $code;
                return true;
        }

        /**
         * Check if locale is used.
         * @return boolean
         */
        public function useLocale()
        {
                return isset($this->_locale);
        }

        /**
         * Set locale.
         * @param string $locale The locale string (e.g. sv_SE).
         * @return boolean
         */
        private function setLocale($locale)
        {
                foreach ($this->_locale['categories'] as $category) {
                        if (!setlocale($category, $locale)) {
                                error_log("Failed set locale $locale (needs to be generated?)");
                                return false;
                        }
                }

                return true;
        }

        /**
         * Set translation text domain.
         * 
         * Call this function to use a custom text domain for page translation. Useful for a
         * big site where a single PO-file may becode really large. This can also be useful
         * for partitioning different locations in their own text domain.
         * 
         * Usually, all translation files are keept in the same directory so using the path
         * argument is never needed. One use could be when sharing home directories.
         * 
         * @param string $name The text domain name.
         * @param string $path The path of translation files.
         * @return boolean
         */
        public function setTextDomain($name = null, $path = null)
        {
                if (!isset($name)) {
                        $name = $this->_locale['textdomain'];
                }
                if (!isset($path)) {
                        $path = $this->_locale['directory'];
                }
                if ($path[0] != '/') {
                        $path = sprintf("%s/%s", $this->_config->proj, $path);
                }

                if (!file_exists($path)) {
                        error_log("The text domain $path is missing");
                        return false;
                }
                if (!bindtextdomain($name, $path)) {
                        error_log("Failed bind text domain $name in $path");
                        return false;
                }
                if (!textdomain($name)) {
                        error_log("Failed set default text domain $name");
                        return false;
                }

                return true;
        }

        /**
         * Get prefered language from browser.
         */
        private function getPrefered()
        {
                if (($prefered = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'))) {
                        $codes = explode(',', $prefered);
                        foreach ($codes as $code) {
                                $code = explode(";", str_replace("_", "-", strtolower($code)))[0];
                                if (($code = $this->getLanguage($code))) {
                                        return $code;
                                }
                        }
                }

                return false;
        }

}

// 
// Add workaround for missing gettext functions:
// 
if (!extension_loaded('gettext')) {

        function _($text)
        {
                return $text;
        }

        function gettext($text)
        {
                return $text;
        }

        function ngettext($text1, $text2, $n)
        {
                return $n == 1 ? $text1 : $text2;
        }

        function bindtextdomain($domain, $directory)
        {
                return true;
        }

        function textdomain($domain)
        {
                return true;
        }

}

?>
