<h2>Version Control</h2>
<p>
    The site is commited to SVN version control. You need an account for checking out 
    and commiting updates.
</p>
<div class="w3-code bash">
    <span>svn co svn://svn.bmc.uu.se/compdept/sites/it.bmc.uu.se/trunk it.bmc.uu.se</span><br>
    <span>cd it.bmc.uu.se</span><br>
</div>
<p>
    The workflow is to *always* update you local tree before commiting:
</p>
<div class="w3-code bash">
    <span>svn up</span><br>
    <span>svn ci public/file.php -m "Some intelligent and short comment."</span><br>
</div>
<p>
    Not all content belongs under version control, i.e. content on the site that is 
    automatic updated from elsewere. Add ignore flag in these:
</p>
<div class="w3-code bash">
    <span>svn ps svn:ignore repo .</span><br>
    <span>svn ci . -m "Added ignore flag in RPM-repository."</span><br>
</div>
