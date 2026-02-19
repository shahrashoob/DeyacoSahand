<pre  id="pre{{$object->id}}" style="font-size: 16px; text-align: left;direction: ltr; background: #bbb3b3" ></pre>
<script>
    var data = @php echo ($object->data); @endphp;
    document.getElementById("pre{{$object->id}}").innerHTML = JSON.stringify(data, null, 4);
</script>
