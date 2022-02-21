<html>
  <head>
    <meta http-equiv="Refresh" content="1">
    <title>温度分布</title>
    <style>

    </style>
    <script type = "text/javascript" language = "javascript">
      function rgb2hex ( rgb ) {
          return "#" + rgb.map( function ( value ) {
          return ( "0" + value.toString( 16 ) ).slice( -2 ) ;
          } ).join( "" ) ;
      }
      
      window.onload = function(){
      var max = document.getElementById("1_1").innerText;
      var max_id = "1_1";
      for(var i = 1; i < 9; i++){
			 for(var j = 1; j < 9; j++){
					    var id = i + "_" + j;
					    var value = document.getElementById(id).innerText;
					    if(max < value){
						     max = value;
						     max_id = id;
						     }
					    var r = 255;
					    var g = Math.floor(value /50.0 * -255 + 255);
					    var b = 0;
					    if(g > 255) g = 255; 
			                    if(g < 0) g = 0 ;

					    if(value<15){
						r=0; g=255;
					    }else if(value<=20){
						r=0; g=255;
					    }else if(value<=25){
						r=Math.floor((value-20) * 25);
					    }else if(value<=30){
						r=Math.floor((value-25) * 25 + 128);
					    }
					    var color = rgb2hex([r, g, b]);
					    document.getElementById(id).style.backgroundColor = color;
			  }
	}
	for(var i = 1; i < 9; i++){
		for(var j = 1; j < 9; j++){
			var id = i + "_" + j;
			var value = document.getElementById(id).innerText;
			if(value == max){
				var color = rgb2hex([255, 0, 0]);
				document.getElementById(id).style.backgroundColor = color;
				   }
		}
	}
      };
      </script>
  </head>
  <body>
    <?php
       try {
       $pdo = new PDO('mysql:host=localhost;dbname=sensor;charset=utf8','root','1qaz2wsx',
       array(PDO::ATTR_EMULATE_PREPARES => false));
    }catch (PDOException $e) {
    exit('データベース接続失敗。'.$e->getMessage());
    }
    
    $stmt = $pdo->query("SELECT * FROM grideye WHERE id = (select max(id) from grideye)");
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    for($i=1;$i<=8;$i++){
		  for($j=1;$j<=8;$j++){
				$data_text = "data_".$i."_".$j;
				$temp[$j][$i] = $row[$data_text];
    		  }
	}
	$time = $row["datetime"];			
    }
				
	echo "<TABLE border='1'>";
	$text = "";
	for($i=8;$i>=1;$i--){
		      $text .= "<tr>";			      
		      for($j=1;$j<=8;$j++){
				    $text .= "<td height=50 align='center' id=".$j."_".$i.">".$temp[$i][$j]."</td>";
		      }
		      $text .= "</tr>";
        }
				    
	$text .= "</TABLE>";     			    
	echo $text;
	echo "<br />time : ".$time;			    
				    
				
?>
</body>
</html>
