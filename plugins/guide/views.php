<h2>Views</h2>
<p>
    The simplest case is to create an *.php file and add HTML inside it. The page title will 
    be set to script name converted to camel case. For output of dynamic content, use normal 
    PHP escape sequences:
</p>
<pre><code class="php"><?= htmlentities(file_get_contents("partials/view1.inc")) ?></code></pre>

<h3>Plain HTML</h3>
<p>
    If dynamic content is not used, then a simple view is essential the same as an
    ordinary HTML-page, except for using the *.php extension to be recognized as a
    target for the dispatcher.
</p>

<h3>Parent class inheritance</h3>
<p>
    Because the view is wrapper inside a view class by dispatcher.php, all methods and
    properties of the parent class are accessible from within the simple view page:
</p>
<pre><code class="php"><?= htmlentities(file_get_contents("partials/view2.inc")) ?></code></pre>    

<p>
    The framework (uup-site) injects the $config and $session objects into the view process 
    global scope so they can be readily used without using i.e. $this->session.
</p>
