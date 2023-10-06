<table class="form-table">
	<tr valign="top">
        <td colspan="2">
			<a href="https://hygmarketing.wpengine.com/dealer-promotions/" class="button button-hero" target="_blank">Select Promos <span class="dashicons dashicons-external" style="vertical-align: middle;"></span></a>
        </td>
    </tr>
	<tr valign="top">
        <th scope="row">
            <label>Available Promos</label><br /><br />
			<a href="<?php echo add_query_arg( 'force_update', 1 ); ?>" class="button button-secondary">Refresh</a>
        </th>
        <td>
			<?php if ( ! empty( $available_promos ) ) : ?>
			<table class="wp-list-table widefat fixed striped posts" id="hyg_promos_promo__available_promos" style="max-width: 800px;">
				<thead>
					<tr>
						<th scope="col" style="padding: 8px 10px; width: 5%;">ID</th>
						<th scope="col" style="padding: 8px 10px; width: 10%">Status</th>
						<th scope="col" style="padding: 8px 10px; width: 45%">Title</th>
						<th scope="col" style="padding: 8px 10px; width: 10%">Type</th>
						<th scope="col" style="padding: 8px 10px; width: 15%">Expiration</th>
						<th scope="col" style="padding: 8px 10px; width: 15%; text-align: right;">Preview</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ( $available_promos as $promo ) :
							$active = in_array( $promo['id'], $active_promo_ids ) ? 'Active' : '-';
					?>
					<tr>
						<td><?php echo $promo['id']; ?></td>
						<td><?php echo $active; ?></td>
						<td><?php echo $promo['title']; ?><br /><small><?php echo $promo['description']; ?></small></td>
						<td><?php echo array_first( $promo['types'] )['name']; ?></td>
						<td><?php echo $promo['expiration'] ? date( 'M d, Y', strtotime( $promo['expiration'] ) ) : 'N/A'; ?></td>
						<td style="text-align: right;"><a href="<?php echo esc_url( $promo['preview_url'] ); ?>" target="_blank">Preview <span class="dashicons dashicons-external"></span></a></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
        </td>
    </tr>
</table>
