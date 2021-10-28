	<?php
	$ci = &get_instance();
	?>
	<ul class="nav nav-tabs">
		<li<?php if ($ci->router->fetch_method() == 'index') {
		print ' class="active"';
	}
	?>>
			<a href="/admin">Listings</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'tx') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/tx">Transactions</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'categories') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/categories">Categories</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'users') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/users">Users</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'comments') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/comments">Comments</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'tos') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/tos">TOS</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'settings') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/settings">Payment Settings</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'config') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/config">Configure</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'seo') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/seo">SEO</a>
		</li>
		<li<?php if ($ci->router->fetch_method() == 'mail') {
		print ' class="active"';
	}
	?>>
			<a href="/admin/mail">Mail Server</a>
		</li>
		<li>
			<a href="/admin/logout">Log out</a>
		</li>
	</ul>