

(function($j) {


Reflect.config.study = false;
Reflect.config.view.enable_rating = <?php echo get_option("rf_enable_flagging"); ?>;

$j.extend(Reflect.config.api, {
  domain : 'wordpress_plugin',
  server_loc: "<?php echo get_bloginfo('url') ?>",
  media_dir: "<?php echo get_bloginfo('url') ?>" + '/wp-content/plugins/reflect/media/'
});

$j.extend(Reflect.config.contract, {
  components: [{
    comment_identifier: '.comment',
    comment_offset: 8,
    comment_text: '.ctext',
    get_commenter_name: function(comment_id){return $j.trim($j('#comment-'+comment_id+ ' .rf_wp_comment_author').text());}
  }]
});

Reflect.Contract = Reflect.Contract.extend({
  user_name_selector : function(){return '';},
  modifier: function(){
    $j('<?php echo get_option("rf_comment_text_class"); ?>').wrap('<div class="ctext" />');
  },
  post_process: function(){
    var comment_width =  $j('.ctext:first').width() - $j('.rf_comment_summary:first').outerWidth() - ($j('.rf_comment_text_wrapper:first').outerWidth() - $j('.rf_comment_text_wrapper:first').width());
    $j('.rf_comment_text_wrapper').width(comment_width);

  },
  get_comment_thread: function(){
    return $j('.commentlist');
  }
});


Reflect.api.DataInterface = Reflect.api.DataInterface.extend({

  init: function(config){
    this._super(config);
    this.api_loc = this.server_loc + '/wp-content/plugins/reflect/php/api.php';
  },
   get_templates: function(callback){
     $j.get(this.server_loc + '/wp-content/plugins/reflect/templates/templates.html',callback);
   },
   get_current_user: function(){
    var user = "<?php echo $current_user->user_login ?>";
    if(!user || user == '')
      user = 'Anonymous';
    return user;
   },
  post_bullet: function(settings){
    settings.params.operation = 'bullet';
    $j.ajax({url:this.api_loc,
      type:'POST',
      data: settings.params,
      error: function(data){
          var json_data = JSON.parse(data);
          settings.error(json_data);
      },
      success: function(data){
          var json_data = JSON.parse(data);
          settings.success(json_data);
      }
    });
  },

  post_rating: function(settings){ 
    settings.params.operation = 'rate';
    $j.ajax({url:this.api_loc,
      type:'POST',
      data: settings.params,
      error: function(data){
          var json_data = JSON.parse(data);
          settings.error(json_data);
      },
      success: function(data){
          var json_data = JSON.parse(data);
          settings.success(json_data);
      }
    });

  },
  post_response: function(settings){
    settings.params.operation = 'response';
    $j.ajax({url:this.api_loc,
      type:'POST',
      data: settings.params,
      error: function(data){
        var json_data = JSON.parse(data);
        settings.error(json_data);
      },
      success: function(data){
        var json_data = JSON.parse(data);
        settings.success(json_data);
      }
    });
  },
  get_data: function(params, callback){
    $j.getJSON(this.api_loc, params, callback);
  }
});

})(jQuery);