<h1>Guidelines for content publisher</h1>
<div class="w3-panel w3-blue">
    <p>Some introduction notices to read before publishing content on this site.</p>
</div>

<h3>Basic</h3>
<p>
    This site uses a template system (uup-site) for rendering content. A requested URL is
    mapped to a PHP file by the <span class="w3-codespan">dispatch.php</span> script found
    in the public directory root.
</p>

<h3>Editor</h3>
<p>
    It's rekommended to use the <a href="https://netbeans.org/">NetBeans IDE</a>. The benefit is 
    code completion, syntax highlight and error check. This site is under version control (SVN) 
    and includes a project for NetBeans.
</p>

<div class="w3-panel w3-green">
    <h3>Getting started</h3>
    <?php
    $menu = include('standard.menu');
    foreach ($menu['data'] as $name => $link) {
            printf("<li><a href=\"%s\">%s</a></li>\n", $link, $name);
    }

    ?>
    <p>These pages provides a short introduction. Read them before starting publish content.</p>
</div>
