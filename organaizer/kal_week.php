<div id="kalendar_block">
		<?php 
			$wday=Date("N");
			$monday=time()-(($wday-1)*86400);
		?>
		<div id="kal_head_month">
		<div id="kal_weeks_buttons">
			<div id="week_back" onclick="get_kalendar(<?= ($_GET["where"]-604800) ?>,'kal_week')"><������� ������</div>
			<div id="week_next" onclick="get_kalendar(<?= ($_GET["where"]+604800) ?>,'kal_week')">��������� ������></div>
		</div>
			<div id="kal_variants">
				<div class="kal_variants_buttons" onclick="get_kalendar(<?= time() ?>,'kal_today')" id="k_v_b_day">�������</div>
				<div class="kal_variants_buttons" onclick="get_kalendar(<?= $monday ?>,'kal_week')" id="k_v_b_day">������</div>
				<div class="kal_variants_buttons" id="k_v_b_day">�����</div>
			</div>
<?php
			switch (Date("m",$_GET["where"])){
				case 1:$rus_month="������"; break;
				case 2:$rus_month="�������"; break;
				case 3:$rus_month="����"; break;
				case 4:$rus_month="������"; break;
				case 5:$rus_month="���"; break;
				case 6:$rus_month="����"; break;
				case 7:$rus_month="����"; break;
				case 8:$rus_month="������"; break;
				case 9:$rus_month="��������"; break;
				case 10:$rus_month="�������"; break;
				case 11:$rus_month="������"; break;
				case 12:$rus_month="�������";
			}
			echo "<div id='name_month'>".$rus_month."</div>";
			echo "</div>";
			//��������� ����
				$rus_days=array('�����������','�������','�����','�������','�������','�������','�����������');
				echo "<div id='kal_week_days_head'>";
				echo "<div class='hour_head'>�����</div>";
				$day_for_show_week=$_GET['where'];
				$today=99;
				for ($i=0;$i<=6;$i++){
					echo "<div class='day_of_week_head'>".$rus_days[$i]." ".Date('d',$day_for_show_week)."/".Date("m",$day_for_show_week)."</div>";
					if (Date("d",$day_for_show_week)==Date("d",time()))
						$today=$i;
					$day_for_show_week+=86400;
				}				
				echo "</div>";
				//��� ������
				echo "<div id='kal_week_days_body'>";
				echo "<div id='kal_hours_numbers'>";
				for ($j=0;$j<=23;$j++){
					echo "<div id='kal_time_block_head_$j' class='kal_time_block_head'>$j:00</div>";
				}
				echo "</div>";
				for ($i=0;$i<=6;$i++){
					$h=true;
					echo "<div class='day_of_week' id='day_of_week_$i'";
					if ($i==$today) echo "style='background:#f1f1f1' ";
					echo ">";
					for ($j=0;$j<=23.5;$j+=0.5){
						echo "<div id='kal_time_block_$j' class='kal_time_block_";
						echo $h?1:2;
						echo "'></div>";
						$h?$h=false:$h=true;
					}
					echo "</div>";
				}
				echo "</div>";
?>
<script type="text/javascript">
jQuery(function()
    {
        jQuery('#kal_week_days_body').jScrollPane();
		api = $('#kal_week_days_body').data('jsp');
		<?php  
			$now_time=(Date("G")*35)-35;
		?>
		api.scrollToY(<?= $now_time ?>,0);
		$("#kal_time_block_head_<?=Date('G')-1?>").append("<div id='time_cursor'></div>");
    });
</script>
</div>