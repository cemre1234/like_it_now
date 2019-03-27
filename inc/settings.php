<div class="wrap like-it-options">
<h1>Like It Now Settings</h1>
<p>It is a very simple like-dislike button for your posts</p>
<p>Three shortcode available</p>
<ul><li><code>[like_it]</code>-which displays the like button and the number of likes received</li>
    <li><code>[like_it_posts]</code>-which displays your lists of posts(Last 10 posts) with likes numbers</li>
    <li><code>[like_it_tags]</code>-which displays your lists of tags(Last 10 tags) with total likes numbers</li>
</ul>
<form id="form_id" method="post" action="options.php">

<?php settings_fields( 'like_it_now' ); ?>
<?php do_settings_sections( 'like_it_group' ); ?>
<?php submit_button(); ?>

</form>
</div>
