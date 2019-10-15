<h1>Hello world!</h1>
<div class="w3-container ">
    <p>
        If you see this page, then your uup-site is setup and working. You can now start publish
        content on your site or develop your web application. Please visit the project page for
        i.e. API documentation.
    </p>

    <?php if (file_exists("../guide")): ?>
            <a class="w3-btn w3-deep-orange" href="../guide">Guide</a>
    <?php endif; ?>
    <?php if (file_exists("../example")): ?>
            <a class="w3-btn w3-deep-orange" href="../example">Examples</a>
    <?php endif; ?>

    <a class="w3-btn w3-deep-orange" href="https://nowise.se/oss/uup/site/">Project</a>
</div>
