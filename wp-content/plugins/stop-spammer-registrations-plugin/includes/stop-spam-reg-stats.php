<?php
/*
	Stop Spammers Plugin 
	History and Stats Page
*/
if (!defined('ABSPATH')) exit; // just in case

	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	$stats=kpg_sp_get_stats();
	//echo "<!--";
	//print_r($stats);
	//echo "-->";
	extract($stats);
	$options=kpg_sp_get_options();
	extract($options);
	$nonce='';
	$trash = plugins_url( 'includes/trashcan.png', dirname(__FILE__) );
	$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
    // update checks
	if (array_key_exists('kpg_stop_spammers_control',$_POST)) $nonce=$_POST['kpg_stop_spammers_control'];
	if (wp_verify_nonce($nonce,'kpgstopspam_update')) { 
		if (array_key_exists('kpg_stop_clear_cache',$_POST)) {
			// clear the cache
			$badems=array();
			$badips=array();
			$goodips=array();
			$stats['badems']=$badems;
			$stats['badips']=$badips;
			$stats['goodips']=$goodips;
			update_option('kpg_stop_sp_reg_stats',$stats);
			echo "<h2>Cache Cleared</h2>";
			
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Cache Cleared"."\r\n");
		}
		if (array_key_exists('kpg_stop_clear_wl',$_POST)) {
			// clear the cache
			$wlreq=array();
			$stats['wlreq']=$wlreq;
			update_option('kpg_stop_sp_reg_stats',$stats);
			echo "<h2>White List Requests Cleared</h2>";
			
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: White List Requests Cleared"."\r\n");
		}
		if (array_key_exists('kpg_stop_clear_hist',$_POST)) {
			// clear the cache
			$hist=array();
			$spcount=0;
			$stats['hist']=$hist;
			$stats['spcount']=$spcount;
			update_option('kpg_stop_sp_reg_stats',$stats);
			echo "<h2>History Cleared</h2>";
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: History Cleared"."\r\n");
		}	
		if (array_key_exists('kpg_stop_clear_reason',$_POST)) {
			$stats['cntjscript']=0;
			$stats['cntsfs']=0;
			$stats['cntreferer']=0;
			$stats['cntdisp']=0;
			$stats['cntrh']=0;

			$stats['cntdnsbl']=0;
			$stats['cntubiquity']=0;
			$stats['cntakismet']=0;
			$stats['cntspamwords']=0;
			$stats['cntsession']=0;

			$stats['cntlong']=0;
			$stats['cntagent']=0;
			$stats['cnttld']=0;
			$stats['cntemdom']=0;			
			$stats['cntcacheip']=0;
			
			$stats['cntcacheem']=0;
			$stats['cnthp']=0;
			$stats['cntbotscout']=0;
			$stats['cntblem']=0;
			$stats['cntlongauth']=0;
					
			$stats['cntblip']=0;
			$stats['cntaccept']=0;
			$stats['cntpassed']=0;
			$stats['cntwhite']=0;
			$stats['cntgood']=0;
					
			update_option('kpg_stop_sp_reg_stats',$stats);
			extract($stats);
			echo "<h2>History Cleared</h2>";
			if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Reasons Cleared"."\r\n");
		}
		if (array_key_exists('kpg_stop_add_black_list',$_POST)) {
			$bbbb=$_POST['kpg_stop_add_black_list'];
			if (!in_array($bbbb,$blist)&&!in_array($bbbb,$wlist)) {
				$blist[]=$bbbb;
				$options['blist']=$blist;
				update_option('kpg_stop_sp_reg_options',$options);
				echo "<h2>$bbbb Added to Black List</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $bbbb Added to Black List"."\r\n");
			}
		}
		if (array_key_exists('kpg_stop_del_black_list',$_POST)) {
			$bbbb=$_POST['kpg_stop_del_black_list'];
			if (array_key_exists($bbbb,$badips)) {
				unset($badips[$bbbb]);
				$stats['badips']=$badips;
				update_option('kpg_stop_sp_reg_stats',$stats);
				echo "<h2>$bbbb Removed from cache</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $bbbb Removed from cache"."\r\n");
			} else if (array_key_exists($bbbb,$badems)) {
				unset($badems[$bbbb]);
				$stats['badems']=$badems;
				update_option('kpg_stop_sp_reg_stats',$stats);
				echo "<h2>$bbbb Removed from cache</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $bbbb Removed from cache"."\r\n");
			} else if (array_key_exists($bbbb,$goodips)) {
				unset($goodips[$bbbb]);
				$stats['goodips']=$goodips;
				update_option('kpg_stop_sp_reg_stats',$stats);
				echo "<h2>$bbbb Removed from cache</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $bbbb Removed from cache"."\r\n");
			}
			
		}
		if (array_key_exists('kpg_stop_add_white_list',$_POST)) {
			$bb=$_POST['kpg_stop_add_white_list'];
			if (!in_array($bb,$wlist)) {
				$wlist[]=$bb;
				$options['wlist']=$wlist;
				update_option('kpg_stop_sp_reg_options',$options);
				echo "<h2>$bb Added to White List</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $bb Added to White List.\r\n");
			}
		}
		if (array_key_exists('kpg_stop_delete_req',$_POST)) {
			$bb=$_POST['kpg_stop_delete_req'];
			// $wlreq is an array with arrays ip is element[1]
			for( $j=0;$j<count($wlreq);$j++){
				$wlip=$wlreq[$j][1];
				if ($wlip==$bb) {
					unset($wlreq[$j]);
					$stats['wlreq']=$wlreq;
					update_option('kpg_stop_sp_reg_stats',$stats);
					echo "<h2>$wlip Removed from White List Requests</h2>";
					if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: $wlip Removed from White List Requests"."\r\n");
				}
			}
			
		}
		if (array_key_exists('kpg_stop_delete_log',$_POST)) {
			// clear the cache
			$f=dirname(__FILE__)."/../sfs_debug_output.txt";
			if (file_exists($f)) {
			    unlink($f);
				echo "<h2>Deleted Error Log File</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Deleted Error Log File.\r\n");
			}
		}
		if (array_key_exists('kpg_stop_history_log',$_POST)) {
			// clear the cache
				kpg_file_delete('.history_log.txt');
				echo "<h2>Deleted Error Log File</h2>";
				if ($logfilesize>0) kpg_append_file('.history_log.txt',"$now: Deleted History Log File.\r\n");
		}
}
$me=admin_url('options-general.php?page=stopspammersoptions');
$sme=admin_url('options-general.php?page=stopspammerstats');
if (function_exists('is_multisite') && is_multisite() && $muswitch=='Y') {
	switch_to_blog(1);
	$me=get_admin_url( 1,'network/settings.php?page=adminstopspammersoptions');
	$sme=get_admin_url( 1,'network/settings.php?page=adminstopspammerstats');
	restore_current_blog();
}
	$nonce=wp_create_nonce('kpgstopspam_update');

?>
<div class="wrap">
	<h2>Stop Spammers Plugin Stats Version 4.3</h2>
	<p><a href="<?php echo $sme; ?>">View History</a> - <a href="<?php echo $me; ?>">View Options</a> </p>
<?php
	if (count($wlreq)==1) {
		echo "<p><a style=\"font-style:italic;\" href=\"#wlreq\">".count($wlreq)." user</a> has been denied access and requested that you add them to the white list";
		echo"</p>";
	} else if (count($wlreq)>0) {
		echo "<p><a style=\"font-style:italic;\" href=\"#wlreq\">".count($wlreq)." users</a> have been denied access and requested that you add them to the white list";
		echo"</p>";
	}
?>	
	
	
	<hr/>

<?php
    if (!empty($_GET) && array_key_exists('v',$_GET) && wp_verify_nonce($_GET['v'],'kpgstopspam_fileview')) {
		// display the file
?>

	<h3>History Log (located in content directory)</h3>
	<form method="post" action="<?php echo $sme; ?>">
		<input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
		<input type="hidden" name="kpg_stop_history_log" value="true" />
		<p class="submit">
			<input  class="button-primary"  value="Delete History Log File" type="submit" />
		</p>
	</form>

		<pre>		
		<?php echo "\r\n".kpg_read_file('.history_log.txt'); ?>
		
		</pre>
<?php	
	} else {
	

?>

  <form action="" method="post" name="kpg_ssp_bl" id="kpg_ssp_bl">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_add_black_list" value="" />
  </form>
  <form action="" method="post" name="kpg_ssp_del" id="kpg_ssp_del">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_del_black_list" value="" />
  </form>
  <form action="" method="post" name="kpg_ssp_wl" id="kpg_ssp_wl">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_add_white_list" value="" />
  </form>
  <form action="" method="post" name="kpg_ssp_req_del" id="kpg_ssp_req_del">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_delete_req" value="" />
  </form>
  <script type="text/javascript" >
function addblack(ip) {
	document.kpg_ssp_bl.kpg_stop_add_black_list.value=ip;
	document.kpg_ssp_bl.submit();
	return false;
}
function delblack(ip) {
	document.kpg_ssp_del.kpg_stop_del_black_list.value=ip;
	document.kpg_ssp_del.submit();
	return false;
}

function addwhite(ip) {
	document.kpg_ssp_wl.kpg_stop_add_white_list.value=ip;
	document.kpg_ssp_wl.submit();
	return false;
}
function delreq(ip) {
	document.kpg_ssp_req_del.kpg_stop_delete_req.value=ip;
	document.kpg_ssp_req_del.submit();
	return false;
}
</script>
<?php
	if ($spmcount>0) {
?>
<h3>Stop Spammers has stopped <?php echo $spmcount; ?> spammers since <?php echo $spmdate; ?>.</h3>
  <?php 
}
	if ($spcount>0) {
?>
  <h3>Stop Spammers has stopped <?php echo $spcount; ?> spammers since <?php echo $spdate; ?>.</h3>
  <?php 
	}
	$num_comm = wp_count_comments( );
	$num = number_format_i18n($num_comm->spam);
	if ($num_comm->spam>0) {	
?>
  <p>There are <a href='edit-comments.php?comment_status=spam'><?php echo $num; ?></a> spam comments waiting for you to report them</p>
  <?php 
	}
	$num_comm = wp_count_comments( );
	$num = number_format_i18n($num_comm->moderated);
	if ($num_comm->moderated>0) {	
?>
  <p>There are <a href='edit-comments.php?comment_status=moderated'><?php echo $num; ?></a> comments waiting to be moderated</p>
  <?php 
	}
	

?>
  
    <hr/>
  <h3>Spam Reasons</h3>
   <form method="post" action="">
     <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_clear_reason" value="true" />
    <p class="submit">
    <input  class="button-primary" value="Clear Reason Summary" type="submit" />
	</p>
  </form>
  <style type="text/css">
	#reasontab {
		background-color:#eeeeee;
	}
	#reasontab tr {
		background-color:#ffffff;
	}
	#reasontab td {
		text-align:right;
	}
  </style>
  <table width="100%" cellpadding="2" cellspacing="2"  id="reasontab">
    <tr>
      <td>Javascript Trap</td>
      <td><?php echo $cntjscript; ?></td>
      <td>SFS database</td>
      <td><?php echo $cntsfs; ?></td>
      <td>HTTP_REFERER</td>
      <td><?php echo $cntreferer; ?></td>
      <td>Disposable email</td>
       <td><?php echo $cntdisp; ?></td>
      <td>Red Herring</td>
      <td><?php echo $cntrh; ?></td>
    </tr>
    <tr>
      <td>DSNBL database</td>
      <td><?php echo $cntdnsbl; ?></td>
      <td>Ubiquity Servers</td>
      <td><?php echo $cntubiquity; ?></td>
      <td>Akismet</td>
      <td><?php echo $cntakismet; ?></td>
       <td>Spam Words</td>
      <td><?php echo $cntspamwords; ?></td>
      <td>Session speed</td>
      <td><?php echo $cntsession; ?></td>
    </tr>
    <tr>
      <td>Long email</td>
      <td><?php echo $cntlong; ?></td>
      <td>Missing Agent</td>
      <td><?php echo $cntagent; ?></td>
      <td>Blocked TLD</td>
      <td><?php echo $cnttld; ?></td>
      <td>Blocked Email Domain</td>
      <td><?php echo $cntemdom; ?></td>
      <td>Cached Bad IP</td>
      <td><?php echo $cntcacheip; ?></td>
    </tr>
    <tr>
      <td>Cached Bad Email</td>
      <td><?php echo $cntcacheem; ?></td>
      <td>Project Honeypot</td>
      <td><?php echo $cnthp; ?></td>
      <td>Botscout</td>
      <td><?php echo $cntbotscout; ?></td>
      <td>Black List Email</td>
      <td><?php echo $cntblem; ?></td>
      <td>Long Author Name</td>
      <td><?php echo $cntlongauth; ?></td>
    </tr>
    <tr>
      <td>Black List IP</td>
      <td><?php echo $cntblip; ?></td>
      <td>Bad Accept Header</td>
      <td><?php echo $cntaccept; ?></td>
      <td>Passed</td>
      <td><?php echo $cntpassed; ?></td>
      <td>White List</td>
      <td><?php echo $cntwhite; ?></td>
      <td>In Good Cache</td>
      <td><?php echo $cntgood; ?></td>
    </tr>
  </table>
  <hr/>

  
  <?php
	if (count($hist)==0) {
		echo "<p>No Activity Recorded.</p>";
	} else {
  ?>
  <h3>Recent Activity</h3>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_clear_hist" value="true" />
    <p class="submit">
    <input  class="button-primary" value="Clear Recent Activity" type="submit" />
	</p>
  </form>
  </p>
  <table style="background-color:#eeeeee;" cellspacing="2">
    <tr style="background-color:ivory;text-align:center;">
      <td>date/time</td>
      <td>email</td>
      <td>IP</td>
      <td>author, user/pwd</td>
      <td>script</td>
      <td>reason
        <?php
	if (function_exists('is_multisite') && is_multisite()) {
?>
      </td>
      <td>blog</td>
      <?php
}
?>
    </tr>
    <?php
		foreach($hist as $key=>$data) {
			//$hist[$now]=array($ip,$email,$author,$sname,'begin');
			$em=strip_tags(trim($data[1]));
			$dt=strip_tags($key);
			$ip=$data[0];
			$au=strip_tags($data[2]);
			$id=strip_tags($data[3]);
			if (empty($au)) $au=' -- ';
			if (empty($em)) $em=' -- ';
			$reason=$data[4];
			$blog=1;
			if (count($data)>5) $blog=$data[5];
			if (empty($blog)) $blog=1;
			if(empty($reason)) 
				$reason="passed";
				
			echo "<tr style=\"background-color:white;\">
				<td style=\"font-size:.8em;padding:2px;\">$dt</td>
				<td style=\"font-size:.8em;padding:2px;\">$em</td>
				<td style=\"font-size:.8em;padding:2px;\">$ip";
		    if (strpos($reason,'passed')!==false && ($id=='/'||strpos($id,'login')!==false) && !in_array($ip,$blist) && !in_array($ip,$wlist)) {
				$skull = plugins_url( 'includes/sk.jpg', dirname(__FILE__) );
				echo "<a href=\"\" onclick=\"return addblack('$ip');\" title=\"Add to Black List\" alt=\"Add to Black List\" ><img src=\"$skull\" width=\"12px\" /></a>";
			}
			echo "</td><td style=\"font-size:.8em;padding:2px;\">$au</td>
				<td style=\"font-size:.8em;padding:2px;\">$id</td>
				<td style=\"font-size:.8em;padding:2px;\">$reason</td>";
			if (function_exists('is_multisite') && is_multisite()) {
				// switch to blog and back
				switch_to_blog($blog);
				$num_comm = wp_count_comments( );
				restore_current_blog();
				$snum = number_format_i18n($num_comm->spam);
				$mnum = number_format_i18n($num_comm->moderated );
				$anum = number_format_i18n($num_comm->total_comments);

				$blogname=get_blog_option( $blog, 'blogname' );
				$blogadmin=esc_url( get_admin_url($blog) );
				if (substr($blogadmin,strlen($blogadmin)-1)=='/') $blogadmin=substr($blogadmin,0,strlen($blogadmin)-1);
				echo "<td style=\"font-size:.8em;padding:2px;\" align=\"center\">";
				echo "$blogname: c<a href=\"$blogadmin/edit-comments.php/\">($anum)</a>,&nbsp; 
				p<a href=\"$blogadmin/edit-comments.php?comment_status=moderated\">($mnum)</a>,&nbsp; 
				s<a href=\"$blogadmin/edit-comments.php?comment_status=spam\">($snum)</a>";
				echo "</td>";
			}
			echo "</tr>";
		}
	?>
  </table>
  <?php
		
	
   }
   if (count($wlreq)==0) {
   		// maybe say something
	} else {
		// show white list request
?>
<hr/>
<a name="wlreq" a></a>
  <h3>White List Requests</h3>
  <p>Users who have been blocked are requesting to be white listed.</p>
  <table style="background-color:#eeeeee;" cellspacing="2">
    <tr style="background-color:ivory;text-align:center;">
		<td>Time</td>
		<td>IP</td>
		<td>Email</td>
		<td>Author/Login</td>
		<td>Reason</td>
		<td>Request</td>
		<td>Action</td>
	</tr>

<?php
	foreach ($wlreq as $wl) {
	    for ($j=0;$j<count($wl);$j++) {
			$wl[$j]=sanitize_text_field($wl[$j]);
		}
		if (count($wl)<6) $wl[5]='';
		?>	
		<tr style="background-color:white;">
			<td><?php echo $wl[0];?></td>
			<td><?php echo $wl[1];?></td>
			<td><?php echo $wl[2];?></td>
			<td><?php echo $wl[3];?></td>
			<td><?php echo $wl[4];?></td>
			<td><?php echo $wl[5];?></td>
			<td><a href='' onclick="return addwhite('<?php echo $wl[1]; ?>');" title="Add to White List" alt="Add to White List">Add</a>, <a title="Check Stop Forum Spam (SFS)" target="_stopspam" href="http://www.stopforumspam.com/search.php?q=<?php echo $wl[1]; ?>\">Check SFS</a>, <a href='' onclick="return delreq('<?php echo $wl[1]; ?>');" title="Delete Request" alt="Delete Request">Delete</a></td>
		</tr>
		<?php		
	}
	?>
</table>
  <table>
    <tr>
      <td><form method="post" action="">
          <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
          <input type="hidden" name="kpg_stop_clear_wl" value="true" />
    <span class="submit">
          <input  class="button-primary" value="Clear the White List Requests" type="submit" />
	</span>
        </form></td>
    </tr>
	</table>

<?php	
	}
// end of white list
   if (count($badems)==0&&count($badips)==0&&count($goodips)==0) {
?>
  <p>Nothing in the cache.</p>
  <?php
   } else {
?>
<hr/>
  <h3>Cached Values</h3>
  <table>
    <tr>
      <td><form method="post" action="">
          <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
          <input type="hidden" name="kpg_stop_clear_cache" value="true" />
    <p class="submit">
          <input  class="button-primary" value="Clear the Cache" type="submit" />
	</p>
        </form></td>
    </tr>
  </table>
  <table>
    <tr>
      <?php
		if (count($badems)>0) {
	?>
      <td width="35%" align="center">Rejected Emails</td>
      <?php
		}
	?>
      <?php
		if (count($badips)>0) {
	?>
      <td width="30%" align="center">Rejected IPs</td>
      <?php
		}
	?>
      <?php
		if (count($goodips)>0) {
	?>
      <td width="30%" align="center">Good IPs</td>
      <?php
		}
	?>
    </tr>
    <tr>
      <?php
		if (count($badems)>0) {
	?>
      <td style="border:1px solid black;font-size:.75em;padding:3px;" valign="top"><?php
		foreach ($badems as $key => $value) {
			//echo "$key; Date: $value<br/>\r\n";
			$key=urldecode($key);
			echo "<a href=\"http://www.stopforumspam.com/search?q=$key\" target=\"_stopspam\">$key: $value</a>
<a href=\"\" onclick=\"return delblack('$key');\" title=\"Delete from Black List\" alt=\"Delete from Black List\" ><img src=\"$trash\" width=\"12px\" /></a>			
			<br/>";
		}
	?></td>
      <?php
		}
	?>
      <?php
		if (count($badips)>0) {
	?>
      <td  style="border:1px solid black;font-size:.75em;padding:3px;" valign="top"><?php
		foreach ($badips as $key => $value) {
			//echo "$key; Date: $value<br/>\r\n";
			echo "<a href=\"http://www.stopforumspam.com/search?q=$key\" target=\"_stopspam\">$key: $value</a>
<a href=\"\" onclick=\"return delblack('$key');\" title=\"Delete from Black List\" alt=\"Delete from Black List\" ><img src=\"$trash\" width=\"12px\" /></a>			
			<br/>";
		}
	?></td>
      <?php
		}
	?>
      <?php
		if (count($goodips)>0) {
	?>
      <td  style="border:1px solid black;font-size:.75em;padding:3px;" valign="top"><?php
		foreach ($goodips as $key => $value) {
			//echo "$key; Date: $value<br/>\r\n";
			echo "<a href=\"http://www.stopforumspam.com/search?q=$key\" target=\"_stopspam\">$key: $value</a>
			<a href=\"\" onclick=\"return delblack('$key');\" title=\"Delete from Black List\" alt=\"Delete from Black List\" ><img src=\"$trash\" width=\"12px\" /></a><br/>";
		}
	?></td>
      <?php
		}
	?>
    </tr>
  </table>
  <?PHP
} 
	$options=kpg_sp_get_options();
	extract($options);
 
 	$ip=kpg_get_ip();

	if ($addtowhitelist=='Y'&&in_array($ip,$wlist)) {
		echo "<h3>Your current IP is in your white list. This will keep you from being locked out in the future</h3>";
	}



	if (function_exists('is_multisite') && is_multisite()) {
	?>
  <p>If you are looking for the list of spam on the blogs, I've broken that out into a separate plugin. 
    It works with this plugin and allows you to report spam from a single page for all blogs. It is less likely to time out than the old version.
    Please try it out: <a href="http://wordpress.org/extend/plugins/mu-manage-comments-plugin/" target="_blank">MU Manage Comments Plugin</a></p>
  <?php
	}
?>
  <?php
     $f=dirname(__FILE__)."/../sfs_debug_output.txt";
	 if (file_exists($f)) {
	    ?>
  <h3>Error Log</h3>
  <p>If debugging is turned on, the plugin will drop a record each time it encounters a PHP error. 
    Most of these errors are not fatal and do not effect the operation of the plugin. Almost all come from the unexpected data that
    spammers include in their effort to fool us. The author's goal is to eliminate any and
    all errors. These errors should be corrected. Fatal errors should be reported to the author at www.blogseye.com.</p>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="kpg_stop_delete_log" value="true" />
    <p class="submit">
    <input  class="button-primary"  value="Delete Error Log File" type="submit" />
	</p>
  </form>
  <pre>
<?php readfile($f); ?>
</pre>
  <?php
	 }
// show the history log file
	
	$fnonce=wp_create_nonce('kpgstopspam_fileview');

	$clog=kpg_file_exists('.history_log.txt');
	if ($clog!==false) {
		if ($clog>$logfilesize) {
			?>
			<p style="color:red">Your logfile has exceded its size limit (set log file size in options)</p>
			<?php
		}
?>
	<h3>Log file</h3>

	<a href="<?php echo $sme; ?>&v=<?php echo $fnonce; ?>">View Log file (size=<?php echo $clog; ?> bytes)</a>
<?php
	}
} // end of check for fnonce
?>
</div>
