/**
 * SlideDeck 3 Professional for WordPress Admin JavaScript
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 3 Pro for WordPress
 * 
 * @author SlideDeck
 */

/*
Copyright 2012 HBWSL  (email : support@hbwsl.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * SlideDeck 3 Professional for WordPress Admin JavaScript
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 3 Pro for WordPress
 * 
 * @author SlideDeck
 */

/*
Copyright 2012 HBWSL  (email : support@hbwsl.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/

function sdhtmlvideo(e){"sdhtml5"==e?(jQuery(".slide-content-fields").show(),jQuery(".uploadsdvideo").show(),jQuery(".change-media-src").hide(),jQuery("#youtubetext").val(""),jQuery("#video-url-input").hide()):(jQuery(".slide-content-fields").show(),jQuery(".uploadsdvideo").hide(),jQuery(".change-media-src").show(),jQuery("#video-url-input").show())}!function(e,i,t){SlideDeckPlugin.CustomCSSEditor={textarea:null,initialize:function(){var i=this;i.textarea=e("#custom-slidedeck-css").find("textarea"),i.textarea.length&&(this.editor=CodeMirror.fromTextArea(i.textarea[0],{lineNumbers:!0,mode:"css",theme:"slidedeck",readOnly:!1,indentUnit:4,tabSize:4,lineWrapping:!0,onCursorActivity:function(e){e.save(),SlideDeckPlugin.CustomCSSEditor.editor.setLineClass(SlideDeckPlugin.CustomCSSEditor.line,null),SlideDeckPlugin.CustomCSSEditor.line=SlideDeckPlugin.CustomCSSEditor.editor.setLineClass(SlideDeckPlugin.CustomCSSEditor.editor.getCursor().line,"activeline")},onChange:function(e){i.textarea.sliderTimer&&clearTimeout(i.textarea.sliderTimer),i.textarea.sliderTimer=setTimeout(function(){SlideDeckPreview.ajaxUpdate()},990)}}),this.line=this.editor.setLineClass(0,"activeline"))}},e(document).ready(function(){SlideDeckPlugin.CustomCSSEditor.initialize()})}(jQuery,window,null);
