<h2>Web Components</h2>
<p>
    These are PHP classes that provides both presentation and behavior. Examples are the
    sitemap or download classes that let us define properties before scanning and once
    rendered, they provides user interaction.

<h3>Site local</h3>
<p>
    Two site local components exist under the source directory. The first is the download 
    class that simplifies generating a download page. The second is the content specification
    scanner that creates a table of content gallery.
</p>

<h4>The download component</h4>
<p>
    Use the download class to render a download page. Use add() method to define sub directories
    containing download files:
</p>
<pre><code class="php"><?= htmlentities(file_get_contents('partials/component1.inc')) ?></code></pre>
<p>
    Additional arguments can be passed if required (i.e. description or filename extension).
</p>
<pre><code class="php"><?= htmlentities(file_get_contents('partials/download.inc')) ?></code></pre>

<h3>Composer package</h3>
<p>
    The package bmc/uup-web-components defines additional web components, for example cards:
</p>
<pre><code class="php"><?= htmlentities(file_get_contents('partials/component2.inc')) ?></code></pre>
<p>
    Rendering this code fragment will create a responsive, three column grid layout:
</p>
<?php include('partials/component2.inc') ?>

<h3>Try out</h3>
<div class="w3-row">
<div class="w3-panel w3-indigo w3-padding w3-half">
    <h4>Try out</h4>
    <p>For more examples, visit the online demo setup of uup-web-component</p>
    <button class="w3-btn" onclick="location.href = 'https://it.bmc.uu.se/andlov/php/proj/bmc/uup-web-component/example/'">Open Examples</button>
</div>
<div class="w3-panel w3-deep-purple w3-padding w3-half">
    <h4>Try out</h4>
    <p>Click on the run code button to test the download code fragment from above live.</p>
    <button class="w3-btn" onclick="location.href = 'download'">Open Download</button>
</div>
</div>
