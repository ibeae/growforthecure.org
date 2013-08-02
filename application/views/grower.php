<div class="inner">

<h1><?php echo $full_name; ?> - Member of Team <?php echo $team_name ?></h1>

<?php if($campaign_remaining < 1) { ?>
<div class="grow-status">The Grow is over! Congratulations!</div>
<?php } ?>

<br clear="all" />

<div class="sidebar">
<?php 
	if ($profile_pic) {
		$photo = '<img src="' . base_url() . 'userphotos/' . str_replace('.', '_thumb.', $profile_pic) . '" width="200px" />';
	} else {
		$photo = '<img src="http://placehold.it/200x300&text=No+Profile+Photo" />';
	}
 ?>

	<?php echo $photo; ?>
</div>


<div class="main-content">


<?php if (isset($badges)) { ?>
	<div class="new-section">
		<p class="sub-head">Badges Earned.</p>
		<?php foreach ($badges as $badge) {
			if ($badge->badgeID == 1){
				$icon = 'New In Town Badge';
			} elseif ($badge->badgeID == 2) {
				$icon = 'First Donation is Free Badge';
			} elseif ($badge->badgeID == 3) {
				$icon = 'No Man Left Behind Badge';
			} elseif ($badge->badgeID == 4) {
				$icon = 'Band of Brothers Badge';
			}
			echo $icon . '</br />';
		} ?>
	</div>
<?php } ?>

<?php if (isset($styles_choices)) { ?>
	<div class="new-section">
		<p class="sub-head">Pledge your support to <?php echo $full_name; ?>'s grow.</p>
		<p>Here you can see the styles <?php echo $full_name; ?> is willing to shave his beard to. You can also see which style is winning in pledges, if any. Make a donation of ANY amount to your favorite style by clicking on an icon below.</p>
		<br />
		<div class="image-row">
		<?php 		
				foreach ($styles_choices as $choice) {

					$paypalLink = 'https://www.sandbox.paypal.com/cgi-bin/webscr?business=paypal@lungcancerfoundation.org
						&amp;cmd=_donations&rm=2
						&amp;item_name=Support+' . urlencode($full_name) . '+of+Growforthecure.org
						&amp;item_number=Grow+Style+' . urlencode($choice->styleName) . '&amp;currency_code=USD
						&amp;notify_url=http%3A%2F%2Fcreatedbysteve.com%2fgrowforthecure%2Fnotify&custom=' . $user_id . '-' . $campaign_id . '-' . $choice->StyleID . '-' . urlencode($full_name) . '
						&amp;return=http%3A%2F%2Fcreatedbysteve.com%2fgrowforthecure%2Fthanks&custom=' . $user_id . '-' . $campaign_id . '-'.$choice->StyleID. '-' . urlencode($full_name);

					if (isset($choice->PledgeAmount)) {
						$PA = '<span class="pledgeamount">$' . $choice->PledgeAmount . '</span>';
					} else {
						$PA = '';
					}
					echo '<div class="icon"><a target="_blank" href="' . $paypalLink . '"><img width="60px" id="' . $choice->StyleID . '" alt="' . $choice->styleName . '" title="' . $choice->styleName . '" src="' . base_url() .'artwork/styles/'. $choice->fileName . '" />'  . $PA .'</a></div>';

				}
		 ?>
		</div>
		<br clear="all" />
		<br clear="all" />

	</div>

<?php } ?>


<?php if (isset($comments)) { ?>
	<div class="new-section">
		<p class="sub-head">Some of <?php echo $full_name; ?>'s generous supporters wishing him the best.</p>
		<table class="comment-table">
			<?php foreach ($comments as $comment) { ?>
				<tr>
					<td width="100px"><?php echo $comment->commenterName; ?></td>
					<td><?php echo $comment->comment; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
			<br clear="all" />
		<br clear="all" />

<?php } ?>


<?php if($campaign_remaining > 0) { ?>
	<div class="new-section">

		<?php 
			if ($campaign_remaining > 1) {
				$timeFrame = $campaign_remaining . ' Days';
			} else {
				$timeFrame = $campaign_remaining . ' Day';
			}
		 ?>

		<p class="sub-head">Only <?php echo $timeFrame; ?> Left in the Grow.</p>
<!-- 	
		<p>Total Days: <?php echo $campaign_full; ?></p>
		<p>Days Passed: <?php echo $campaign_elapsed; ?></p>
		<p>Days Remaining: <?php echo $campaign_remaining; ?></p>
 -->
		<?php 
			$complete = round((1 - ($campaign_remaining / $campaign_full)) * 100);
//			echo $complete;
		 ?>

		<div id="container" style="width:100%; height:20px; border:1px solid lightgrey;border-radius:50px;overflow:hidden;background-color:lightgrey;">
				<div id="progress-bar" style="width:<?php echo $complete; ?>%;background-color:red;height:50px;"></div>
		</div>

	</div>

		<br clear="all" />
		<br clear="all" />

<?php } ?>	

<?php if (isset($statement) && strlen($statement) > 0) { ?>
	<div class="new-section">
		<p class="sub-head">Personal Statement.</p>
		<p><?php echo $statement; ?></p>
	</div>
			<br clear="all" />
		<br clear="all" />

<?php } ?>

<?php if (isset($photos)) { ?>
	<div class="new-section">
		<p class="sub-head">See All of <?php echo $full_name; ?>'s Current Photos</p>
		<?php 
				foreach ($photos as $photo) {
					echo '<img style="padding-right:10px;" src="' . base_url() . 'userphotos/' . $photo->photoImage . '" width="100px">';
				}
		?>

	</div>
			<br clear="all" />
		<br clear="all" />

<?php } ?>



	<div class="new-section">
		<p class="sub-head">See All Members of Team <?php echo $team_name ?></p>
		
		<?php 

		foreach ($teammembers as $member) {

			if ($member->profilePic) {
				$photo = '<img src="' . base_url() . 'userphotos/' . str_replace('.', '_thumb.', $member->profilePic) . '" width="100px" />';
			} else {
				$photo = '<img src="http://placehold.it/100x100&text=NoProfilePhoto" />';
			}

			?>
			<div class="members">
				<p><a href="<?php echo base_url(); ?>grower/<?php echo  strtolower($member->firstName) . '-' . strtolower($member->lastName) ?>/<?php echo $member->userID; ?>"><?php echo $member->firstName . ' ' . $member->lastName; ?></a></p>
				<?php echo $photo; ?>
			</div>

			<?php } ?>
	</div>

<br clear="all" />
<br clear="all" />

</div>



</div>