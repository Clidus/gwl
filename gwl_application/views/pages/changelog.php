<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h1><?php echo $pagetitle ?></h1>

<div>
	<p><b>Version 0.2.2 - 2014-05-24</b></p>
	<ul>
		<li><strong>Bug Fix:</strong> Changing a game to no longer playing would produce a blank event.</li>
		<li><strong>Bug Fix:</strong> Prioritise old game caches in cron job.</li>
	</ul>
	<p><b>Version 0.2.1 - 2014-05-13</b></p>
	<ul>
		<li><strong>Feature:</strong> Cron job to update game cache.</li>
	</ul>
	<p><b><a href="/blog/version-0-2-0">Version 0.2.0 - 2014-05-11</a></b></p>
	<ul>
		<li><strong>Feature:</strong> Collection stats now update live as filters are changed. <a href="https://github.com/Clidus/gwl/issues/35">#35</a></li>
		<li><strong>Feature:</strong> List and completion filters now have numbers against them. <a href="https://github.com/Clidus/gwl/issues/35">#35</a></li>
		<li><strong>Feature:</strong> “Add Games” button added to your collection page. <a href="https://github.com/Clidus/gwl/issues/22">#22</a></li>
		<li><strong>Feature:</strong> “What’s Happening” box on homepage shows recent user activity. <a href="https://github.com/Clidus/gwl/issues/21">#21</a></li>
		<li><strong>Feature:</strong> Custom 404 page added. <a href="https://github.com/Clidus/gwl/issues/43">#43</a></li>
		<li><strong>Change:</strong> Wanted games are now excluded from collection total and completion percentage. <a href="https://github.com/Clidus/gwl/issues/37">#37</a></li>
		<li><strong>Change:</strong> “View More” button on collection page made more prominent.</li>
		<li><strong>Change:</strong> Clicking a platform checkbox when logged out now returns a prompt to login rather than being disabled. Clicking the checkbox before a game has been added to a list prompts the user to do so. <a href="https://github.com/Clidus/gwl/issues/39">#39</a></li>
		<li><strong>Change:</strong> Errors have been moved to a language file to allow us to add localisation in the future. <a href="https://github.com/Clidus/gwl/issues/40">#40</a></li>
		<li><strong>Change:</strong> All urls are now relative paths. </li>
		<li><strong>Bug Fix:</strong> Added support for unicode in game names and descriptions. <a href="https://github.com/Clidus/gwl/issues/42">#42</a></li>
	</ul>
	<p><b><a href="/blog/alpha-release">Version 0.1.0 - 2014-05-03</a></b></p>
	<ul>
		<li>Alpha release.</li>
	</ul>
</div>