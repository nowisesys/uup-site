<?php
if (!function_exists('format_code')) {

        function format_code($code)
        {
                return "<div class=\"w3-code\">$code</div>\n";
        }

}

?>

<h2>Syntax Highlight</h2>
<p>
    Source code can have syntax highlight using the highlight.js library by putting it
    within pre and code tags:
</p>
<pre><code class="c"><?= htmlentities(file_get_contents("partials/syntax1.inc")) ?></code></pre>
<p>
    The source code language is autodetected unless explicit defined by a class attribute 
    value in the code tag. Color scheme (theme) is selected by the render template by including 
    the prefered CSS-file.
</p>

<h3>Code fragments</h3>
<p>
    To highlight short code fragments, simply place the code within:
</p>
<?= format_code(htmlentities("<pre><code class=\"c\">printf(\"hello world!\")</code></pre>")) ?>
<p>
    Include this HTML markup on the page yields:
</p>
<pre><code class="c">printf("hello world!")</code></pre>

<h3>Source code files</h3>
<p>
    For larger chunks of code its better to read the file content inside the tags:
</p>
<?= format_code(htmlentities("<pre><code class=\"java\"><?php readfile(\"file.java\") ?></code></pre>")) ?>

<h3>Mixup with HTML</h3>
<p>
    Source code that contains HTML special characters needs to be converted or they will disappear 
    from the output. Remember to change the PHP escape sequence to output a string instead of 
    executing a code block:
</p>
<?= format_code(htmlentities("<pre><code class=\"c\"><?= htmlentities(file_get_contents((\"file.c\") ?></code></pre>")) ?>

<h3>CodeBox</h3>
<p>
    The CodeBox component provides metods for displaying files or code fragments. For simple
    cases, its easiest to use the convenience functions. The syntax highlight area containing
    the code will be wrapped inside a code box:
</p>
<?= format_code(htmlentities("<?php UUP\Web\Component\Script\CodeBox::outputFile('file.c', true) ?>")) ?>
<?= format_code(htmlentities("<?php UUP\Web\Component\Script\CodeBox::outputText('c', 'printf(\"hello world!\")') ?>")) ?>
<a class="w3-btn w3-deep-orange" href="http://it.bmc.uu.se/andlov/php/uup-web-component/example/?page=container/codebox">Examples</a>

<h3>Missing languages?</h3>
<p>
    Please goto <a href="https://highlightjs.org/download/">Getting highlight.js</a> to select
    additional languages. Download new bundle and replace the highlight.js library in the assets 
    directory.
</p>
