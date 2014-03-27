<?php require_once('../../../wp-load.php');
global $data;
header('Content-type: text/css');
?>
* {margin: 0; padding: 0; outline: none;}

body {
background: <?php echo $data['bbit_background']; ?> url(<?php echo $data['bbit_custom_bg']; ?>);
font-family : arial;
font-style : normal;
font-size : 12px;
color : #595959;
line-height : 16px;
position : relative;
<?php if ( $data['bbit_protected'] == 1 ): ?>
-webkit-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
-o-user-select: none;
<?php endif; ?>
}

h1, h2, h3, h4, h5, h6 {font-size: small;}

.clearfix {clear: both;}
.pad,.tagcloud {padding: 7px;}
.lineH20 {line-height: 20px;}
.bderTop {border-top: 1px solid #e2e2e2;}
.left {float: left;}
.right {float: right;}
.center {text-align: center;}

a.post_title {color: #666;}
a.post_title:hover {color: #2CAAD3; text-decoration: none;}
a {text-decoration: none; color: #2CAAD3;}
a:hover {text-decoration: underline;}

#body {
display : block;
background : #FFF;
max-width : 650px;
min-width : 200px;
margin : auto;
position : relative;
box-shadow : 0 7px 50px rgba(0, 0, 0, 0.3);
}

header {
background : url(assets/images/header/header.png) top left repeat-x #99d6f9;
position : relative;
text-align : center;
padding : 20px 0 15px 10px;
color : #fff;
}

#Menu {background : url(assets/images/bar/bar.png) right no-repeat #2CAAD3; position : relative;}
#Menu li, #FooterMenu li{display: inline-block;}
#Menu a, #Menu a:hover{color : #FFF; display : block; padding : 10px;}
#Menu a:hover {background : #39C1EE; text-decoration : none;}

#breadcrumbs, .title{font-weight: 700; color: #fff; background: #2CAAD3; border-bottom: 3px solid #39C1EE; padding: 8px; cursor: default; display: block;}
#breadcrumbs a {color: #FFF;}

#content p {margin-bottom: 10px;}
#content img {max-width: 100%; height: auto; display: block; margin-left: auto; margin-right: auto;}
#content ul, ol {margin: 0 20px;}

article:nth-child(odd) {position: relative; border-bottom: 1px solid #e2e2e2; padding: 7px; clear: both;}
article:nth-child(even) {position: relative; border-bottom: 1px solid #e2e2e2; padding: 7px; clear: both; background-color: #fcfcfc;}
article:hover {box-shadow: 0 5px 20px rgba(0, 0, 0, 0.20);}
article p {max-height: 36px; display: block; overflow: hidden; line-height: 18px;}
.photo {width: 55px; float: left;margin: 0 8px 0 0; height: 55px; border-radius: 19%;}
.format-image img {width: 100%;}
.format-image h2 {margin: 5px 0;}

.prefix {background: hsl(127, 39%, 55%); color: hsl(0, 100%, 100%); border-radius: 2px; padding: 1px 2px;}
.hreview-aggregate {background: #f6f6f6; overflow: hidden;}
.hreview-aggregate label {font-weight: 700;}

.count {font-style: italic; font-size: smaller;}
.Tag a{background: hsl(0, 100%, 73%); color: hsl(0, 100%, 100%); border-radius: 2px; padding: 1px 2px; margin: 2px;}
.banner {border: 1px dashed #11B6CC; background: #EEF9FD; padding: 5px;}

.pagenavi {text-align: center; padding : 10px; clear : both;}
.pagenavi a, .pagenavi a:hover, .pagenavi .NowPage {color: #636363; padding: 2px 6px; margin-right: 1px;}
.pagenavi a:hover, .pagenavi .NowPage {color: #FFF; background: #2CAAD3; text-decoration: none;}

.downloadfree{margin-top: 5px; background: #61c357; line-height: 28px; color: #FFF; font-weight: 700; float: left; padding: 0 15px 0 15px;}
.downloadfree:hover {background: #1EB353; text-decoration : none;}
.download {display: inline-block; border: #6DBD44 solid 1px; margin-bottom: 5px;}
.download ul {margin-left: 4px !important;}
.dl_menu {font-weight: 700; color: #FFF; background: #85ba40; padding-left: 5px; line-height: 28px;}
.download .item {list-style: none; border-bottom: 1px solid #E2E2E2; padding: 4px 10px; background: url(assets/images/item.png) left 8px no-repeat; color: #2CAAD3;}

input, textarea {width: 80%; padding: 3px 0 3px 0; margin-bottom: 2px;}
button {background: #61c357; padding: 5px 10px; border: 0; color: #fff; cursor: pointer;width: 15%;}
button:hover, button:focus {background: #1EB353;}

.icon_view {background: url(assets/images/icon_view.png) no-repeat center left; padding-left: 20px; font-size: 11px; margin-right: 4px;}
.icon_comment {background: url(assets/images/icon_comment.png) no-repeat center left; padding-left: 20px; font-size: 11px; margin-right: 4px;}

.icon_download, .icon_play {padding-left: 13px; font-size: 11px; margin-right: 4px;}
.icon_download {background: url(assets/images/icon_download.png) no-repeat center left; font-size: 12px;}
.icon_play {background: url(assets/images/item.png) no-repeat center left;}

.ribbon_new {
background: url(assets/images/ribbon_new.png) no-repeat scroll right top / 35px auto transparent;
display: block;
height: 35px;
width: 35px;
position: absolute;
right: 0;
top: 0;
}

#FooterMenu {background : #253444; position : relative;}
#FooterMenu a, #FooterMenu a:hover{color : #FFF; display : block; padding : 10px;}
#FooterMenu a:hover{background : #39C1EE; text-decoration : none;}

footer {
text-align : center;
color : #fff;
background : #99d6f9 url(assets/images/header/header.png) top left repeat-x;
}

footer a {color: #fff;}
footer img {padding: 5px;}

@media screen and (min-width: 651px) {
    .nav_menu {margin: 0 0 8px; border-top: 0;}

    .nav_menu ul li {
    color : #f4f4f4;
    font-weight : 400;
    font-size : 13px;
    padding : 8px 35px 8px 8px;
    list-style : none;
    text-decoration : none;
    border : #e2e2e2 solid 1px;
    border-top : 0;
    }

    .nav_menu a {color: #666;}
	
    #close {display: none!important}
	
    #open {display: none!important}
}
<?php if ( $data['bbit_mobile_navi'] == 1 ) : ?>
@media only screen and (min-width: 251px) and (max-width: 650px) {
  #nav .title {background: rgba(44, 170, 211, 0)!important;}
  
  #nav li a {
  display: block;
  color: #ccc;
  font-size: 0.875em;
  line-height: 1.28571em;
  font-weight: bold;
  outline: none;
  }

  #nav{position: absolute; top: 0; padding-top: 5.25em;}
  #nav:not(:target) {z-index: 1; height: 0;}
  #nav li {position: relative; border-top: 1px solid rgba(255, 255, 255, 0.1);}
  #nav li:last-child {border-bottom: 1px solid rgba(255, 255, 255, 0.1);}
  #nav li :hover {background: rgba(255, 255, 255, 0.1); text-decoration: none;}

  #nav li :hover:after {
    z-index: 50;
    display: block;
    content: "";
    position: absolute;
    top: 50%;
    right: -0.03125em;
    margin-top: -0.625em;
    border-top: 0.625em transparent solid;
    border-bottom: 0.625em transparent solid;
    border-right: 0.625em white solid;
  }
  
  #nav li a {padding: 8px;}

  .js-ready #nav{
    height: 100%;
    width: 70%;
    background: #333;
    box-shadow: inset -1.5em 0 1.5em -0.75em rgba(0, 0, 0, 0.25);
  }

  .js-ready #nav{left: -70%;}
  .js-ready #body {left: 0;}
  .js-nav #body {left: 70%;}

  .csstransforms3d.csstransitions.js-ready #nav{
    left: 0;
    -webkit-transform: translate3d(-100%, 0, 0);
    -moz-transform: translate3d(-100%, 0, 0);
    -ms-transform: translate3d(-100%, 0, 0);
    -o-transform: translate3d(-100%, 0, 0);
    transform: translate3d(-100%, 0, 0);
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    -ms-backface-visibility: hidden;
    -o-backface-visibility: hidden;
    backface-visibility: hidden;
  }
  
  .csstransforms3d.csstransitions.js-ready #body {
    left: 0 !important;
    transform: translate3d(0, 0, 0);
    -webkit-transition: -webkit-transform 500ms ease;
    -moz-transition: -moz-transform 500ms ease;
    -o-transition: -o-transform 500ms ease;
    transition: transform 500ms ease;
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    -ms-backface-visibility: hidden;
    -o-backface-visibility: hidden;
    backface-visibility: hidden;
  }

  .csstransforms3d.csstransitions.js-nav #body {
    -webkit-transform: translate3d(70%, 0, 0) scale3d(1, 1, 1);
    -moz-transform: translate3d(70%, 0, 0) scale3d(1, 1, 1);
    -ms-transform: translate3d(70%, 0, 0) scale3d(1, 1, 1);
    -o-transform: translate3d(70%, 0, 0) scale3d(1, 1, 1);
    transform: translate3d(70%, 0, 0) scale3d(1, 1, 1);
  }
  
  #close {padding:50px}
}
<?php endif; ?>
@media only screen and (max-width: 250px) {

    .nav_menu ul li {
    color : #f4f4f4;
    font-weight : 400;
    font-size : 13px;
    padding : 8px 35px 8px 8px;
    list-style : none;
    text-decoration : none;
    border : #e2e2e2 solid 1px;
    border-top : 0;
    }
	
    .nav_menu {margin: 0 0 8px; border-top: 0;}
    .nav_menu a {color: #666;}
    header {background : #99d6f9;}
    .title {background: #2CAAD3; padding: 4px;}
	#Menu a, #Menu a:hover, #FooterMenu a, #FooterMenu a:hover{padding: 6px;}
	footer {background: #99d6f9;}
    #close, #open {display: none;}
}

<?php echo $data['bbit_custom_css']; ?>