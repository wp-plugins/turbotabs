/** 
  * TurboTabs Wordpress Plugin
  * Author: Aleksej Vukomanovic
  * Website: http://themeflection.com
  * Version: 1.0
  * Version from: 10.04.2015
  */
;(function ( $ ) {
    $.fn.turbotabs = function(options){
        // collecting values
         var textColor = this.find('#turbotabs_styles').data('tt-tc');
         var backgroundColor = this.find('#turbotabs_styles').data('tt-cc');
         var navBackground = this.find('#turbotabs_styles').data('tt-nc');
         var hoverBackground = this.find('#turbotabs_styles').data('tt-hb');
         var activeBackground = this.find('#turbotabs_styles').data('tt-ab');
         var mode = this.data('tt-mode');
         var width = this.data('tt-width');
         var padding = this.data('tt-padding');
         var animation = this.data('tt-animation');
         var headingBackground = this.find('#turbotabs_styles').data('tt-hbck');
         var navTextShadow  = this.find('#turbotabs_styles').data('tt-shd');
         var navTextShadowColor  = this.find('#turbotabs_styles').data('tt-shdc');
         var navAlign  = this.data('tt-align');
         var layout = this.find('#turbotabs_styles').data('tt-layout');
         var borderColor = this.find('#turbotabs_styles').data('tt-bdc');
         var TitleColor = this.find('#turbotabs_styles').data('tt-ttl');
         var forceHeight = this.find('#turbotabs_styles').data('tt-fh');
         var Id = this.attr('id');
         var tabs = this.find('.tt_tabs');
         var container = this.find('.tt_container');
         var tab = this.find('.tt_container .tt_tab');
         var sel = this;
         var animationIn = '';
         var animationOut = '';
         var once = 0;
         var primWidth = []; // create an array that will store the primary widths, before resizing (used in responsive function)
         var tabsResponsive = false;
         var timer = 340;
         var tabHeights = '';
         var maxHeight = '';
         var style = '';
         var addMiddle = '';
         var respStyle = '';
         var accordion = false;
         var loaded = 0;

         //setting defaults
         width = width != '%' ? width : '70%';
         padding = padding != '%' ? padding : '15%';
         navBackground = navBackground ? navBackground : '#929292';
         animation = animation ? animation : 'Scale';
         navAlign = navAlign ? navAlign : 'left';
         activeBackground = activeBackground ? activeBackground : '#444A53';
         textColor = textColor ? textColor : '#fff';
         TitleColor = TitleColor ? TitleColor : 'palegoldenrod';
         backgroundColor = backgroundColor ? backgroundColor : '#444A53';

         if( sel.hasClass('responsive') ){
             sel.find('.tt_tabs li').css({height: 'auto'});
           }
           
          if( mode === 'horizontal' ){
                // setting each tab to fit the container width 
               setTimeout(function(){
                  var tWidth = container.outerWidth();
                  container.find('.tt_tab').outerWidth(tWidth);
               },50);

              // setting the each li height to be equal in case that one has description subtitle added
             // and other, or others don't. This will prevent ugly tab design
                setTimeout(function(){
                   liHeights = tabs.find('li').map(function() {
                      return $(this).outerHeight(true);
                   }).get();
                   setEqual = Math.max.apply(null, liHeights);
                   tabs.find('li').css({height: setEqual, verticalAlign: 'bottom'});
                },50);
         } 

        setTimeout(function(){ // delay setting the heigh for small interval, giving it a time to collect right value
            calcHeight();
        },150);

        if( forceHeight === 'yes' ) {
            setTimeout(function(){
                var activeHeight = sel.find('.tt_tab.active').outerHeight(true);
                container.css('height', activeHeight ); 
            },180);
         }

        if( mode === 'vertical' ){ // check if mode is set to vertical
            sel.addClass('vertical');
        } else if( mode === 'accordion' ){
            reset();
            accordion = true;
        }

        borP = 'top'; // assign current position to borderPosition variable
        // addiding alternative layout (the one user selected)
        if( layout === 'classic' ){
             sel.addClass('classic');
             style += ( '#' + Id + '.classic .tt_container{ border: 1px solid '+ borderColor +'; }  #' + Id + '.classic .tt_tabs li{border: 1px solid '+ borderColor +'; background: '+ navBackground +';} #' + Id + '.classic .tt_tabs li.active{border-' + (borP === 'top' ? 'bottom' : 'top' ) +': 1px solid transparent; background: '+ backgroundColor +'} #' + Id + '.classic .tt_tabs li:hover{background:'+ backgroundColor +'}' );
        } 
        else if( layout === 'hollow' ){
            sel.addClass('hollow');
            backgroundColor = 'transparent';
            style +=( ' #' + Id + '.hollow .tt_tabs li{background:transparent; border-'+ (borP === 'top' ? 'bottom' : 'top' ) +': 2px solid transparent} #' + Id + '.hollow .tt_tabs li.active{ border-'+ (borP === 'top' ? 'bottom' : 'top' ) +': 2px solid wheat;} #' + Id + '.hollow .tt_container{border: none;box-shadow:none;} #' + Id + '.hollow .tt_tabs{border-'+ (borP === 'top' ? 'bottom' : 'top' ) +': 2px solid '+ borderColor +'}' );
        } 
        else if( layout === 'simple' ){
            sel.addClass('simple');
            style += ( ' #' + Id + '.simple .tt_tabs li{box-shadow: none; background: transparent; border: 1px solid transparent} #' + Id + '.simple .tt_tabs li.active{border: 1px solid '+ borderColor +'; border-' + (borP === 'top' ? 'bottom' : 'top' ) +'-color: transparent; background: '+ backgroundColor +'} #' + Id + '.simple .tt_container, #' + Id + '.simple:not(.responsive) .tt_tabs li:not(.active):hover{border: 1px solid '+ borderColor +'; } #' + Id + '.simple .tt_tabs li:not(.active):hover{border: 1px solid'+ borderColor +'}' );
        } 
        // now check if mode is vertical and apply additional styling
        if( mode === 'vertical' && layout === 'classic' ){
            style += ' #' + Id + '.classic.vertical .tt_tabs{border: none} #' + Id + '.classic.vertical .tt_tabs li{border: 1px solid '+ borderColor +'} #' + Id + '.classic.vertical:not(.responsive) .tt_tabs li.active{border-'+ (borP === 'right' ? 'left' : 'right' ) +': 1px solid transparent}';
        }
        else if( mode === 'vertical' && layout === 'hollow' ){
            style += ' #' + Id + '.hollow.vertical .tt_container{border-'+ borP +': 2px solid '+ borderColor +'} #' + Id + '.hollow.vertical .tt_tabs{border: none} #' + Id + '.hollow.vertical .tt_tabs li{border: none; border-'+ (borP === 'right' ? 'left' : 'right' ) +': 2px solid transparent} #' + Id + '.hollow.vertical:not(.responsive) .tt_tabs li.active{border-'+ (borP === 'right' ? 'left' : 'right' ) +': 2px solid wheat;}';
        }
        else if( mode === 'vertical' && layout === 'simple' ){
            style += ' #' + Id + '.simple.vertical .tt_tabs{border: none} #' + Id + '.simple.vertical .tt_tabs li{border: 1px solid transparent} #' + Id + '.simple.vertical:not(.responsive) .tt_tabs li.active, #' + Id + '.simple.vertical:not(.responsive) .tt_tabs li:hover{border: 1px solid '+ borderColor +';border-'+ (borP === 'right' ? 'left' : 'right' ) +': 1px solid transparent}';
        }
      

        // addiding the selected navigation align
        if( navAlign ){ 
            if( navAlign === 'left' || navAlign === 'right' ){
                style += (' #' + Id + ' .tt_tabs{text-align: '+ navAlign +'; padding-'+ navAlign +': 25px!important;}');
            } 
            else if( navAlign === 'center' ){
                style += (' #' + Id + ' .tt_tabs{text-align: '+ navAlign +'; padding: 0}');
            }
            else if( navAlign === 'middle' ){
                setTimeout(function(){ 
                    var middle = parseInt( sel.find('.tt_container').outerHeight(true) ) - parseInt( sel.find('.tt_tabs').height() );
                    var middleM = middle / 2;
                    addMiddle += (' #' + Id + ' .tt_tabs{ vertical-align: top; margin-top: '+ middleM +'px; margin-bottom: '+ middleM +'px }');
                    sel.closest('html').find('head style[data-title="'+ Id +'"]').append(addMiddle);
                },170); 
            } else {
                 style += (' #' + Id + ' .tt_tabs{vertical-align: '+ navAlign +'; padding: 15px 0 15px 0;}');
            }    
        }   

        // applying the color, background and other options to the actual tab
        if( layout === 'hollow' ){
            boxShd = 'none';
            borderR = 'none';
        }
        this.css({width: width, padding: '20px ' + padding});
        sel.css('display', 'table');
        if( layout === '' ){
             container.css({color: textColor, background: backgroundColor});
         } else {
            container.css({color: textColor, background: backgroundColor});
         }
        this.closest('html').find('head').append('<style data-title="'+ Id +'" type="text/css">#' + Id + ' .tt_tabs{margin: 0;} #' + Id + ' .tt_tabs li{background: ' + navBackground + ' ; color: ' + textColor + '} #' + Id + ' .tt_tabs li:hover{background: ' + hoverBackground + '; color: ' + hoverColor +'}  #' + Id + ' .tt_tabs li:hover .ttnav-title, #' + Id + ' .tt_tabs li:hover .ttnav-sbt, #' + Id + ' .tt_tabs li.active .ttnav-title, #' + Id + ' .tt_tabs li.active .ttnav-sbt{color: '+ hoverColor +'} #' + Id + ' .tt_tabs li.active, #' + Id + ' .tt_tabs li.active h3.tt_heading{background:'+ activeBackground +'; color: '+ hoverColor +'} #' + Id + ' .tt_tabs .tt_tab{color: '+ textColor +'} #' + Id + ' .tt_tabs h3.tt_heading{ background: '+ headingBackground +'; margin: 0;} #' + Id + ' .tt_tabs:not(.responsive), #' + Id + ' .tt_tabs:not(.vertical){text-align: '+ navAlign +'} #' + Id + ' .tt_tabs li:hover{background: '+ hoverBackground +'} #' + Id + '.hollow .tt_tabs li:hover{background: transparent} '+ style +' #' + Id + ' .tt_tabs li .fa{color: '+ iconC +'} #' + Id + ' .tt_tabs li:hover .fa, #' + Id + ' .tt_tabs li.active .fa{color: '+ hoverColor +'} #' + Id + ' .tt_tabs li .ttnav-title{color: '+ TitleColor +'} #' + Id + ' .tt_tabs li .ttnav-sbt{}</style>');
        if( navTextShadow === 'on' ) {  // if text shadow enabled
            if( navTextShadowColor === 'dark' ){
                sel.closest('html').find('head style[data-title="'+ Id +'"]').append(' #' + Id + ':not(.responsive) .tt_tabs li .ttnav-title, #' + Id + ' .tt_tabs h3.tt_heading .ttnav-title{text-shadow: 1px 0 2px #333;}');
            } else {
                sel.closest('html').find('head style[data-title="'+ Id +'"]').append(' #' + Id + ':not(.responsive) .tt_tabs li .ttnav-title, #' + Id + ' .tt_tabs h3.tt_heading .ttnav-title{text-shadow: 1px 0 2px #fefefe;}');
            }
        }
 
        /*==============================================
                            ANIMATIONS
        ================================================*/
        if( 'Scale' === animation ){
            animationIn = 'zoomIn';
            animationOut = 'zoomOut';
        }
        else if( 'FadeUp' === animation ){
            animationIn = 'fadeInUp';
            animationOut = 'fadeOutDown';
            timer = 280;
        }
        else if( 'FadeLeft' === animation ){
            animationIn = 'fadeInLeft';
            animationOut = 'fadeOutLeft';
            timer = 280;
        }
        else if( 'Bounce' === animation ){
            animationIn = 'bounceIn';
            animationOut = 'bounceOut';
        }
        /*==============================================
                       Initialize Tabs
        ===============================================*/ 
        var activeIndex = sel.data('tt-index');
        if( mode === 'accordion' ){
            setTimeout(function(){
                tabs.find('li h3.tt_heading').eq(activeIndex).addClass('active').parent().find('.tt_tab').slideDown();
            },100);
        } else {
            tabs.find('li').eq(activeIndex).addClass('active');
            container.find('.tt_tab').eq(activeIndex).addClass('active animated ' + animationIn).siblings().addClass('animated ' + animationOut);
        }

        tabs.find('li').on('click', function(){
            if( false === tabsResponsive ){ 
                if( !$(this).hasClass("active") ) {
                    var index = $(this).index();
                    var current = $(this);
                    $(this).parent().find("li.active").removeClass("active");
                    $(this).addClass("active");
                    $(this).closest(sel).find("div.tt_tab.active").removeClass("active " + animationIn).addClass(animationOut); 
                     setTimeout(function(){
                        current.closest(sel).find("div.tt_tab").eq(index).addClass("active " + animationIn).removeClass(animationOut); 
                        ForceHeight(); // if force height option enabled 
                    },timer);
                }// if
            }// if tabs not responsive
        });

        //if tabs are in accordion mode
        tabs.find('li').on('click','h3.tt_heading', function(){ 
            if( !$(this).hasClass("active") ) { 
                 $(this).addClass('active').parent().addClass('active').find('.tt_tab').slideDown();
                 $(this).parent().siblings().removeClass('active').find('.tt_tab').slideUp();
                  $(this).closest('.tt_tabs').find('li:not(.active) .tt_heading').removeClass('active');
            } else{
                $(this).removeClass('active').parent().removeClass('active').find('.tt_tab').slideUp();
            }//else
        });
        /*==============================================
                        RESPONSIVENESS
        ===============================================*/
        // create variables that will store values that will be added later
            var tabsWidth = 0;
            var currWidth = 0;
            var conWidth = 0;
            var mobile = false;
            var tabW = 0;
            var called = 0;
            var resized = 0;
            var prDone = 0;
            var printW = 0;
            var oldHgh = tabs.outerHeight(true);
            primWidth['resized'] = 0;

           
            calcWidth();
           
        if( !accordion ) {   // if not accordion mode selected

            if( mode != 'vertical' ) {

                if( container.outerWidth() < tabsWidth + 20 ){ // if starting from small screen transform it to accordion now
                       reset(); 
                       mobile = true;
                }
                
                $(window).resize(function(){
                    var windowWidth = parseInt( $(window).outerWidth(true) ); // check for device width;
                    calcWidth(); //callback

                    if( !mobile ) { // if viewed on larger screen and then resized to smaller one 

                        var twidth = container.outerWidth();
                        var currHgh = tabs.outerHeight(true);

                        if( typeof resize === 'function'  ){
                            setTimeout(function(){
                                twidth = container.outerWidth();
                                container.find('.tt_tab').outerWidth(twidth);
                            });
                        }

                        if( windowWidth < 480 ) {
                            reset(); 
                        } 
                        else if( false === tabsResponsive && twidth < tabsWidth ){
                                reset(); 
                                if( prDone === 0 ){
                                    printW = twidth + 15;
                                    prDone++;
                                }
                        } 
                        else if( sel.find('li').outerWidth(true) > printW ){
                            resize();
                        } else if( false === tabsResponsive && parseInt(currHgh) > parseInt(oldHgh) ){
                            reset();
                        }
                    } else { 
                       // if starting from small screen
                       if( windowWidth < 480 ) {
                           if(  true === tabsResponsive && currWidth > primWidth['container']  * 1.5 ) { 
                                resize(); 
                                calcHeight();
                                setTimeout(function(){
                                calcHeight();    
                                if( 1 === once ){
                                    primWidth['disposal'] = tabW + 130;
                                } //if
                                },120);
                                
                            } //if 
                            if( false === tabsResponsive && primWidth['disposal'] > currWidth ){ 
                                reset(); 
                            }//if
                        } else if( windowWidth > 480  ){
                            var zbr = tabs.find('li').length * 170; // calculate approximate width for each tab nav
                            if( currWidth > zbr ) {
                                resize();
                                calcHeight();
                            } else {
                                reset();
                            }
                        }  else if( windowWidth > 1100 ){
                            resize();
                        }  
                    }//else
                }); //window.resize()
            } else { // if vertical mode 
                var windowWidth = parseInt( $(window).outerWidth() );
                
                 if( windowWidth < 760 ){ // if starting from small screen transform it to accordion now
                       reset();
                       mobile = true;
                } else if( sel.width() < 450 ){ // if width of vertical tab less than 450px, transform
                    reset();
                }        
                // wait 190ms for tab to fully load it's height and then measure tab .tt_cotainer height
                // matching it with current #turbotab height
                // if container + 40 ( adding 20px for top padding and 20px for bottom) is of less height
                // than #turbotab, that means tabs navigation went beneath the tab content, so transform to accordion
                setTimeout(function(){
                    if( parseInt( container.outerHeight(true) ) + 40 < parseInt( sel.outerHeight(true) ) ){ 
                        reset();
                    }    
                },190); 

                $(window).resize(function(){
                    windowWidth = parseInt( $(window).outerWidth() ); //  updatedevice width;

                    calcWidth(); 

                    setTimeout(function(){ // run again this check, just to be sure
                        if( parseInt( container.outerHeight(true) ) + 40 < parseInt( sel.outerHeight(true) ) ){ 
                            reset();
                        }    
                    },190); 

                    if( !mobile ) { // if viewed on larger screen and then resized to smaller one
                        if( windowWidth < 720 ){
                            reset(); 
                        } else {
                            resize(); 
                        }  
                    } else {
                        if( windowWidth > 720 ){
                            resize();
                            setTimeout(function(){
                                calcHeight();    
                            },120);
                        } else {
                            reset();
                        }//else

                    }//else
                });//window.resize()
            } // else (is vertical)
        } else {
            reset();
        }//else( is accordion)
        
        /*==============================================
                        HELPER FUNCTIONS
        ===============================================*/
        function ForceHeight(){ 
          // force tab content to fit currently viewed tab, if enabled
         if( forceHeight === 'yes' && tabsResponsive === false ){ 
             var currentActive = container.find('.tt_tab.active').outerHeight(true);
             if( mode === 'horizontal' ){
                container.animate({height: currentActive},320); 
             } 
             else if( mode === 'vertical' ){ 
                if( currentActive < tabs.outerHeight(true) ) {
                  currentActive = parseInt(currentActive) + ( parseInt( tabs.outerHeight(true) ) - currentActive ) + 50; 
                }
                container.animate({height: currentActive + 'px'},320);
             }
         }
        }
        function calcWidth(){
             // reset variables before adding new values
             tabsWidth = 0;
             currWidth = 0;
             conWidth = 0;
             // get the widths of both navigations and container
             currWidth = parseInt( tabs.find('li').first().outerWidth(true) ); // get current width of resized tab li
             conWidth = parseInt( container.outerWidth(true) );
             if( tabsResponsive === false ){
                 tabs.find('li').each(function(){ // loop through navs and add width to variable
                    tabsWidth += parseInt( $(this).outerWidth(true) );
                 }); //if
            } else {
                tabsWidth = primWidth['tabs'];
            }//else
            // use the array created in the beginning to store primary widths
            //make sure that this process is done only once (preventing the new values to override the old ones)
            if( 0 === once ) {
                once++ ;
                primWidth['tabs'] = tabsWidth + 10;
                primWidth['container'] = conWidth;
            } else if ( 0 === once && mobile ){
                primWidth['container'] = sel.find('.tt_tabs li.active .tt_tab').width();
            }
            tabW = parseInt( $('.tt_tab').width() );
        }// calcWidth()

        function calcHeight(){
            //seting the the tab content height to the tallest tab content
            // src = http://stackoverflow.com/questions/6781031/use-jquery-css-to-find-the-tallest-of-all-elements
            // Get an array of all element heights
            tabHeights = tab.map(function() {
            return $(this).outerHeight(true);
            }).get();
            // Math.max takes a variable number of arguments
            // `apply` is equivalent to passing each height as an argument
            maxHeight = Math.max.apply(null, tabHeights);
            container.css('height', maxHeight);
            if( loaded === 0 ){
              loaded++;
            }
        }// calcHeight()

        function reset(){ // transform tab to accordion if number of nav tabs exceeds container width
            tabsResponsive = true;
            if( called === 0 ){
                primWidth['resized'] = parseInt( container.width() );
                called++;
            }
            sel.addClass('responsive');
            if( tabs.find('li').first().find('.tt_heading').length != 1 ){
                tabs.find('li').wrapInner('<h3 class="tt_heading"></h3>');
            }
            var index = -1;
            var zbir = tab.length;
            for( var i = 0; i < zbir; i++ ){
                (tab.eq(i)).appendTo(tabs.find('li').eq(i));
            }
            if( resized === 0 ){
                resized++;
                if( layout === 'classic' ){
                    respStyle += ' #'+Id+'.responsive.classic .tt_tabs{border: none} #'+Id+'.responsive.classic .tt_tabs li{border: 1px solid '+ borderColor +'; background: '+ navBackground +'} #'+Id+'.responsive.classic .tt_tabs li h3.tt_heading{background: '+ headingBackground +'} #'+Id+'.responsive.classic .tt_tabs li h3.tt_heading:hover, #'+Id+'.responsive.classic .tt_tabs li.active h3.tt_heading{background: '+ activeBackground +'; color: '+ hoverColor +'} #'+Id+'.responsive.classic .tt_tabs li.active{background: '+ backgroundColor +'}';
                } else if( layout === 'hollow' ){
                    respStyle += ' #'+Id+'.responsive.hollow .tt_tabs{border: none;} #'+Id+'.responsive.hollow .tt_tabs li{border: 1px solid '+ borderColor +'} #'+Id+'.responsive.hollow .tt_tabs li.active, #'+Id+'.responsive.hollow .tt_tabs li:hover{border-color: wheat}  #'+Id+'.responsive.hollow .tt_tabs li h3.tt_heading{background: transparent}';
                }  else if( layout === 'simple' ){
                    respStyle += ' #'+Id+'.responsive.simple .tt_tabs{border: none} #'+Id+'.responsive.simple .tt_tabs li{border: 1px solid '+ borderColor +'} #'+Id+'.responsive.simple .tt_tabs li h3.tt_heading{background: '+ backgroundColor +'} #'+Id+'.responsive.simple .tt_tabs li h3.tt_heading:hover{background: '+ hoverBackground +'; } #'+Id+'.responsive.simple .tt_tabs li.active h3.tt_heading{background: '+ activeBackground +';  border-bottom-left-radius: 0px; border-bottom-right-radius: 5px}';
                } 
                sel.closest('html').find('head style[data-title="'+ Id +'"]').append(' #'+Id+'.responsive .tt_tabs h3.tt_heading{background: ' + navBackground + ';} #'+ Id +'.responsive .tt_tabs li, #'+ Id +'.responsive .tt_tabs li.active, #'+ Id +'.responsive .tt_tabs li:hover{background: '+ backgroundColor +';} #' + Id + ' .tt_tabs h3.tt_heading:hover{background: ' + hoverBackground + '; }  #' + Id + '.responsive .tt_tabs .tt_tab{color: '+ textColor +'} ' + respStyle);
            }
            sel.find('.tt_tabs .tt_tab').not('.active').slideUp();
            setTimeout(function(){
              if( sel.hasClass('responsive') ){
                sel.find('.tt_tabs li').css('height', 'auto');
                sel.find('.tt_tab').css('width', 'auto').removeClass(animationIn + ' ' + animationOut);
              }
            },100);

        }// reset

        function resize(){ // reset accordion to tab again
            if( !mobile && mode != 'vertical' ){
                tabsWidth = 0;
                currWidth = 0;
                conWidth = 0;
             }
            var activeIndex = tabs.find('li.active').index();
            sel.removeClass('responsive');  
            tabsResponsive = false;
            liReturn();
            tabs.find('li').eq(activeIndex).addClass('active').siblings().removeClass('active');
            container.find('.tt_tab').eq(activeIndex).addClass('active').siblings().removeClass('active');
            if( mobile ){
                tabW = 0;
                tabs.find('li').each(function(){ // loop through navs and add width to variable
                    tabW += parseInt( $(this).outerWidth(true) ); 
                });    
                conWidth = parseInt( container.outerWidth(true) );
            }   
            if( !sel.hasClass('responsive') ){
               var ber = setInterval(function(){
                  if( typeof liReturn === 'function' && !mobile ) {
                      clearInterval(ber);  
                      setTimeout(function(){  
                          tabs.find('li').css({height: oldHgh, verticalAlign: 'bottom'});
                      },200);    
                   } else if( typeof liReturn === 'function' && mobile ){
                         liHeights = tabs.find('li').map(function() {
                            return $(this).outerHeight(true);
                         }).get();
                         setEqual = Math.max.apply(null, liHeights);
                         tabs.find('li').css({height: setEqual, verticalAlign: 'bottom'});
                   }
                },100);
               ber;
            }

        }// resize

         function liReturn(){
                tabs.find('li').each(function(){
                    var h3 = $(this).find('h3.tt_heading');
                    var value = h3.html();
                    $(this).find('.tt_tab').appendTo(container);
                    $(this).html(value).find(h3).remove();
                    tab.css('display', 'block');
                });
         }
       
        return this;

    }; // TurboTabs

}( jQuery ));


/*! fluidvids.js v2.4.1 | (c) 2014 @toddmotto | https://github.com/toddmotto/fluidvids */
!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:e.fluidvids=t()}(this,function(){"use strict";function e(e){return new RegExp("^(https?:)?//(?:"+d.players.join("|")+").*$","i").test(e)}function t(e,t){return parseInt(e,10)/parseInt(t,10)*100+"%"}function i(i){if((e(i.src)||e(i.data))&&!i.getAttribute("data-fluidvids")){var n=document.createElement("div");i.parentNode.insertBefore(n,i),i.className+=(i.className?" ":"")+"fluidvids-item",i.setAttribute("data-fluidvids","loaded"),n.className+="fluidvids",n.style.paddingTop=t(i.height,i.width),n.appendChild(i)}}function n(){var e=document.createElement("div");e.innerHTML="<p>x</p><style>"+o+"</style>",r.appendChild(e.childNodes[1])}var d={selector:["iframe","object"],players:["www.youtube.com","player.vimeo.com"]},o=[".fluidvids {","width: 100%; max-width: 100%; position: relative;","}",".fluidvids-item {","position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;","}"].join(""),r=document.head||document.getElementsByTagName("head")[0];return d.render=function(){for(var e=document.querySelectorAll(d.selector.join()),t=e.length;t--;)i(e[t])},d.init=function(e){for(var t in e)d[t]=e[t];d.render(),n()},d});


jQuery(document).ready(function($){
    // Initializing turbotabs
     $('.turbotabs').each(function(){
         $(this).turbotabs();   
     })

    ///fluid vids initializing   
    fluidvids.init({
      selector: ['iframe', 'object'], // runs querySelectorAll()
      players: ['www.youtube.com', 'player.vimeo.com'] // players to support
    });
});

jQuery(window).load(function(){
  jQuery('.turbotabs .tt_overlay').fadeOut('slow'); // remove preloader when window has finished loading
});