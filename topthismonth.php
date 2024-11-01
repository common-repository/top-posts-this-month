<?php
/*
Plugin Name: Top Posts This Month
Version: 1.0
Plugin URI: http://byme.se
Author: Design of Tommie Hansen
Author URI: http://byme.se
Description: Shows the top posts for the current month by checking the number of comments for posts at the current month.
*/
?>
<?php
function moncoms() {  ?>
		<h2><?php $options = get_option("moncoms_options"); if ($options['moncoms_title'] == "") { echo 'Top posts this month'; } else { echo $options['moncoms_title']; } ?></h2>
		<?php echo $after_title; ?>
		<ul>
		<?php
		global $wpdb;
		$options = get_option("moncoms_options");
		$moncomnum = $options['moncoms_number'];
		if($moncomnum == '') { $moncomnum = '10'; }
		$popular_posts = $wpdb->get_results("
		SELECT comment_count, ID, post_title
		FROM $wpdb->posts WHERE post_status = 'publish' AND YEAR(post_date) = YEAR(CURDATE()) AND MONTH(post_date) = MONTH(CURDATE())
		ORDER BY comment_count DESC
		LIMIT $moncomnum
		");
		foreach($popular_posts as $post) {
		echo "<li><a href='". get_permalink($post->ID) ."'>".$post->post_title." <em>(".get_comments_number($post->ID).")</em></a></li>"; }
		echo '</ul>' . $after_widget;
		?>
		
<?php } //end fluff ?>

<?php
function widget_moncoms($args) {
		extract($args);
		echo $before_widget;
		moncoms();
		echo $after_widget;
}
?>

<?php 
function widget_moncoms_control() {

    $options = get_option("moncoms_options");
    if ($_POST['moncoms-submit']) {
        $options['moncoms_title'] = htmlspecialchars($_POST['moncoms-widgettitle']);
		$options['moncoms_number'] = htmlspecialchars($_POST['moncoms-number']);
        update_option("moncoms_options", $options);
    }
?>
		<p>
		<label for="moncoms-widgettitle">Widget title </label>
		<input type="text" id="moncoms-widgettitle" name="moncoms-widgettitle" value="<?php echo $options['moncoms_title']; ?>"><br /><br />
		<label for="moncoms-number">Number to show</label>
		<input style="width:30px" type="text" id="moncoms-number" name="moncoms-number" value="<?php echo $options['moncoms_number']; ?>">
		<input type="hidden" id="moncoms-submit"  name="moncoms-submit" value="1" />
		</p>		
<?php } ?>

<?php
function moncoms_init() {
register_sidebar_widget(__('Most Commented This Month'), 'widget_moncoms');
register_widget_control('Most Commented This Month', 'widget_moncoms_control');
}
add_action("init", "moncoms_init");
?>