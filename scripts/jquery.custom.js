$(document).ready(function(){

	function updateStyles(styleID, campaignID, userID, insdelYN)
	{
		$.ajax({
		  type: "POST",
//		  url: "../styleupdate",
		  url: "http://createdbysteve.com/growforthecure/styleupdate",
		  data: { style: styleID, campaign: campaignID, user: userID, idyn: insdelYN }
		}).done(function( msg ) {
			if (insdelYN == "del") {
				console.log( 'Deleted: ' + msg );
			} else {
				console.log( 'Added: ' + msg );
			}
		  
		});
	}

//	$('div.image-row div.icon').click(updateStyles(styleID, userID));


	$('div.icon').on('click', function(){
		if ($(this).hasClass('styleBG')) {
			if ($(this).hasClass('pledged')) {
				alert('Sorry, you can not remove a style that has pledges.');
			} else {
				insdel = "del";
				cid = $('input[name="campaignID"]').val();
				uid = $('input[name="userID"]').val();
				sid = $(this).children('img').attr('id');
				$(this).removeClass('styleBG');
				updateStyles(sid, cid, uid, insdel);	
			}

		} else {
			insdel = "ins";
			cid = $('input[name="campaignID"]').val();
			uid = $('input[name="userID"]').val();
			sid = $(this).children('img').attr('id');
			$(this).addClass('styleBG');
			updateStyles(sid, cid, uid, insdel);	
		}




	});







});