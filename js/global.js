$(document).ready(function(){

    if(window.location.hash) {
        var hash = window.location.hash;
        var segs = hash.split('/');
        if($(segs[0]).length) {
            $('ul.lines').scrollTo(segs[0]);
        }
        if(segs.length > 1) {
            if(segs[1].substr(0,1) == 'h') {
                $(segs[1].substr(1).split(',')).each(function(i,id){
                    $('#'+id).toggleClass('selected');
                });
            }
            $('#urlnav li.permalink a').attr('href',hash);
        }
    }

    $('ul.lines li').hover(function(){
        $(this).toggleClass('hover');
    });
    $('ul.lines li').click(function(e){
        if($(e.target).is("a")){ return; }
        $(this).toggleClass('selected');
        var all_selected = $(this).parent().find('.selected');
        if(all_selected.length) {
            var sel_ids = [];
            all_selected.each(function(i,el){
                sel_ids.push($(el).attr('id'));
            });
            var hash = sel_ids[0]+'/h'+sel_ids.join(',');
            window.location.hash = hash;
            $('#urlnav li.permalink a').attr('href','#'+hash);
        } else {
            window.location.hash = 'none';
        }
    });
    $('#urlnav li.clear a').click(function(e){
        $('ul.lines li.selected').removeClass('selected');
        window.location.hash = 'none';
        $('#urlnav li.permalink a').attr('href','#none');
    });
    $('#urlnav li.permalink a').click(function(e){
        window.location = $(this).attr('href');
    });
    $('#urlnav li.top a, #urlnav li.bottom a').click(function(e){
        e.preventDefault();
        $('ul.lines').scrollTo($(this).attr('href'));
    });

});