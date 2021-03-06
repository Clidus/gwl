<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h1><?php echo $pagetitle ?></h1>

<div>
	<p><b>Version 0.4.7 - 2017-07-23</b></p>
	<ul>
		<li><b>Bug Fix:</b> A duplicate game entry in the database would produce more duplicate entries. <a href="https://github.com/Clidus/gwl/issues/114">#114</a></li>
		<li><b>Bug Fix:</b> Fixed error when sql_mode=only_full_group_by is set. <a href="https://github.com/Clidus/gwl/issues/114">#115</a></li>
	</ul>
	<p><b>Version 0.4.6 - 2016-08-21</b></p>
	<ul>
		<li><b>Change:</b> Upgraded to Ignition 0.5.0. <a href="https://github.com/Clidus/gwl/issues/109">#109</a></li>
	</ul>
	<p><b>Version 0.4.5 - 2016-05-03</b></p>
	<ul>
		<li><strong>Change:</strong> Crawler reborn as "Super Crawler". Now pulls 100 games at a time. <a href="https://github.com/Clidus/gwl/issues/101">#101</a></li>
	</ul>
	<p><b>Version 0.4.4 - 2016-04-28</b></p>
	<ul>
		<li><strong>Change:</strong> Reduced dependencies on Giant Bomb. <a href="https://github.com/Clidus/gwl/issues/57">#57</a></li>
		<li><strong>Change:</strong> Cron job now processes search logs to expand game database. <a href="https://github.com/Clidus/gwl/issues/96">#96</a></li>
	</ul>
	<p><b>Version 0.4.3 - 2016-02-18</b></p>
	<ul>
		<li><strong>Bug Fix:</strong> Giant Bomb now require user agent on API requests. <a href="https://github.com/Clidus/gwl/issues/90">#90</a></li>
	</ul>
	<p><b>Version 0.4.2 - 2015-10-31</b></p>
	<ul>
		<li><strong>Change:</strong> Log API requests to database. <a href="https://github.com/Clidus/gwl/issues/57">#57</a></li>
	</ul>
	<p><b>Version 0.4.1 - 2015-10-25</b></p>
	<ul>
		<li><strong>Change:</strong> Crawler to update game data now collects all the platforms a game is on. <a href="https://github.com/Clidus/gwl/issues/57">#57</a></li>
	</ul>
	<p><b><a href="/blog/version-0-4">Version 0.4.0 - 2015-10-24</a></b></p>
	<ul>
		<li><b>Feature:</b> Export collection to CSV. <a href="https://github.com/Clidus/gwl/issues/73">#73</a></li>
		<li><b>Feature:</b> View collection completion by platform. <a href="https://github.com/Clidus/gwl/issues/36">#36</a></li>
		<li><b>Feature:</b> Forgot your password. <a href="https://github.com/Clidus/gwl/issues/44">#44</a></li>
		<li><b>Feature:</b> "Who's played this?" show users who have completed / played a game on game pages. <a href="https://github.com/Clidus/gwl/issues/59">#59</a></li>
		<li><b>Feature:</b> Make Android status bar lemon yellow. <a href="https://github.com/Clidus/gwl/issues/60">#60</a></li>
		<li><b>Change:</b> Reduce width of profile page side bar. <a href="https://github.com/Clidus/gwl/issues/56">#56</a></li>
		<li><b>Change:</b> Upgraded to CodeIgniter 2.2.3</li>
		<li><b>Change:</b> Upgraded to Ignition 0.4.0 <a href="https://github.com/Clidus/gwl/issues/72">#72</a></li>
	</ul>
	<p><b>Version 0.3.2 - 2015-01-17</b></p>
	<ul>
		<li><strong>Change:</strong> Upgraded Ignition from v0.1 to v0.3.</li>
		<li><strong>Change:</strong> Upgraded Bootstrap from v3.2 to v3.3.1.</li>
	</ul>
	<p><b>Version 0.3.1 - 2015-01-02</b></p>
	<ul>
		<li><strong>Bug Fix:</strong> Cron job to update game caches got stuck on a game that no longer exists. <a href="https://github.com/Clidus/gwl/issues/53">#53</a></li>
	</ul>
	<p><b><a href="/blog/version-0-3">Version 0.3.0 - 2014-07-17</a></b></p>
	<ul>
		<li><strong>Feature:</strong> You can now follow users. <a href="https://github.com/Clidus/gwl/issues/29">#29</a></li>
		<li><strong>Feature:</strong> You can see what you and your followers have done recently in a new homepage feed. <a href="https://github.com/Clidus/gwl/issues/29">#29</a></li>
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