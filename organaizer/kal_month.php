<?php
	session_start();
	header('Content-type: text/html; charset=utf-8');
	$wday=Date("N");
	$monday=time()-(($wday-1)*86400);
?>		
		<div id="kal_head_month">
		<div id="kal_weeks_buttons">
			<div id="week_back" onclick="get_kalendar(<?= ($_GET["where"]-86400) ?>,'kal_today')"><прошлый день</div>
			<div id="week_next" onclick="get_kalendar(<?= ($_GET["where"]+86400) ?>,'kal_today')">следующий день></div>
		</div>
			<div id="kal_variants">
				<div class="kal_variants_buttons" onclick="get_kalendar(<?= time() ?>,'kal_today')" id="k_v_b_day">сегодня</div>
				<div class="kal_variants_buttons" onclick="get_kalendar(<?= $monday ?>,'kal_week')" id="k_v_b_day">неделя</div>
				<div class="kal_variants_buttons" id="k_v_b_day">месяц</div>
			</div>
<?php
			switch (Date("m",$_GET["where"])){
				case 1:$rus_month="январь"; break;
				case 2:$rus_month="февраль"; break;
				case 3:$rus_month="март"; break;
				case 4:$rus_month="апрель"; break;
				case 5:$rus_month="май"; break;
				case 6:$rus_month="июнь"; break;
				case 7:$rus_month="июль"; break;
				case 8:$rus_month="август"; break;
				case 9:$rus_month="сентябрь"; break;
				case 10:$rus_month="октябрь"; break;
				case 11:$rus_month="ноябрь"; break;
				case 12:$rus_month="декабрь";
			}
			
			echo "<div id='name_month'>".$rus_month."</div>";
			echo "</div>";
			//заголовки дней
				$rus_days=array('','понедельник','вторник','среда','четверг','пятница','суббота','воскресенье');
				echo "<div id='kal_week_days_head'>";
				echo "<div class='hour_head'>время</div>";
				echo "<div class='day_of_today_head'>".$rus_days[Date('N',$_GET["where"])]." ".Date('d',$_GET["where"])."/".Date("m",$_GET["where"])."</div>";				
				echo "</div>";
				//сам список
				echo "<div id='kal_week_days_body'>";
				echo "<div id='kal_hours_numbers'>";
				for ($j=0;$j<=23;$j++){
					echo "<div class='kal_time_block_head'>$j:00</div>";
				}
				echo "</div>";

					$h=true;
					echo "<div class='day_of_today'>";
					for ($j=0;$j<=23.5;$j+=0.5){
						echo "<div id='kal_time_block_$j' class='kal_time_block_";
						echo $h?1:2;
						echo "'></div>";
						$h?$h=false:$h=true;
					}
					echo "</div>";
				echo "</div>";
?>
<script type="text/javascript">
jQuery(function()
    {
        jQuery('#kal_week_days_body').jScrollPane();
		api = $('#kal_week_days_body').data('jsp');
		<?php  
			$now_time=(Date("H")*8);
		?>
		api.positionDragY(<?= $now_time ?>,0);
    });
</script>