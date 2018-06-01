<h2>Markup Code</h2>
<p>
    Mostly used in documentation of shell command or SQL. 
    The file <span class="w3-codespan">public/assets/css/markup.css</span> defines some custom CSS-rules
    for markup. Use prefered method (span or custom tags):
</p>
<pre><code class="html"><?= htmlentities(file_get_contents("partials/markup1.inc")) ?></code></pre>
<p>
    The output from this markup will be:
</p>
<?= file_get_contents("partials/markup1.inc") ?>

<p>
    In addition to bash, custom CSS is defined for these common command line tools:
</p>
<pre><code class="html"><?= htmlentities(file_get_contents("partials/markup2.inc")) ?></code></pre>
<p>
    The output from this markup will be:
</p>
<?= file_get_contents("partials/markup2.inc") ?>

<p>
    A special sudo markup is also defined, currently these commands are displayed 
    using a bash shell prompt.
</p>
<pre><code class="html"><?= htmlentities(file_get_contents("partials/markup3.inc")) ?></code></pre>
<p>
    The output from this markup will be:
</p>
<?= file_get_contents("partials/markup3.inc") ?>

<h3>Benefit</h3>
<p>
    In addition to colorize the prompt, it also let users to select text and copy without
    getting the prompt name mixed in.
</p>
