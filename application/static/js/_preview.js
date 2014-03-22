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

        textWithh      = textarea.val().substring(start - attrLength, end);
        textWithhBR    = textarea.val().substring(start - 1, start);
        textWithhBRAfter    = textarea.val().substring(end, end + 1);

        textWithBRList    = textarea.val().substring(start - 2, start);
        textWithBRListAfter    = textarea.val().substring(end, end + 2);

        if(text || type == 'image' || type == 'video') {

            console.log(textWithTag);
            
            // Bold **
            $regex = "";
            if(textWithTag.match("^\\*\\*" + text + "\\*\\*$") && type != 'image' && type != 'video') {
                textarea.val(textarea.val().substring(0, start - 2) + textWithTag.replace(/\*\*/g,'') + textarea.val().substring(end  + 2, len));
                textarea[0].selectionStart = start - 2;
                textarea[0].selectionEnd = end - 2;
                textarea[0].focus();

            // Italic *
            } else if( (textWithTag.match("^\\*"+ text + "\\*$") && ( textWith2.match("^\\s\\*" + text + "\\*\\s$") || textWith2.match("^\\s\\*" + text + "\\*$") || textWith2.match("^\\*" + text + "\\*\\s$") || textWith2.match("^\\*"+ text + "\\*$") ) && type != 'image' && type != 'video') || textWith3.match("^\\*\\*\\*" + text + "\\*\\*\\*$") ) {
                textarea.val(textarea.val().substring(0, start - 1) + textWithTag.replace(/\*/g,'') + textarea.val().substring(end  + 1, len));
                textarea[0].selectionStart = start - 1;
                textarea[0].selectionEnd = end - 1;
                textarea[0].focus();

            // Headings #
            } else if( textWithh.match("^######\\s" + text + "$") ||
                       textWithh.match("^#####\\s" + text + "$") ||
                       textWithh.match("^####\\s" + text + "$") ||
                       textWithh.match("^###\\s" + text + "$") ||
                       textWithh.match("^##\\s" + text + "$") ||
                       textWithh.match("^#\\s" + text + "$")
                    ) {
                switch (attrLength) {
                    case 2:
                    hep = textWithh.replace(/#\s/g,'');
                    break;
                    case 3:
                    hep = textWithh.replace(/##\s/g,'');
                    break;
                    case 4:
                    hep = textWithh.replace(/###\s/g,'');
                    break;
                    case 5:
                    hep = textWithh.replace(/####\s/g,'');
                    break;
                    case 6:
                    hep = textWithh.replace(/#####\s/g,'');
                    break;
                    case 7:
                    hep = textWithh.replace(/######\s/g,'');
                    break;
                }
                
                textarea.val(textarea.val().substring(0, start - attrLength) + hep + textarea.val().substring(end, len));
                textarea[0].selectionStart = start - attrLength;
                textarea[0].selectionEnd = end - attrLength;
                textarea[0].focus();

            // List bullet - 
            } else if(textWithh.match("^-\\s" + text + "$")) {
                console.log('list');
                textarea.val(textarea.val().substring(0, start - attrLength) + textWithh.replace(/-\s/g,'') + textarea.val().substring(end, len));
                textarea[0].selectionStart = start - attrLength;
                textarea[0].selectionEnd = end - attrLength;
                textarea[0].focus();

            // List Numbers 1. 
            } else if(textWithh.match("^1.\\s" + text + "$")) {
                console.log('list');
                textarea.val(textarea.val().substring(0, start - attrLength) + textWithh.replace(/1.\s/g,'') + textarea.val().substring(end, len));
                textarea[0].selectionStart = start - attrLength;
                textarea[0].selectionEnd = end - attrLength;
                textarea[0].focus();

            // Add attribute
            } else if(type == 'URL') {
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
            
            } else {
                var BR = '',
                    BRAfter = '';
                attr_sec = (type == 'double') ? attr : '';
                if(type == 'single') {
                    BR = (textWithhBR != '\n') ? '\n' : '';
                    BRAfter = (textWithhBRAfter != '\n') ? '\n' : '';
                    attrLength = (textWithhBR != '\n') ? attrLength + 1 : attrLength;
                } else if(type == 'list') {
                    BR = (textWithBRList != '\n\n') ? '\n\n' : '';
                    BRAfter = (textWithBRListAfter != '\n\n') ? '\n\n' : '';
                    attrLength = (textWithBRList != '\n\n') ? attrLength + 2 : attrLength;
                }

                textarea.val(textarea.val().substring(0, start) + BR + attr + text + attr_sec + BRAfter + textarea.val().substring(end, len));
                textarea[0].selectionStart = start + attrLength;
                textarea[0].selectionEnd = end + attrLength;
                textarea[0].focus();
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

    // Autoload the preview
    $this = $('textarea');
    $this.each(function(e, input){

        $this = $('#' + input.id);
        $thisVal = $this.val();

        if($thisVal) {
            $this.parent().find('.mightypreview').fadeIn('fast');
        }

        $content = $thisVal;
        Markdown.convert($content, $this);
    });

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
    
    $this = $(this).attr('id');
    // mightyBold
    switch($this) {
        case 'mightyBold':
        MightyTools.replaceIt(textarea, '**', 'double');
        break;

        case 'mightyItalic':
        MightyTools.replaceIt(textarea, '*',  'double');
        break;

        case 'mightyH1':
        MightyTools.replaceIt(textarea, '# ', 'single');
        break;

        case 'mightyH2':
        MightyTools.replaceIt(textarea, '## ', 'single');
        break;

        case 'mightyH3':
        MightyTools.replaceIt(textarea, '### ', 'single');
        break;

        case 'mightyH4':
        MightyTools.replaceIt(textarea, '#### ', 'single');
        break;

        case 'mightyH5':
        MightyTools.replaceIt(textarea, '##### ', 'single');
        break;

        case 'mightyH6':
        MightyTools.replaceIt(textarea, '###### ', 'single');
        break;

        case 'mightyBullet':
        MightyTools.replaceIt(textarea, '- ', 'list');
        break;

        case 'mightyNumbers':
        MightyTools.replaceIt(textarea, '1. ', 'list');
        break;

        case 'mightyLink':
        MightyTools.replaceIt(textarea, 'prompURL', 'URL');
        break;

        case 'mightyImage':
        MightyTools.replaceIt(textarea, 'prompURL', 'image');
        break;

        case 'mightyVideo':
        MightyTools.replaceIt(textarea, 'prompURL', 'video');
        break;


    }

    Markdown.convert(textarea.val(), textarea);
});

// ======== Preview Markdown