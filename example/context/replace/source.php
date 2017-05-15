<?php
/*
 * Copyright (C) Error: on line 5, column 33 in Templates/Licenses/license-apache20.txt
  The string doesn't match the expected date/time format. The string to parse was: "May 15, 2017". The expected format was: "yyyy-MMM-dd". Anders Lövgren (Computing Department at BMC, Uppsala University).
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

?>
<div style='background-color: blue'>
    <h3>This is content from source.php</h3>
    <b>Date: <?= strftime("%x %X") ?></b>
</div>
