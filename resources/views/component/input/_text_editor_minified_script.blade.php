<!--  -->
<script src="{{asset('assets/plugins/minified/sceditor.min.js')}}"></script>
<script src="{{asset('assets/plugins/minified/icons/monocons.js')}}"></script>
<script src="{{asset('assets/plugins/minified/formats/bbcode.js')}}"></script>
<!-- / -->
<script>
    var toolbar = 'bold,italic,underline,strike|subscript,superscript|left,center,right,justify|font,size,color,removeformat|cut,copy,paste,pastetext|bulletlist,orderedlist|table,code,quote,horizontalrule|email,link,unlink|date,time|ltr,rtl|print,maximize,source';
    var textarea1 = document.getElementById('{{$id}}');
    sceditor.create(textarea1, {
        format: 'xhtml',
        toolbar: toolbar
    });
</script>
