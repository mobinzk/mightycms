// Copyright 2014 -  @mobinzk

// ======== Preview Markdown
var Markdown = {
  convert: function($content, $this) {
    $content = "content=" + $content;
    $.ajax({
      url: '/mightycms/markdownHTML',
      type: 'post',
      data: $content,
      dataType: 'html',

      success: function(data){
        // console.log(data);
        $preview = $this.parent().find('.mightycontent');
        $($preview).html(data);
      }
    });
  }
};

var MightyTools = {
    // Initialize Preview
    init: function(){
        // Autoload the preview
        $this = $('textarea');
        $this.each(function(e, input){
            $this = $('#' + input.id);

            // Add tools wysiwyg
            /*jshint multistr: true */
            $this.before('<ul class="mighty-editor-tools"> \n\
                                    <li id="mightyBold" data-attr="**" data-type="bold" title="Bold"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                    <li id="mightyItalic" data-attr="*" data-type="italic" title="Italic"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                    <li id="mightyH"> \n\
                                        <span></span> \n\
                                        <ul> \n\
                                            <li id="mightyH1" data-attr="\n\n# " data-type="h1" title="Heading 1">H1</li> \n\
                                            <li id="mightyH2" data-attr="\n\n## " data-type="h2" title="Heading 2">H2</li> \n\
                                            <li id="mightyH3" data-attr="\n\n### " data-type="h3" title="Heading 3">H3</li> \n\
                                            <li id="mightyH4" data-attr="\n\n#### " data-type="h4" title="Heading 4">H4</li> \n\
                                            <li id="mightyH5" data-attr="\n\n##### " data-type="h5" title="Heading 5">H5</li> \n\
                                            <li id="mightyH6" data-attr="\n\n###### " data-type="h6" title="Heading 6">H6</li> \n\
                                        </ul> \n\
                                    </li> \n\
                                    <li id="mightyBullet" data-attr="\n\n- " data-type="bullet" title="Unordered list"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                    <li id="mightyNumbers" data-attr="\n\n1. " data-type="number" title="Ordered list"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                    <li id="mightyLink" data-attr="" data-type="url" title="Link"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                    <li id="mightyImage" data-attr="" data-type="image" title="Image"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                    <li id="mightyVideo" data-attr="" data-type="video" title="video"> \n\
                                        <span></span> \n\
                                    </li> \n\
                                </ul>');
            
            // Add mighty Preview
            $height = $this.outerHeight();
            $this.after('<div class="mightypreview"> \n\
                                    <label for="">Live preview</label> \n\
                                    <div style="height:' + $height + 'px" class="mightycontent"> \n\
                                    </div> \n\
                                </div>');

            // Add the text to mighty Preview
            $thisVal = $this.val();
            if($thisVal) {
                $this.parent().find('.mightypreview').fadeIn('fast');
            }

            $content = $thisVal;
            Markdown.convert($content, $this);
        });
    },
    // Get highlighted text
    highlightedText: function($textarea) {
          
        var start = 0,
            end = 0,
            normalizedValue,
            range,
            textInputRange,
            len,
            endRange;

            if (typeof $textarea[0].selectionStart == "number" && typeof $textarea[0].selectionEnd == "number") {
                start = $textarea[0].selectionStart;
                end = $textarea[0].selectionEnd;
            } else {
                range = document.selection.createRange();

                if (range && range.parentElement() == el) {
                    len = $textarea[0].value.length;
                    normalizedValue = $textarea[0].value.replace(/\r\n/g, "\n");

                    // Create a working TextRange that lives only in the input
                    textInputRange = $textarea[0].createTextRange();
                    textInputRange.moveToBookmark(range.getBookmark());

                    // Check if the start and end of the selection are at the very end
                    // of the input, since moveStart/moveEnd doesn't return what we want
                    // in those cases
                    endRange = $textarea[0].createTextRange();
                    endRange.collapse(false);

                    if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                        start = end = len;
                    } else {
                        start = -textInputRange.moveStart("character", -len);
                        start += normalizedValue.slice(0, start).split("\n").length - 1;

                        if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                            end = len;
                        } else {
                            end = -textInputRange.moveEnd("character", -len);
                            end += normalizedValue.slice(0, end).split("\n").length - 1;
                        }
                    }
                }
            }

        return { start: start, end: end, text: $textarea.val().substring(start, end) };

    },

    // From https://github.com/jamiebicknell/Markdown-Helper
    MarkdownHelper: function (block,event) {

        if(event.keyCode=='13') {
            check = false;
            input = block.value.replace(/\r\n/g,'\n');
            if(block.selectionStart) {
                start = block.selectionStart;
            }
            else {
                block.focus();
                range = document.selection.createRange();
                range.moveStart('character',-input.length);
                start = range.text.replace(/\r\n/g,'\n').length;
            }
            lines = input.split('\n');
            state = input.substr(0,start).split('\n').length;
            value = lines[state-1].replace(/^\s+/,'');
            first = value.substr(0,2);
            if(new RegExp('^[0-9]+[\\.] (.*)$').test(value)) {
                prior = value.substr(0,value.indexOf('. '));
                begin = prior + '. ';
                label = String(eval(prior)+1) + '. ';
                check = true;
            }
            if(value&&!check&&lines[state-1].substr(0,4)=='    ') {
                begin = label = '    ';
                check = true;
            }
            if(['* ','+ ','- ','> '].indexOf(first)>=0) {
                begin = label = first;
                check = true;
            }
            if(check) {
                width = lines[state-1].indexOf(begin);
                if(value.replace(/^\s+/,'')==begin) {
                    block.value = input.substr(0,start-1-width-label.length) + '\n\n' + input.substr(start,input.length);
                    caret = start+1-label.length-width;
                }
                else {
                    block.value = input.substr(0,start) + '\n' + (new Array(width+1).join(' ')) + label + input.substr(start,input.length);
                    caret = start+1+label.length+width;
                }
                if(block.selectionStart) {
                    block.setSelectionRange(caret,caret);
                }
                else {
                    var range = block.createTextRange();
                    range.move('character',caret);
                    range.select();
                }
                return false;
            }
        }
    },
    replaceIt: function(textarea, attr, type) {

        // <:type> is "single", "double", "list", "image", "video", "URL"
        
        highlighed = MightyTools.highlightedText(textarea);
        text = highlighed.text.trim();
        start = highlighed.start;
        end = highlighed.end;
        len = textarea.val().length;
        attrLength = attr.length;

        textWithTag = textarea.val().substring(start - attrLength, end + attrLength);
        textWith2   = textarea.val().substring(start - 2, end + 2);
        textWith3   = textarea.val().substring(start - 3, end + 3);

        textWit4first  = textarea.val().substring(start - attrLength, end);
        textWit4first2After  = textarea.val().substring(start - attrLength, end + 2);

        textWithhBR    = textarea.val().substring(start - 1, start);
        textWithhBRAfter    = textarea.val().substring(end, end + 1);

        textWithBRListBefore   = textarea.val().substring(start - 2, start);
        textWithBRListAfter    = textarea.val().substring(end, end + 2);
        
        if(type == 'bold') {
            if(RegExp('^\\*\\*[^]+\\*\\*$').test(textWithTag) ||
               RegExp('^\\*\\*\\*\\*$').test(textWithTag)) {
                textarea.val(textarea.val().substring(0, start - 2) + textWithTag.substring(2, textWithTag.length-2) + textarea.val().substring(end  + 2, len));
                textarea[0].selectionStart = start - 2;
                textarea[0].selectionEnd = end - 2;
                
            } else {
                textarea.val(textarea.val().substring(0, start) + attr + text + attr + textarea.val().substring(end, len));
                textarea[0].selectionStart = start + attrLength;
                textarea[0].selectionEnd = end + attrLength;
            }

            textarea[0].focus();

        } else if(type == 'italic') {
            if( (RegExp('^\\*[^]+\\*$').test(textWithTag) &&
                 !RegExp('^\\*\\*[^]+\\*\\*$').test(textWith2) ) ||
                 RegExp('^\\*\\*$').test(textWithTag) ||

                 RegExp('^\\*\\*\\*[^]+\\*\\*\\*$').test(textWith3)) {
                textarea.val(textarea.val().substring(0, start - 1) + textWithTag.substring(1, textWithTag.length-1) + textarea.val().substring(end  + 1, len));
                textarea[0].selectionStart = start - 1;
                textarea[0].selectionEnd = end - 1;
                
            } else {
                textarea.val(textarea.val().substring(0, start) + attr + text + attr + textarea.val().substring(end, len));
                textarea[0].selectionStart = start + attrLength;
                textarea[0].selectionEnd = end + attrLength;
            }

            textarea[0].focus();

        } else if(type == 'h1') {
                if(RegExp('^\n\n#\\s[^]+$').test(textWit4first) || RegExp('^\n\n#\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(4, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 4;
                    textarea[0].selectionEnd = end - 4;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 4;
                    textarea[0].selectionEnd = end + 4;
                }

        } else if(type == 'h2') {
                if(RegExp('^\n\n##\\s[^]+$').test(textWit4first) || RegExp('^\n\n##\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(5, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 5;
                    textarea[0].selectionEnd = end - 5;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 5;
                    textarea[0].selectionEnd = end + 5;
                }

        } else if(type == 'h3') {
                if(RegExp('^\n\n###\\s[^]+$').test(textWit4first) || RegExp('^\n\n###\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(6, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 6;
                    textarea[0].selectionEnd = end - 6;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 6;
                    textarea[0].selectionEnd = end + 6;
                }

        } else if(type == 'h4') {
                if(RegExp('^\n\n####\\s[^]+$').test(textWit4first) || RegExp('^\n\n####\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(7, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 7;
                    textarea[0].selectionEnd = end - 7;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 7;
                    textarea[0].selectionEnd = end + 7;
                }

        } else if(type == 'h5') {
                if(RegExp('^\n\n#####\\s[^]+$').test(textWit4first) || RegExp('^\n\n#####\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(8, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 8;
                    textarea[0].selectionEnd = end - 8;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 8;
                    textarea[0].selectionEnd = end + 8;
                }

        } else if(type == 'h6') {
                if(RegExp('^\n\n######\\s[^]+$').test(textWit4first) || RegExp('^\n\n######\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(9, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 9;
                    textarea[0].selectionEnd = end - 9;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 9;
                    textarea[0].selectionEnd = end + 9;
                }

        } else if(type == 'bullet') {
                if(RegExp('^\n\n-\\s[^]+$').test(textWit4first) || RegExp('^\n\n-\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(4, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 4;
                    textarea[0].selectionEnd = end - 4;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 4;
                    textarea[0].selectionEnd = end + 4;
                }

                textarea[0].focus();

        } else if(type == 'number') {
                if(RegExp('^\n\n1.\\s[^]+$').test(textWit4first) || RegExp('^\n\n1.\\s$').test(textWit4first)) {
                    textarea.val(textarea.val().substring(0, start - attrLength) + textWit4first2After.substring(5, textWit4first2After.length - 2) + textarea.val().substring(end + 2, len));
                    textarea[0].selectionStart = start - 5;
                    textarea[0].selectionEnd = end - 5;
                } else {
                    textarea.val(textarea.val().substring(0, start) + attr + text + '\n\n' + textarea.val().substring(end, len));
                    textarea[0].selectionStart = start + 5;
                    textarea[0].selectionEnd = end + 5;
                }

                textarea[0].focus();

        } else if(type == 'url') {
             var prompURL = prompt('Please provide the link URL \n\nThis is a link /page/onyour/website within your website. \n\nThis is a link http://www.google.com outside of your website','');
            if(prompURL) {
                textarea.val(textarea.val().substring(0, start) + '[' + text + ']' + '(' + prompURL + ')' + textarea.val().substring(end, len));
            }
        } else if(type == 'image') {
                var prompImage = prompt('Please provide the image URL','');
                
                if(prompImage) {
                    var prompText = prompt('Please provide the image title (It\'s best to describe the image)','');
                    var prompPosition = prompt('Please provide the image position \n\n(write left or right)','');

                    prompPosition = (prompPosition) ? '{.' + prompPosition + '}' : '';
                    prompText     = (prompText) ? prompText : '';
                    textarea.val(textarea.val().substring(0, start) + '![' + prompText + ']' + '(' + prompImage + ')' + prompPosition + textarea.val().substring(end, len));
                }

        } else if(type == 'video') {
            var prompVideo = prompt('Please provide the video URL from YouTube or Vimeo','');
            var $video;
            
            if(prompVideo) {
                $video = MightyTools.mediaExtractor(prompVideo);
            }
            
            if($video) {

                if($video.type == 'youtube') {
                    textarea.val(textarea.val().substring(0, start) + '<iframe width="560" height="315" src="//www.youtube.com/embed/'+ $video.id +'?rel=0" frameborder="0" allowfullscreen></iframe>' + textarea.val().substring(end, len));
                } else if($video.type == 'vimeo') {
                    textarea.val(textarea.val().substring(0, start) + '<iframe src="//player.vimeo.com/video/'+ $video.id +'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="552" height="311" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' + textarea.val().substring(end, len));
                }
            }
        
        }
    },
    // From http://stackoverflow.com/questions/3452546/javascript-regex-how-to-get-youtube-video-id-from-url
    mediaExtractor: function(pastedData) {
        var success = false;
        var media   = {};
        
        if (pastedData.match('(http(s)?://)?(www.)?youtube|youtu\.be')) {
            if (pastedData.match('embed')) { youtube_id = pastedData.split(/embed\//)[1].split('"')[0]; }
            else { youtube_id = pastedData.split(/v\/|v=|youtu\.be\//)[1].split(/[?&]/)[0]; }
            media.type  = "youtube";
            media.id    = youtube_id;
            success = true;
        } else if (pastedData.match('(http(s)?://)?(player.)?vimeo\.com')) {
            vimeo_id = pastedData.split(/video\/|http:\/\/vimeo\.com\//)[1].split(/[?&]/)[0];
            media.type  = "vimeo";
            media.id    = vimeo_id;
            success = true;
        }
        
        if (success) {
            return media;
        } else {
            alert("No valid media id detected");
        }
        return false;
    }
};

$(function(){

    MightyTools.init();

});

// Change preview on keyup
$('html').on('keyup', 'textarea',function(e) {
    $(this).parent().find('.mightypreview').fadeIn('fast');
    $content = escape($(this).val());
    Markdown.convert($content, $(this));
});

// Sort out lists
$('html').on('keypress', 'textarea',function(e) {
    return MightyTools.MarkdownHelper($(this)[0],e);
});


// Scroll at the same time
$('textarea').scroll(function(){
    $this = $(this);
    $preview = $this.parent().find('.mightycontent');
    $preview.scrollTop($this.scrollTop());
});
$('.mightycontent').scroll(function(){
    $this = $(this);
    $preview = $this.parent().parent().find('textarea');
    $preview.scrollTop($this.scrollTop());
});

// Change size on click
$('.uim-form li textarea').click( function(e) {
    $this = $(this);
    $height = $this.outerHeight();
    
    $preview = $this.parent().find('.mightycontent');
    $preview.css('height', $height);
});

// Mighty tools clicked
$('html').on('click', '.mighty-editor-tools li', function(e){
    textarea = $(this).parent().parent().find('textarea');
    if(textarea.length === 0) {
        textarea = $(this).parent().parent().parent().parent().find('textarea');
    }
    
    $this = $(this);
    $attr = $this.attr('data-attr');
    $type = $this.attr('data-type');
    
    MightyTools.replaceIt(textarea, $attr, $type);

    Markdown.convert(textarea.val(), textarea);
});

// ======== Preview Markdown