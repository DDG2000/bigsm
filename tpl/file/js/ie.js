;(function ($, undefined) {
    $(function () {
        
        function saveit(src) {
            download.document.location = src;
            savepic();
        }
        function savepic() {
            if (download.document.readyState == "complete") {
                download.document.execCommand("saveas");
            } else {
                window.setTimeout("savepic()", 10);
            }
        }

        $("a.ie-download").click(function () {
            saveit($(this).attr("href"));
            
            return false;
        })

    })


})(jQuery);