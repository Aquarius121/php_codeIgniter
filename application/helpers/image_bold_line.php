<?php 

function imageBoldLine($resource, $x1, $y1, $x2, $y2, $Color, $BoldNess=2, $func='imageLine')
{
  $center = round($BoldNess/2);
  for($i=0;$i<$BoldNess;$i++)
  { 
    $a = $center-$i; if($a<0){$a -= $a;}
    for($j=0;$j<$BoldNess;$j++)
    {
     $b = $center-$j; if($b<0){$b -= $b;}
     $c = sqrt($a*$a + $b*$b);
     if($c<=$BoldNess)
     {
      $func($resource, $x1 +$i, $y1+$j, $x2 +$i, $y2+$j, $Color);
     }
    }
  }        
} 