<html>
<head>
<title>How to Export HTML to Word Document with JavaScript</title>

</head>
<body>
<div class="source-html-outer">
    <div id="source-html">
            <?php 
            $simulation_name="";
            ?>
        
            @foreach($report_data as $data)
        
            
            <?php 
            $i=0; 
            ?>
            @foreach($data as $value)
            
             @if($simulation_name != $value['chat_users_data']['simulation_data']->simulation_name)
                <h1>{{$value['chat_users_data']['simulation_data']->simulation_name}}</h1>
             @endif
        
            @if($i==0)
            <h3>{{$value['chat_users_data']->name}}</h3>
            @endif
        
            @if( $value['message'] != '')
              <p>{{$value['user_data']->name}} : {{$value['message']}}</p>
             @elseif($value['image'] != '') 
             <p>{{$value['user_data']->name}} :{{$value['upload_data']->name}}</p>
             @elseif($value['video'] != '') 
             <p>{{$value['user_data']->name}} :{{$value['upload_data']->name}}</p>
             @elseif($value['doc'] != '') 
             <p>{{$value['user_data']->name}} :{{$value['upload_data']->name}}</p>
             @else
             <p>{{$value['message']}}</p>
             @endif
        
        
             <?php 
             $i++; 
             $simulation_name=$value['chat_users_data']['simulation_data']->simulation_name;
             ?>
        
             @endforeach
        
            @endforeach
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        exportHTML();
    });
    function exportHTML() {
        var header = '<html xmlns:o="urn:schemas-microsoft-com:office:office"' +
            ' xmlns:w="urn:schemas-microsoft-com:office:word"' +
            ' xmlns="http://www.w3.org/TR/REC-html40"><body>';
        var footer = '</body></html>';
        var sourceHTML = header + document.getElementById("source-html").innerHTML + footer;

        var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
        var fileDownload = document.createElement('a');
        document.body.appendChild(fileDownload);
        fileDownload.href = source;
        fileDownload.download = 'PMS Simulation History.doc';
        fileDownload.click();
        document.body.removeChild(fileDownload);

        // Redirect after export is completed
        setTimeout(function() {
                    location.href = '/simulation_history';
                }, 100); // Adjust the timeout as needed
    }
</script>
</body>
</html>