<?php  /**
 * Content Widget
 *
 * This file is used to register and display the Layers - Content widget.
 *
 * @package Layers
 * @since Layers 1.0.0
 */
if( !class_exists( 'Layers_Content_Widget' ) ) {
	class Layers_Content_Widget extends Layers_Widget {

		/**
		*  Widget construction
		*/
		function Layers_Content_Widget(){

			/**
			* Widget variables
			*
		 	* @param  	string    		$widget_title    	Widget title
		 	* @param  	string    		$widget_id    		Widget slug for use as an ID/classname
		 	* @param  	string    		$post_type    		(optional) Post type for use in widget options
		 	* @param  	string    		$taxonomy    		(optional) Taxonomy slug for use as an ID/classname
		 	* @param  	array 			$checkboxes    	(optional) Array of checkbox names to be saved in this widget. Don't forget these please!
		 	*/
			$this->widget_title = __( 'Content' , 'layerswp' );
			$this->widget_id = 'column';
			$this->post_type = '';
			$this->taxonomy = '';
			$this->checkboxes = array();

			/* Widget settings. */
			$widget_ops = array(
				'classname'   => 'obox-layers-' . $this->widget_id .'-widget',
				'description' => __( 'This widget is used to display your ', 'layerswp' ) . $this->widget_title . '.',
			);

			/* Widget control settings. */
			$control_ops = array(
				'width'   => LAYERS_WIDGET_WIDTH_LARGE,
				'height'  => NULL,
				'id_base' => LAYERS_THEME_SLUG . '-widget-' . $this->widget_id,
			);

			/* Create the widget. */
			parent::__construct(
				LAYERS_THEME_SLUG . '-widget-' . $this->widget_id ,
				$this->widget_title,
				$widget_ops,
				$control_ops
			);

			/* Setup Widget Defaults */
			$this->defaults = array (
				'title' => __( 'Our Services', 'layerswp' ),
				'excerpt' => __( 'Our services run deep and are backed by over ten years of experience.', 'layerswp' ),
				'design' => array(
					'layout' => 'layout-boxed',
					'liststyle' => 'list-grid',
					'columns' => '3',
					'gutter' => 'on',
					'background' => array(
						'position' => 'center',
						'repeat' => 'no-repeat'
					),
					'fonts' => array(
						'align' => 'text-left',
						'size' => 'medium',
						'color' => NULL,
						'shadow' => NULL
					)
				),
			);

			/* Setup Widget Repeater Defaults */
			$this->register_repeater_defaults( 'column', 3, array(
				'title' => __( 'Your service title', 'layerswp' ),
				'excerpt' => __( 'Give us a brief description of the service that you are promoting. Try keep it short so that it is easy for people to scan your page.', 'layerswp' ),
				'width' => '4',
				'design' => array(
					'imagealign' => 'image-top',
					'background' => NULL,
					'fonts' => array(
						'align' => 'text-left',
						'size' => 'medium',
						'color' => NULL,
						'shadow' => NULL
					)
				)
			) );
			
		}

		/**
		*  Widget front end display
		*/
		function widget( $args, $instance ) {

			// Turn $args array into variables.
			extract( $args );

			// $instance Defaults
			$instance_defaults = $this->defaults;

			// If we have information in this widget, then ignore the defaults
			if( !empty( $instance ) ) $instance_defaults = array();

			// Parse $instance
			$widget = wp_parse_args( $instance, $instance_defaults );

			// Enqueue Masonry if need be
			if( 'list-masonry' == $this->check_and_return( $widget , 'design', 'liststyle' ) ) $this->enqueue_masonry();

			// Set the background styling
			if( !empty( $widget['design'][ 'background' ] ) ) layers_inline_styles( '#' . $widget_id, 'background', array( 'background' => $widget['design'][ 'background' ] ) );
			if( !empty( $widget['design']['fonts'][ 'color' ] ) ) layers_inline_styles( '#' . $widget_id, 'color', array( 'selectors' => array( '.section-title h3.heading' , '.section-title div.excerpt' ) , 'color' => $widget['design']['fonts'][ 'color' ] ) );

			// Apply the advanced widget styling
			$this->apply_widget_advanced_styling( $widget_id, $widget );

			/**
			* Generate the widget container class
			*/
			$widget_container_class = array();
			$widget_container_class[] = 'widget';
			$widget_container_class[] = 'layers-content-widget';
			$widget_container_class[] = 'row';
			$widget_container_class[] = 'content-vertical-massive';
			$widget_container_class[] = $this->check_and_return( $widget , 'design', 'advanced', 'customclass' ); // Apply custom class from design-bar's advanced control.
			$widget_container_class[] = $this->get_widget_spacing_class( $widget );
			$widget_container_class = implode( ' ', apply_filters( 'layers_content_widget_container_class' , $widget_container_class ) ); ?>

			<section id="<?php echo esc_attr( $widget_id ); ?>" class="<?php echo esc_attr( $widget_container_class ); ?>">
				<?php if( '' != $this->check_and_return( $widget , 'title' ) ||'' != $this->check_and_return( $widget , 'excerpt' ) ) { ?>
					<div class="container clearfix">
						<?php /**
						* Generate the Section Title Classes
						*/
						$section_title_class = array();
						$section_title_class[] = 'section-title clearfix';
						$section_title_class[] = $this->check_and_return( $widget , 'design', 'fonts', 'size' );
						$section_title_class[] = $this->check_and_return( $widget , 'design', 'fonts', 'align' );
						$section_title_class[] = ( $this->check_and_return( $widget, 'design', 'background' , 'color' ) && 'dark' == layers_is_light_or_dark( $this->check_and_return( $widget, 'design', 'background' , 'color' ) ) ? 'invert' : '' );
						$section_title_class = implode( ' ', $section_title_class ); ?>
						<div class="<?php echo $section_title_class; ?>">
							<?php if( '' != $widget['title'] ) { ?>
								<h3 class="heading"><?php echo $widget['title'] ?></h3>
							<?php } ?>
							<?php if( '' != $widget['excerpt'] ) { ?>
								<div class="excerpt"><?php echo $widget['excerpt']; ?></div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<?php if( ! empty( $widget[ 'columns' ] ) ) { ?>
					<div class="row <?php echo $this->get_widget_layout_class( $widget ); ?> <?php echo $this->check_and_return( $widget , 'design', 'liststyle' ); ?>">
						<?php // Set total width so that we can apply .last to the final container
						$total_width = 0; ?>
						<?php foreach ( explode( ',', $widget[ 'column_ids' ] ) as $column_key ) {

							// Make sure we've got a column going on here
							if( !isset( $widget[ 'columns' ][ $column_key ] ) ) continue;

							// Setup the relevant slide
							$column = $widget[ 'columns' ][ $column_key ];

							// Set the background styling
							if( !empty( $column['design'][ 'background' ] ) ) layers_inline_styles( '#' . $widget_id . '-' . $column_key , 'background', array( 'background' => $column['design'][ 'background' ] ) );
							if( !empty( $column['design']['fonts'][ 'color' ] ) ) layers_inline_styles( '#' . $widget_id . '-' . $column_key , 'color', array( 'selectors' => array( 'h5.heading a', 'h5.heading' , 'div.excerpt' , 'div.excerpt p' ) , 'color' => $column['design']['fonts'][ 'color' ] ) );
							if( !empty( $column['design']['fonts'][ 'shadow' ] ) ) layers_inline_styles( '#' . $widget_id . '-' . $column_key , 'text-shadow', array( 'selectors' => array( 'h5.heading a', 'h5.heading' , 'div.excerpt' , 'div.excerpt p' )  , 'text-shadow' => $column['design']['fonts'][ 'shadow' ] ) );

							if( !isset( $column[ 'width' ] ) ) $column[ 'width' ] = $this->column_defaults[ 'width' ];
							// Add the correct span class
							$span_class = 'span-' . $column[ 'width' ];

							// Add .last to the final column
							$total_width += $column[ 'width' ];

							if( 12 == $total_width ) {
								$span_class .= ' last';
								$total_width = 0;
							} elseif( $total_width > 12 ) {
								$total_width = 0;
							}

							// Set Featured Media
							$featureimage = $this->check_and_return( $column , 'design' , 'featuredimage' );
							$featurevideo = $this->check_and_return( $column , 'design' , 'featuredvideo' );

							// Set Image Sizes
							if( isset( $column['design'][ 'imageratios' ] ) ){

								// Translate Image Ratio into something usable
								$image_ratio = layers_translate_image_ratios( $column['design'][ 'imageratios' ] );

								if( !isset( $column[ 'width' ] ) ) $column[ 'width' ] = 6;

								if( 4 > $column['width'] ){
									$use_image_ratio = $image_ratio . '-medium';
								} else {
									$use_image_ratio = $image_ratio . '-large';
								}

							} else {
								if( 4 > $column['width'] ){
									$use_image_ratio = 'medium';
								} else {
									$use_image_ratio = 'full';
								}
							}

							$media = layers_get_feature_media(
								$featureimage ,
								$use_image_ratio ,
								$featurevideo
							);

							// Set the column link
							$link = $this->check_and_return( $column , 'link' );

 							/**
							* Set Individual Column CSS
							*/
							$column_class = array();
							$column_class[] = 'layers-masonry-column';
							$column_class[] = $this->id_base . '-' . $column_key;
							$column_class[] = $span_class;
							$column_class[] = ( 'list-masonry' == $this->check_and_return( $widget, 'design', 'liststyle' ) ? 'no-gutter' : '' );
							$column_class[] = 'column' . ( 'on' != $this->check_and_return( $widget, 'design', 'gutter' ) ? '-flush' : '' );
							if( '' != $this->check_and_return( $column, 'design' , 'background', 'image' ) || '' != $this->check_and_return( $column, 'design' , 'background', 'color' ) ) {
								$column_class[] = 'content';
							}
							if( false != $media ) {
								$column_class[] = 'has-image';
							}
							$column_class = implode( ' ', $column_class ); ?>

							<div id="<?php echo $widget_id; ?>-<?php echo $column_key; ?>" class="<?php echo $column_class; ?>">
								<?php /**
								* Set Overlay CSS Classes
								*/
								$column_inner_class = array();
								$column_inner_class[] = 'media';
								if( !$this->check_and_return( $widget, 'design', 'gutter' ) ) {
									$column_inner_class[] = 'no-push-bottom';
								}
								if( $this->check_and_return( $column, 'design', 'background' , 'color' ) ) {
									if( 'dark' == layers_is_light_or_dark( $this->check_and_return( $column, 'design', 'background' , 'color' ) ) ) {
										$column_inner_class[] = 'invert';
									}
								} else {
									if( $this->check_and_return( $widget, 'design', 'background' , 'color' ) && 'dark' == layers_is_light_or_dark( $this->check_and_return( $widget, 'design', 'background' , 'color' ) ) ) {
										$column_inner_class[] = 'invert';
									}
								}

								$column_inner_class[] = $this->check_and_return( $column, 'design', 'imagealign' );
								$column_inner_class[] = $this->check_and_return( $column, 'design', 'fonts' , 'size' );
								$column_inner_class = implode( ' ', $column_inner_class ); ?>

								<div class="<?php echo $column_inner_class; ?>">
									<?php if( NULL != $media ) { ?>
										<div class="media-image <?php echo ( ( isset( $column['design'][ 'imageratios' ] ) && 'image-round' == $column['design'][ 'imageratios' ] ) ? 'image-rounded' : '' ); ?>">
											<?php if( NULL != $link ) { ?><a href="<?php echo $link; ?>"><?php  } ?>
												<?php echo $media; ?>
											<?php if( NULL != $link ) { ?></a><?php  } ?>
										</div>
									<?php } ?>

									<?php if( $this->check_and_return( $column, 'title' ) || $this->check_and_return( $column, 'excerpt' ) || $this->check_and_return( $column, 'link_text' ) ) { ?>
										<div class="media-body <?php echo ( isset( $column['design']['fonts'][ 'align' ] ) ) ? $column['design']['fonts'][ 'align' ] : ''; ?>">
											<?php if( $this->check_and_return( $column, 'title') ) { ?>
												<h5 class="heading">
													<?php if( NULL != $link && ! ( isset( $column['link'] ) && $this->check_and_return( $column , 'link_text' ) ) ) { ?><a href="<?php echo $column['link']; ?>"><?php } ?>
														<?php echo $column['title']; ?>
													<?php if( NULL != $link && ! ( isset( $column['link'] ) && $this->check_and_return( $column , 'link_text' ) ) ) { ?></a><?php } ?>
												</h5>
											<?php } ?>
											<?php if( $this->check_and_return( $column, 'excerpt' ) ) { ?>
												<div class="excerpt"><?php layers_the_content( $column['excerpt'] ); ?></div>
											<?php } ?>
											<?php if( isset( $column['link'] ) && $this->check_and_return( $column , 'link_text' ) ) { ?>
												<a href="<?php echo $column['link']; ?>" class="button btn-<?php echo $this->check_and_return( $column , 'design' , 'fonts' , 'size' ); ?>"><?php echo $column['link_text']; ?></a>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

			</section>
			<?php if( 'list-masonry' == $this->check_and_return( $widget , 'design', 'liststyle' ) ) { ?>
				<script>
					jQuery(function($){
						layers_masonry_settings[ '<?php echo $widget_id; ?>' ] = [{
								itemSelector: '.layers-masonry-column',
								layoutMode: 'masonry',
								gutter: <?php echo ( isset( $widget['design'][ 'gutter' ] ) ? 20 : 0 ); ?>
							}];

						$('#<?php echo $widget_id; ?>').find('.list-masonry').layers_masonry( layers_masonry_settings[ '<?php echo $widget_id; ?>' ][0] );
					});
				</script>
			<?php } // masonry trigger ?>
		<?php }

		/**
		*  Widget update
		*/

		function update($new_instance, $old_instance) {
			if ( isset( $this->checkboxes ) ) {
				foreach( $this->checkboxes as $cb ) {
					if( isset( $old_instance[ $cb ] ) ) {
						$old_instance[ $cb ] = strip_tags( $new_instance[ $cb ] );
					}
				} // foreach checkboxes
			} // if checkboxes
			return $new_instance;
		}

		/**
		*  Widget form
		*
		* We use regular HTML here, it makes reading the widget much easier than if we used just php to echo all the HTML out.
		*
		*/
		function form( $instance ){

			// $instance Defaults
			$instance_defaults = $this->defaults;

			// If we have information in this widget, then ignore the defaults
			if( !empty( $instance ) ) $instance_defaults = array();

			// Parse $instance
			$widget = wp_parse_args( $instance, $instance_defaults );

			$design_bar_components = apply_filters( 'layers_' . $this->widget_id . '_widget_design_bar_components' , array(
				'layout',
				'liststyle' => array(
					'icon-css' => 'icon-list-masonry',
					'label' => __( 'List Style', 'layerswp' ),
					'wrapper-class' => 'layers-small to layers-pop-menu-wrapper layers-animate',
					'elements' => array(
						'liststyle' => array(
							'type' => 'select-icons',
							'name' => $this->get_layers_field_name( 'design', 'liststyle' ) ,
							'id' =>  $this->get_layers_field_id( 'design', 'liststyle' ) ,
							'value' => ( isset( $widget['design'][ 'liststyle' ] ) ) ? $widget['design'][ 'liststyle' ] : NULL,
							'options' => array(
								'list-grid' => __( 'Grid' , 'layerswp' ),
								'list-masonry' => __( 'Masonry' , 'layerswp' )
							)
						),
						'gutter' => array(
							'type' => 'checkbox',
							'label' => __( 'Gutter' , 'layerswp' ),
							'name' => $this->get_layers_field_name( 'design', 'gutter' ) ,
							'id' =>  $this->get_layers_field_id( 'design', 'gutter' ) ,
							'value' => ( isset( $widget['design']['gutter'] ) ) ? $widget['design']['gutter'] : NULL
						)
					)
				),
				'fonts',
				'background',
				'advanced',
			) );

			$this->design_bar(
				'side', // CSS Class Name
				array(
					'name' => $this->get_layers_field_name( 'design' ),
					'id' => $this->get_layers_field_id( 'design' ),
				), // Widget Object
				$widget, // Widget Values
				$design_bar_components // Components
			); ?>
			<div class="layers-container-large" id="layers-column-widget-<?php echo $this->number; ?>">
				
				<?php $this->form_elements()->header( array(
					'title' =>'Content',
					'icon_class' =>'text'
				) ); ?>

				<section class="layers-accordion-section layers-content">
					<p class="layers-form-item">
						<?php echo $this->form_elements()->input(
							array(
								'type' => 'text',
								'name' => $this->get_layers_field_name( 'title' ) ,
								'id' => $this->get_layers_field_id( 'title' ) ,
								'placeholder' => __( 'Enter title here' , 'layerswp' ),
								'value' => ( isset( $widget['title'] ) ) ? $widget['title'] : NULL ,
								'class' => 'layers-text layers-large'
							)
						); ?>
					</p>
					<p class="layers-form-item">
						<?php echo $this->form_elements()->input(
							array(
								'type' => 'rte',
								'name' => $this->get_layers_field_name( 'excerpt' ) ,
								'id' => $this->get_layers_field_id( 'excerpt' ) ,
								'placeholder' =>  __( 'Short Excerpt' , 'layerswp' ),
								'value' => ( isset( $widget['excerpt'] ) ) ? $widget['excerpt'] : NULL ,
								'class' => 'layers-textarea layers-large'
							)
						); ?>
					</p>
				</section>
				
				<section class="layers-accordion-section layers-content">
					<div class="layers-form-item">
						<?php $this->repeater( 'column', $widget ); ?>
					</div>
				</section>
				
			</div>

		<?php }

		function column_item( $item_guid, $widget ){
			?>
			<li class="layers-accordion-item" data-guid="<?php echo esc_attr( $item_guid ); ?>">
				<a class="layers-accordion-title">
					<span>
						<?php _e( 'Column' , 'layerswp' ); ?>
						<span class="layers-detail">
							<?php echo ( isset( $widget['title'] ) ? ': ' . substr( stripslashes( strip_tags( $widget['title'] ) ), 0 , 50 ) : NULL ); ?>
							<?php echo ( isset( $widget['title'] ) && strlen( $widget['title'] ) > 50 ? '...' : NULL ); ?>
						</span>
					</span>
				</a>
				<section class="layers-accordion-section layers-content">
					<?php $this->design_bar(
						'top', // CSS Class Name
						array( // Widget Object
							'name' => $this->get_layers_field_name( 'design' ),
							'id' => $this->get_layers_field_id( 'design' ),
							'number' => $this->number,
							'show_trash' => FALSE,
						),
						$widget, // Widget Values
						array(  // Components
							'background',
							'featuredimage',
							'imagealign',
							'fonts',
							'width' => array(
								'icon-css' => 'icon-columns',
								'label' => 'Column Width',
								'elements' => array(
									'layout' => array(
										'type' => 'select',
										'label' => __( '' , 'layerswp' ),
										'name' => $this->get_layers_field_name( 'width' ),
										'id' => $this->get_layers_field_id( 'width' ),
										'value' => ( isset( $widget['width'] ) ) ? $widget['width'] : NULL,
										'options' => array(
											'1' => __( '1 of 12 columns' , 'layerswp' ),
											'2' => __( '2 of 12 columns' , 'layerswp' ),
											'3' => __( '3 of 12 columns' , 'layerswp' ),
											'4' => __( '4 of 12 columns' , 'layerswp' ),
											'5' => __( '5 of 12 columns' , 'layerswp' ),
											'6' => __( '6 of 12 columns' , 'layerswp' ),
											'7' => __( '7 of 12 columns' , 'layerswp' ),
											'8' => __( '8 of 12 columns' , 'layerswp' ),
											'9' => __( '9 of 12 columns' , 'layerswp' ),
											'10' => __( '10 of 12 columns' , 'layerswp' ),
											'11' => __( '11 of 12 columns' , 'layerswp' ),
											'12' => __( '12 of 12 columns' , 'layerswp' )
										)
									)
								)
							),
						)
					); ?>
					<div class="layers-row">
						<p class="layers-form-item">
							<label for="<?php echo $this->get_layers_field_id( 'title' ); ?>"><?php _e( 'Title' , 'layerswp' ); ?></label>
							<?php echo $this->form_elements()->input(
								array(
									'type' => 'text',
									'name' => $this->get_layers_field_name( 'title' ),
									'id' => $this->get_layers_field_id( 'title' ),
									'placeholder' => __( 'Enter title here' , 'layerswp' ),
									'value' => ( isset( $widget['title'] ) ) ? $widget['title'] : NULL ,
									'class' => 'layers-text'
								)
							); ?>
						</p>
						<p class="layers-form-item">
							<label for="<?php echo $this->get_layers_field_id( 'excerpt' ); ?>"><?php _e( 'Excerpt' , 'layerswp' ); ?></label>
							<?php echo $this->form_elements()->input(
								array(
									'type' => 'rte',
									'name' => $this->get_layers_field_name( 'excerpt' ),
									'id' => $this->get_layers_field_id( 'excerpt' ),
									'placeholder' => __( 'Short Excerpt' , 'layerswp' ),
									'value' => ( isset( $widget['excerpt'] ) ) ? $widget['excerpt'] : NULL ,
									'class' => 'layers-form-item layers-textarea',
									'rows' => 6
								)
							); ?>
						</p>
						<div class="layers-row">
							<p class="layers-form-item layers-column layers-span-6">
								<label for="<?php echo $this->get_layers_field_id( 'link_text' ); ?>"><?php _e( 'Button Link' , 'layerswp' ); ?></label>
								<?php echo $this->form_elements()->input(
									array(
										'type' => 'text',
										'name' => $this->get_layers_field_name( 'link' ),
										'id' => $this->get_layers_field_id( 'link' ),
										'placeholder' => __( 'http://' , 'layerswp' ),
										'value' => ( isset( $widget['link'] ) ) ? $widget['link'] : NULL ,
										'class' => 'layers-text',
									)
								); ?>
							</p>
							<p class="layers-form-item layers-column layers-span-6">
								<label for="<?php echo $this->get_layers_field_id( 'link_text' ); ?>"><?php _e( 'Button Text' , 'layerswp' ); ?></label>
								<?php echo $this->form_elements()->input(
									array(
										'type' => 'text',
										'name' => $this->get_layers_field_name( 'link_text' ),
										'id' => $this->get_layers_field_id( 'link_text' ),
										'placeholder' => __( 'e.g. "Read More"' , 'layerswp' ),
										'value' => ( isset( $widget['link_text'] ) ) ? $widget['link_text'] : NULL ,
									)
								); ?>
							</p>
						</div>
					</div>
				</section>
			</li>
			<?php
		}

	} // Class

	// Add our function to the widgets_init hook.
	 register_widget("Layers_Content_Widget");
}