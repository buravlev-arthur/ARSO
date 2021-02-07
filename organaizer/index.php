<div id="content_personal_block"></div>
<?php } ?>
<script type="text/javascript">
	$(function(){
		$('#content_personal_block').load("personal/kal_week.php?where=<?= $monday ?>");
		$("#per_date_m_ls").click(function(){$('#content_personal_block').load("personal/ls.php")});
		$("#per_date_m_kal").click(function(){$('#content_personal_block').load("personal/kal_week.php?where=<?= $monday ?>")});
		$("#per_date_m_doc").click(function(){$('#content_personal_block').load("personal/doc.php");});
	});
	function get_kalendar(where,page){
		$('#content_personal_block').load("personal/"+page+".php?where="+where);
	}

</script>