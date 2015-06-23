<?php

/*
Plugin Name: Listen360 Reviews Widget
Plugin URI: http://developers.listen360.com/public-reviews-wordpress-widget.html
Description: Adds a shortcode [listen360_reviews] that inserts your company's Listen360 reviews in pages or posts.
Author: Listen360
Version: 0.1
Author URI: http://www.listen360.com
License: GPLv2
*/

add_option('listen360_reviews_location_url', '');

function listen360_reviews_url( $identifier ) {
  if ( $identifier == "" ) {
    $identifier = get_option('listen360_reviews_location_url');
  }

  $str = "https://reviews.listen360.com/" . $identifier;

  if ( substr($str, -1) != '/' ) {
    $str .= "/";
  }

  return $str;
}


function listen360_reviews_shortcode( $atts ) {
  $atts = shortcode_atts( array(
    'per_page' => '10',
    'identifier' => ""
  ), $atts );

  $identifier = "";

  if ( $atts['identifier'] != "" ) {
    $identifier = $atts['identifier'];
  }

  $url = listen360_reviews_url( $identifier );

  if ( get_headers($url, 1)[0] == 'HTTP/1.0 200 OK' ) {
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"https://reviews.listen360.com/assets/listen360.css\" />\n";
    readfile($url . "aggregaterating");
    readfile($url . "stream?per_page=" . $atts['per_page']);
  }
}

add_shortcode('listen360_reviews', 'listen360_reviews_shortcode');

add_action( 'admin_menu', 'listen360_reviews_menu' );


function listen360_reviews_menu() {
  add_options_page( 'Listen360 Reviews Options', 'Listen360', 'manage_options', 'listen360-reviews-widget', 'listen360_reviews_plugin_options' );
}

function listen360_reviews_plugin_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  $opt_val = get_option( 'listen360_reviews_location_url' );

  if( isset($_POST[ 'listen360_reviews_form_submitted' ]) && $_POST[ 'listen360_reviews_form_submitted' ] == 'Y' ) {
      // Read their posted value
      $opt_val = $_POST[ 'listen360_reviews_location_identifier' ];

      // Save the posted value in the database
      update_option( 'listen360_reviews_location_url', $opt_val );

?>
<div class="updated"><p><strong><?php _e('Listen360 Reviews settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

  }

echo '<div class="wrap">';
echo "<h2>" . __( 'Listen360 Reviews Settings', 'menu-test' ) . "</h2>";

?>

<form name="listen360_reviews_options_form" method="post" action="">
<input type="hidden" name="listen360_reviews_form_submitted" value="Y">

<p><?php _e("Location Identifier:&nbsp;&nbsp;", 'menu-test' ); ?>
<input type="text" name="listen360_reviews_location_identifier" value="<?php echo $opt_val; ?>" size="40">
</p>
<p>
  <?php _e("This is the identifier at the end of your Listen360 Reviews page url.  For example:&nbsp;&nbsp;<strong>https://reviews.listen360.com/your-identifier-here</strong>."); ?>
</p>
<p>
  <?php _e("To use the widget, simply insert the shortcode <code>[listen360_reviews]</code> on any page or post where you wish your reviews to appear.  You can reference any location's reviews by specifying an identifier in the shortcode: <code>[listen360_reviews identifier=your-location-identifier]</code>."); ?>
</p>
<p>
  <?php _e("For more information on the widget, please visit <a href=\"http://developers.listen360.com/public-reviews-wordpress-widget.html\" target=\"blank\">the documentation</a>."); ?>
</p>

<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php

}


/*
Listen360 Reviews Widget
Copyright (C) 2014 by Listen360

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
