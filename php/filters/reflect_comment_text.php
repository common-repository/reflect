<?php 

if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
    get_currentuserinfo();
	global $current_user;
}


if (!class_exists("ReflectCommentText")) {
	
class ReflectCommentText {
	
	function get_commenter_name_short(){
		$auth = get_comment_author();
		$pos = strpos($auth, ' ');
		if ($pos)
			return substr($auth, 0, $pos);
		else
			return $auth;
	}
	
	function build_new_bullet($comment_id){
	    $commenter_name = $this->get_commenter_name_short();
	    
	    return "
	            <li class=\"new_bullet bullet\"> 
	                <button class=add_bullet type=button onclick=\"Reflect.transitions.to_bullet($comment_id)\"> 
	                    Add a point that $commenter_name makes
	                </button>
	           </li>";		
	}

	function comment_filter($content) {
		return $content;
	}
	
	function get_rebuttals($bullet){
		global $wpdb;
		global $current_user;

		$rebuttals = array();
		$cur_rebuttals = $wpdb->get_results("SELECT rebuttal_rev FROM " . $wpdb->prefix . "reflect_rebuttal_current WHERE bullet_id = $bullet->bullet_id");		
		foreach($cur_rebuttals as $cur_rebutt){
			$rebutt = $wpdb->get_row("SELECT rebuttal_id as id,timestamp as ts,user as u,text as txt, signal as sig FROM " . $wpdb->prefix . "reflect_rebuttal_revision WHERE id = $cur_rebutt->rebuttal_rev");		
			array_push($rebuttals, $rebutt);
		}
		$media_dir = get_bloginfo('url') . '/wp-content/plugins/reflect/media/';
				
		$text = '';
		$comment_author = get_comment_author();
		$cur_user = $current_user->user_login;
		
		if (count($rebuttals) > 0){
			foreach ($rebuttals as $rebutt){
				switch($rebutt->sig){
					case 0: 
						//$img_src = "<img src=" . $media_dir . 'response-no.png' . ">";
						$img_src = "<span class=response_no>-</span>";
						break;
					case 1:
						//$img_src = "<img src=" . $media_dir . 'response-maybe.png' . ">";
						$img_src = "<span class=response_maybe>!</span>";
						break;
					case 2:
						//$img_src = "<img src=" . $media_dir . 'response-yes.png' . ">";	
						$img_src = "<span class=response_yes>+</span>";			
						break;
					
				}
				$text .= "
					<li class=rebuttal id=rebuttal-$rebutt->id>
						<ul class=rebutt_list>
							<li class=img>" . $img_src . "</li>
							<li class=rebutt_txt>$rebutt->txt <span class=username><a class=user>$rebutt->u</a></span></li>	
						</ul>
						<div class=rebuttal_footer_wrapper>
						    <ul class=footer>
						        <li>
						            <ul class=\"rebuttal_operations\">
						                <li class=modify_operation>
						                    <button class=modify>
						                        <a title=\"modify\"><img class=base src=" . $media_dir . "edit.png><img title=\"Modify\" class=hover src=" . $media_dir . "/edit_hover.png></a>
						                    </button>
						                </li>									       
						                <li class=delete_operation>
						                    <button class=delete>
						                        <a title=\"delete\"><img class=base src=" . $media_dir . "delete_gray.png><img title=\"Delete\" class=hover src=" . $media_dir . "/delete_red.png></a>
						                    </button>
						                </li>			           
						                <li class=dispute_operation>
						                    <span class=\"bullet_prompt_problem base\"><img class=base src=" . $media_dir . "comment-flag.png><img class=hover src=" . $media_dir . "/comment-flag-hover.png></span>
						                    <ul class=bullet_report_problem>
						                        <li>not written neutrally</li>
						                    </ul>
						                </li>
						            </ul>
						        </li>
						    </ul>
						</div>
						<div style=\"clear:both\"></div>						
					</li>
				";
				
			}
		}elseif($comment_author == $cur_user){		
			$text = "
				<li class=\"rebuttal new\">
					<ul class=\"rebuttal_def\">
						<li class=\"prompt\">Were you saying this?</li>
						<li>
							<ul class=rebuttal_eval>
								<li><input type=\"radio\" name=\"accurate\" value=\"2\">Yes</li>
								<li><input type=\"radio\" name=\"accurate\" value=\"1\">Kind of...</li>
								<li><input type=\"radio\" name=\"accurate\" value=\"0\">No</li>
							</ul>
						</li>
						<li class=rebuttal_dialog>
						    <table class=\"new_bullet_wrapper reflect\">
						        <tr>
						            <td class=new_bullet_text_wrap>
						                <textarea class=\"new_bullet_text\"></textarea>  
						            </td>
						            <td class=submit_buttons>
						                <div><button class=\"cancel_bullet\"><img src=\"" . $media_dir . "cancel2.png\"></button></div>
						            </td>
						        </tr>
						        <tr class=submit_footer>
						   			<td>
							            <ul>
						                	<li class=submit>
						                		<button class=\"bullet_submit\">Done</button>
						                	</li>
						                    <li class=count>
						                        <a title=\"Please limit your response to 140 characters or less.\"><span class=char_count></span></a>
						                    </li>
					                    </ul>
									</td>
		                    	</tr>
						    </table> 						
						</li>
					</ul>
				</li>
				";
			
		}		
		return '<div class=rebuttals><ul>' . $text . '</ul></div>';
	}
	
	function get_bullets($comment_id){
		global $wpdb;
		
		//TODO : sort bullets by highlight?
		
		$media_dir = get_bloginfo('url') . '/wp-content/plugins/reflect/media';
		
		$cur_bullets = $wpdb->get_results("SELECT bullet_rev FROM " . $wpdb->prefix . "reflect_bullet_current WHERE comment_id = $comment_id");
		$bullets = array();
		foreach($cur_bullets as $cur_bullet){
			$bullet = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "reflect_bullet_revision WHERE id = $cur_bullet->bullet_rev");
			array_push($bullets,$bullet);
		}

		if (count($bullets) < 1) 
			return '';
				
		$new_bullets = '';
		foreach ($bullets as $bullet){
			$rebuttals = $this->get_rebuttals($bullet);
			
		    $new_bullets .= "
		    	<li class=bullet id=bullet-$bullet->bullet_id >
		    		<div class=bullet_main>
		    			<ul class=bullet_main_wrapper>
		    				<li class=bullet_text>
		    						$bullet->text 
		    						<span class=username>   <a class=user>$bullet->user</a></span>
		    				</li>
		    				<li class=bullet_footer_wrapper>
					            <ul class=bullet_operations>
					                <li class=modify_operation>
					                    <button class=modify>
					                        <a title=\"modify\"><img class=base src=" . $media_dir . "/edit.png><img title=\"Modify\" class=hover src=" . $media_dir . "/edit_hover.png></a>
					                    </button>
					                </li>								      
					                <li class=delete_operation>
					                    <button class=delete>
					                        <a title=\"delete\"><img class=base src=" . $media_dir . "/delete_gray.png><img title=\"Delete\" class=hover src=" . $media_dir . "/delete_red.png></a>
					                    </button>
					                </li>			           
					                <li class=dispute_operation>
					                    <span class=\"bullet_prompt_problem base\"><img class=base src=" . $media_dir . "/comment-flag.png><img class=hover src=" . $media_dir . "/comment-flag-hover.png></span>
					                    <ul class=bullet_report_problem>
					                       <li class=\"flag\" name=\"input\">not a summary</li>
                                           <li class=\"flag\" name=\"neutral\">not written neutrally</li>
                                           <li class=\"flag\" name=\"accurate\"><a class=user>" . $this->get_commenter_name_short() . "</a> didn't say this</li>
                                           <li class=\"flag\" name=\"duplicate\">duplicate bullet</li>
					                    </ul>
					                </li>
					            </ul>
							</li>
						</ul>
						<div class=cl></div>
					</div>
						
					
					$rebuttals
		    	</li>
		    ";
		}
		 return $new_bullets;
		
	}
	
	function reflect_comment_text_filter($content) {
		global $current_user;
		
		$comment_id = get_comment_id();
		$bullets = $this->get_bullets($comment_id);
		
		if(get_comment_author() != $current_user->user_login)
			$new_bullet = $this->build_new_bullet($comment_id);
		else
			$new_bullet = '';
		$user = $current_user->user_login;
		if(!$user || $user == '')
			$user = 'Anonymous';
		
		return "
			   <div id=reflected>
			   	   <span id=\"rf_user_name\" style=\"display:none\">$user</span>
				   <div id=rf_comment_wrapper-$comment_id class=rf_comment_wrapper>
				   	   <span class=rf_comment_author style=display:none>" . get_comment_author() . "</span>
				       <div class=rf_comment_text_wrapper >
				       		<div class=rf_comment_text>
				           		$content
				            </div>
			    	   </div>
				       <div class=rf_comment_summary>
			               <div class=summary id=summary-$comment_id >
			                   <ul class=bullet_list>
			                   		$bullets
			                        $new_bullet
			                   </ul>
			               </div>	       
				       </div>
				       <div style=clear:both></div>
				   </div>
			   </div>
		   ";
	}
    
}


}

if (class_exists("ReflectCommentText")) {
    $reflect_comment_text = new ReflectCommentText();
    add_filter('comment_text', array(&$reflect_comment_text,'reflect_comment_text_filter'), 999);
}

?>