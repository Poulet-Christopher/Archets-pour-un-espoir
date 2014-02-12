<?php 

	$lieu = get_post_meta(get_the_ID(), 'wpcf-lieu', true);
	$musiciens = get_post_meta(get_the_ID(), 'wpcf-musiciens', true);
	$beneficiaire = get_post_meta(get_the_ID(), 'wpcf-beneficiaire', true);
	$recette = get_post_meta(get_the_ID(), 'wpcf-recette', true);



?>


	<div class="meta">
		<p>Information complementaires:</p>
		<?php 
			if(!empty($lieu)){
				echo'<span>';
					echo 'lieu: '.$lieu.'<br>';
				echo'</span>';
			}
			if(!empty($musiciens)){
				echo'<span>';
					echo 'musiciens: '.$musiciens.'<br>';
				echo'</span>';
			}
			if(!empty($beneficiaire)){
				echo'<span>';
					echo 'beneficiaire: '.$beneficiaire.'<br>';
				echo'</span>';
			}
			if(!empty($musiciens)){
				echo'<span>';
					echo 'recette: '.$recette.'<br>';
				echo'</span>';
			}


		?>
	</div>
