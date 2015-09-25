<?php
/*
Plugin Name: CW
*/

add_action( 'widgets_init', function(){
	register_widget( 'Image_Widget' );
});



add_action('admin_enqueue_scripts' , function() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
});

class Custom_Widget extends WP_Widget {

	protected $elements = array();
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'image_widget', // Base ID
			__( 'Feature Widget', 'text_domain' ), // Name
			array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
			);
		$this->elements[] = array(
			'type' => 'image' ,
			'id' =>	'asdasd',
			'title'	=> 'fdssfdfsdfds',
			'placeholder' => 'this sfsdf the placeholder' , 
			);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo $instance['nix'];
		echo __( 'Hello, World!', 'text_domain' );
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
		foreach ($this->elements as $value) {
			$this->render_input($value, $instance);
		}

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ($this->elements as $value) {
			$instance[$value['id']] = ( ! empty( $new_instance[$value['id']] ) ) ? strip_tags( $new_instance[$value['id']]) : '';
		}
		return $instance;
	}

	protected function render_input($arr , $instance) {
		$value = ! empty( $instance[$arr['id']] ) ? $instance[$arr['id']] : $arr['placeholder'];
		switch ($arr['type']) {
			case 'text':
			?>
			<p>
				<label for="<?php echo $this->get_field_id( $arr['id'] ); ?>"><?php echo $arr['title']; ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( $arr['id'] ); ?>" name="<?php echo $this->get_field_name( $arr['id'] ); ?>" type="text" value="<?php echo esc_attr($value); ?>">
			</p>
			<?php
			break;
			case 'number':
			?>
			<p>
				<label for="<?php echo $this->get_field_id( $arr['id'] ); ?>"><?php echo $arr['title']; ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( $arr['id'] ); ?>" name="<?php echo $this->get_field_name( $arr['id'] ); ?>" type="number" value="<?php echo esc_attr($value); ?>">
			</p>
			<?php
			break;

			case 'textarea':
			?>
			<p>
				<label for="<?php echo $this->get_field_id( $arr['id'] ); ?>"><?php echo $arr['title']; ?></label>
				<textarea class="widefat" rows="8" name="<?php echo $this->get_field_name( $arr['id'] ); ?>"
					id="<?php echo $this->get_field_id( $arr['id'] ); ?>" ><?php echo esc_attr($value);?></textarea>
				</p>
				<?php
				break;
				case 'wysiwyg':
				$args = array(
					'textarea_rows' => 15,
					'teeny' => true,
					'textarea_name' => $this->get_field_name( $arr['id'] ),
					);
					?>
					<?php add_thickbox(); ?>
					<div id="my-content-id" style="display:none;">

						<?php 
						wp_editor( $value, 'editor', $args );

						?>	
					</div>
					<a href="#TB_inline?inlineId=my-content-id" class="thickbox">View my inline content!</a>	
					<?php
					break;
					case 'image':
					?>
					<?php $id = $this->get_field_id( $arr['id'] ); ?>
					<?php if(isset($id)) {
						?>
						<img id="<?php echo esc_attr($value); ?>-image" src="<?php echo esc_attr($value); ?>" alt="" width="150px">
						<?php
					}
					?>
					<p>
						<input type="text" 
						name="<?php echo $this->get_field_name( $arr['id'] ); ?>"
						id="<?php echo $this->get_field_id( $arr['id'] ); ?>"
						value="<?php if(isset($value)) {echo esc_attr($value);}?>">	
					</p>

					<input class="upload_image_button button button-primary" type="button" value="Upload Image" />
					<script>
						jQuery(document).ready(function($) {
							jQuery(document).on("click", ".upload_image_button", function() {
								jQuery.data(document.body, 'prevElement', jQuery(this).prev());
								window.send_to_editor = function(html) {
									var imgurl = jQuery('img',html).attr('src');
									var inputText = jQuery.data(document.body, 'prevElement');
									if(inputText != undefined && inputText != '')
									{
										inputText.val(imgurl);
										jQuery('#cat_settings\\[<?php echo $setting['id']; ?>\\]-image').attr('src' , imgurl )
									}
									tb_remove();
								};
								tb_show('', 'media-upload.php?type=image&TB_iframe=true');
								return false;
							});
						});
					</script>

					<?php
					break;
				}
			} 
} // class Image_Widget
