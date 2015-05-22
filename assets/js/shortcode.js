/*=========================================
                SHORTCODE
==========================================*/          
(function($) {

  var Grps = groups;

  tinymce.create('tinymce.plugins.turbotabs', {
    init: function(ed, url) {
      ed.addButton('turbotabs', {
        title: 'TurboTabs',
        image: url + '/../images/tinymce-button.png',
        cmd: 'turbotabs_cmd'        
      });
 
      ed.addCommand('turbotabs_cmd', function() {
        ed.windowManager.open(
          //  Window Properties
          {
            file: url + '/../shortcode/turbotabs-insert.html',
            title: 'TurboTabs Builder',
            width: 370,
            height: 250,
            inline: 1
          },
          //  Windows Parameters/Arguments
          {
            editor: ed,
            groups: Grps,
            jquery: $ // PASS JQUERY
          }
        );
      });
    }
  });
  tinymce.PluginManager.add('turbotabs', tinymce.plugins.turbotabs);
})(jQuery);
