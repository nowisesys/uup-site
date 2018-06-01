<h2>Controllers</h2>
<p>
    Create a class that inherits StandardPage to create a controller. Inside the controller,
    you have access to request paramaters, menus and context. Using a controller is a good
    options when selecting the render view (i.e. *.inc) dynamic at request time.
</p>
<pre><code class="php"><?= htmlentities(file_get_contents("partials/page1.inc")) ?></code></pre>
<h3>Mapping target script</h3>
<p>
    When looking for a matching controller, the character immediate following a '-' in the
    request URI gets converted to upper case. Finally, the Page suffix is appended. Examples 
    of request URI to class names:
</p>
<table class="w3-table-all">
    <tr><th>URI</th><th>Class</th><th>File</th></tr>
    <tr>
        <td>happy-new-year</td>
        <td>HappyNewYearPage</td>
        <td>happy-new-year.php</td>
    </tr>
    <tr>
        <td>index</td>
        <td>IndexPage</td>
        <td>index.php</td>
    </tr>
    <tr>
        <td>about</td>
        <td>AboutPage</td>
        <td>about.php</td>
    </tr>
</table>
<div class="w3-panel w3-red">
    <h4>Name conflicts:</h4>
    <p>
        Using the same name for directory and script in the same location will cause a conflict
        when dispatching a route. Just avoid it is the simple solution.
    </p>
</div>

<h3>Secure content</h3>
<p>
    It's possible to protect content by creating a secure page. The same can be done by
    calling <span class="w3-codespan">$this->authorize()</span> in a standard page controller.
</p>
<p>
    The markup can be hidden automatic from unauthenticated users (guest) by adding the
    custom attribute <span class="w3-codespan">auth="true"</span> on any element. Hiding 
    content won't protect anyone from viewing it and need to be accompanied by validation
    in request handler (called from this page).
</p>
