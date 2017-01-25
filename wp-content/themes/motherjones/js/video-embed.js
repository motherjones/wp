var mj_fix_embed = function(embed) {
    if (embed.attr('width').match('%')) {
      embed.attr('height', 'auto');
      return;
    } else if (embed.attr('height').match('%')) {
      return;
    }
    var div = jQuery('<div class="responsive_video_embed_container"></div>');
    var padding_bottom = parseInt(embed.attr('height')) 
        / parseInt(embed.attr('width')) 
        * 100;
    embed.attr('style', '');
    div.css('padding-bottom', padding_bottom + '%')
    embed.before(div);
    div.append(embed);
}

jQuery(document).ready(function() {
    jQuery('iframe, object, embed').each(function() {
        var $this = jQuery(this);
        var url = $this.attr('src') || $this.html();
        if (
               url.match(/youtu/)
            || url.match(/appspot/)
            || url.match(/vimeo/)
            || url.match(/msnbc/)
            || url.match(/livestream/)
            || url.match(/facebook.com\/plugins\/video.php/)
        ) {
            mj_fix_embed($this);
        }
    });
});
