<h2>{{ $simulation_name }}</h2>
<h2>URLs</h2>
<?php 
foreach($tokens as $key => $token)
{
    $i=$key+1;
    echo "<p><strong>Terminal".$i.": </strong>".$url.'/'.$token->token."</p>";
}
?>

<script type="text/javascript">
    window.onload = function() { window.print(); }
</script>
