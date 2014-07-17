<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h1><?php echo $pagetitle ?></h1>

<div>
	<p><b><a href="/blog/version-0-3">Version 0.3.0 - 2014-07-17</a></b></p>
	<ul>
		<li><strong>Feature:</strong> You can now follow users. <a href="https://github.com/Clidus/gwl/issues/50">#29</a></li>
		<li><strong>Feature:</strong> You can see what you and your followers have done recently in a new homepage feed. <a href="https://github.com/Clidus/gwl/issues/50">#29</a></li>
		<li><strong>Feature:</strong> HTTPS is now supported and enforced. <a href="https://github.com/Clidus/gwl/issues/50">#50</a></li>
		<li><strong>Feature:</strong> Added comments to blog posts. <a href="https://github.com/Clidus/gwl/issues/12">#12</a></li>
		<li><strong>Feature:</strong> Added a blog post archive.</li>
		<li><strong>Feature:</strong> Added YouTube support to markdown using the format ![youtube](l7iVsdRbhnc).</li>
		<li><strong>Change:</strong> Uncompletable games are no longer included in the completion percentage. <a href="https://github.com/Clidus/gwl/issues/52">#52</a></li>
		<li><strong>Change:</strong> Making a comment on an event will now bump it to the top of the feed.</li>
		<li><strong>Change:</strong> New look for events.</li>
		<li><strong>Change:</strong> New look for the blog.</li>
		<li><strong>Change:</strong> New look site footer with social links.</li>
		<li><strong>Change:</strong> New logo.</li>
		<li><strong>Change:</strong> Code restructure for the release of <a href="http://www.ignitionpowered.co.uk/">Ignition</a>.</li>
		<li><strong>Change:</strong> Upgraded CodeIgniter from v2.1.4 to v2.2.0.</li>
		<li><strong>Change:</strong> Upgraded Bootstrap from v3.0 to v3.2.</li>
		<li><strong>Bug Fix:</strong> There was a missing "and" when an event contained "is playing" and one status. <a href="https://github.com/Clidus/gwl/issues/51">#51</a></li>
		<li><strong>Bug Fix:</strong> Some errors returned no message.</li>
	</ul>
	<p><b>Version 0.2.3 - 2014-06-18</b></p>
	<ul>
		<li><strong>Change:</strong> Added meta tags to blog posts.</li>
	</ul>
	<p><b>Version 0.2.2 - 2014-05-24</b></p>
	<ul>
		<li><strong>Bug Fix:</strong> Changing a game to no longer playing would produce a blank event.</li>
		<li><strong>Bug Fix:</strong> Prioritise old game caches in cron job.</li>
	</ul>
	<p><b>Version 0.2.1 - 2014-05-13</b></p>
	<ul>
		<li><strong>Feature:</strong> Cron job to update game cache.</li>
	</ul>
	<p><b><a href="/blog/version-0-2">Version 0.2.0 - 2014-05-11</a></b></p>
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