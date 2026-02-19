<script>
    $("#{{$id}}").on("change", function (e) {
        var filesize = 1;
        var files = e.currentTarget.files; // puts all files into an array

        // call them as such; files[0].size will get you the file size of the 0th file
        for (var x in files) {
            filesize = ((files[x].size / 1024) / 1024).toFixed(4); // MB
            if (filesize > {{$max_file_size}}) {

                alert("حداکثر اندازه فایل باید {{$max_file_size}} مگابایت باشد.");
                $("#{{$id}}").val('');
            }

        }

    });
</script>
