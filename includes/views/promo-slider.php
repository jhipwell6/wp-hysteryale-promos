<div id="hyg-promo-slider" class="carousel slide" data-ride="carousel">
	<div class="carousel-inner">
		<?php 
			$i = 0;
			foreach ( $promos as $Promo ) :
				if ( ! $Promo->has_image() ) {
					continue;
				}
				$active = $i == 0 ? ' active' : '';
		?>
		<div class="carousel-item<?php echo $active; ?>">
			<a href="<?php echo esc_url( $Promo->get_url() ); ?>"><img src="<?php echo esc_url( $Promo->get_image() ); ?>" class="d-block w-100" alt="<?php echo esc_attr( $Promo->get_title() ); ?> Promo Banner"></a>
		</div>
		<?php $i++; endforeach; ?>
	</div>
	<ol class="carousel-indicators">
		<?php
			$i = 0;
			foreach ( $promos as $Promo ) :
				if ( ! $Promo->has_image() ) {
					continue;
				}
				$active = $i == 0 ? ' class="active"' : '';
		?>
		<li data-target="#hyg-promo-slider" data-slide-to="<?php echo $i; ?>"<?php echo $active; ?>></li>
		<?php $i++; endforeach; ?>
	</ol>
</div>