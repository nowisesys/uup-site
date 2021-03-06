<?php
/*
 * Copyright (C) 2018 Anders Lövgren (Nowise Systems).
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
// File containing mixed HTML and Javascript.
// 

?>

<div style='background-color: blue'>
    <h3>This is content from script.php</h3>
    <b>Date: <?= strftime("%x %X") ?></b>
</div>

<script onajax="run">
        function func1() {
            alert("Script fragment run by AJAX");
        }

        func1();
</script>

<script onajax="add">
        function func2() {
            alert("Script fragment added by AJAX");
        }

        func2();
</script>

<script>
        function func3() {
            alert("Script fragment ignored by AJAX");
        }

        func3();
</script>
