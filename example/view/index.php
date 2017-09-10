<!--
        This is the test page for rendering simple views.
-->

<h1>Simple view rendering</h1>
<p>
    A simple view consist of a chunks of standard HTML 5, possibly mixed with 
    PHP code.
</p>
<p>
    The HTML code in views (e.g. the content of this file) should be rendered inside the 
    page body using the simple view class using direct rendering or an router (see dispatcher in
    <a href="../routing/">examples/routing</a>). Using direct rendering is kind of pointless 
    unless you are creating a page mixing multiple views together.
</p>
<hr>

<h4>Inline PHP test</h4>
<p>Q: Are you there?<br>A: <?php echo "Yes, I'm working!" ?></p>
<hr>

<h4>No page decorations?</h4>
<p>
    If you click on the <a href="render.php">render view link</a> you should see the
    content in this file rendered using the standard template. Put context files in
    current directory to show menus and publish info.
</p>

<h4>Scure views</h4>
<p>
    Enforce authetication in page header to create secure views. For an example on 
    conditional hiding content (HTML elements), see 
    <a href="../context/menus/conditional.php">conditional.php</a>
</p>
<pre><code>
if ($session->authenticated()) {
        throw new Exception("Authentication is required");
}
</code></pre>
