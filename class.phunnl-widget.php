<?php
/**
 * @package phunnl
 */
class phunnl_Widget extends WP_Widget {

	function __construct() {
		load_plugin_textdomain( 'phunnl' );
		
		parent::__construct(
			'phunnl_widget',
			__( 'phunnl Widget' , 'phunnl'),
			array( 'description' => __( 'show phunnl calls number' , 'phunnl') )
		);

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_head', array( $this, 'css' ) );
		}
	}

	function css() {
?>

<style type="text/css">
.a-stats {
	width: auto;
}
.a-stats a {
	background: #7CA821;
	background-image:-moz-linear-gradient(0% 100% 90deg,#5F8E14,#7CA821);
	background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#7CA821),to(#5F8E14));
	border: 1px solid #5F8E14;
	border-radius:3px;
	color: #CFEA93;
	cursor: pointer;
	display: block;
	font-weight: normal;
	height: 100%;
	-moz-border-radius:3px;
	padding: 7px 0 8px;
	text-align: center;
	text-decoration: none;
	-webkit-border-radius:3px;
	width: 100%;
}
.a-stats a:hover {
	text-decoration: none;
	background-image:-moz-linear-gradient(0% 100% 90deg,#6F9C1B,#659417);
	background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#659417),to(#6F9C1B));
}
.a-stats .count {
	color: #FFF;
	display: block;
	font-size: 15px;
	line-height: 16px;
	padding: 0 13px;
	white-space: nowrap;
}
</style>

<?php
	}

	function form( $instance ) {
		if ( $instance && isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		else {
			$title = __( 'phunnl ABC Co' , 'phunnl' );
		}
?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' , 'phunnl'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function widget( $args, $instance ) {
		 

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = __( 'ABC Company' , 'phunnl' );
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'];
			echo esc_html( $instance['title'] );
			echo $args['after_title'];
		}
?>

	<div class="a-stats">
		<a href="https://phunnl.com" target="_blank" title=""><?php printf( _n( 'some stuff', 'some stuff', 0 , 'phunnl'), number_format_i18n( $count ) ); ?></a>
	</div>

<?php
		echo $args['after_widget'];
	}
}

//function phunnl_register_widgets() {
//    register_widget( 'phunnl_Widget' );
//}

//add_action( 'widgets_init', 'phunnl_register_widgets' );
