<?php 

/**
* function imageSmoothAlphaLine() - version 1.0
* Draws a smooth line with alpha-functionality
*
* @param   ident    the image to draw on
* @param   integer  x1
* @param   integer  y1
* @param   integer  x2
* @param   integer  y2
* @param   integer  red (0 to 255)
* @param   integer  green (0 to 255)
* @param   integer  blue (0 to 255)
* @param   integer  alpha (0 to 127)
*
* @access  public
*
* @author  DASPRiD <d@sprid.de>
*/
function imagealphaline($image, $x1, $y1, $x2, $y2, $dcol) {
  
  $col_comps = imagecolorsforindex($image, $dcol);
  $r = $col_comps['red'];
  $g = $col_comps['green'];
  $b = $col_comps['blue'];
  $alpha = $col_comps['alpha'];

  $icr = $r;
  $icg = $g;
  $icb = $b;
 
  if ($y1 == $y2 || $x1 == $x2)
    imageline($image, $x1, $y2, $x1, $y2, $dcol);
  else {
    $m = ($y2 - $y1) / ($x2 - $x1);
    $b = $y1 - $m * $x1;

    if (abs ($m) <2) {
      $x = min($x1, $x2);
      $endx = max($x1, $x2) + 1;

      while ($x < $endx) {
        $y = $m * $x + $b;
        $ya = ($y == floor($y) ? 1: $y - floor($y));
        $yb = ceil($y) - $y;
  
        $trgb = ImageColorAt($image, $x, floor($y));
        $tcr = ($trgb >> 16) & 0xFF;
        $tcg = ($trgb >> 8) & 0xFF;
        $tcb = $trgb & 0xFF;
        imagesetpixel($image, $x, floor($y), imagecolorallocatealpha($image, ($tcr * $ya + $icr * $yb), ($tcg * $ya + $icg * $yb), ($tcb * $ya + $icb * $yb), $alpha));
 
        $trgb = ImageColorAt($image, $x, ceil($y));
        $tcr = ($trgb >> 16) & 0xFF;
        $tcg = ($trgb >> 8) & 0xFF;
        $tcb = $trgb & 0xFF;
        imagesetpixel($image, $x, ceil($y), imagecolorallocatealpha($image, ($tcr * $yb + $icr * $ya), ($tcg * $yb + $icg * $ya), ($tcb * $yb + $icb * $ya), $alpha));
 
        $x++;
      }
    } else {
      $y = min($y1, $y2);
      $endy = max($y1, $y2) + 1;

      while ($y < $endy) {
        $x = ($y - $b) / $m;
        $xa = ($x == floor($x) ? 1: $x - floor($x));
        $xb = ceil($x) - $x;
 
        $trgb = ImageColorAt($image, floor($x), $y);
        $tcr = ($trgb >> 16) & 0xFF;
        $tcg = ($trgb >> 8) & 0xFF;
        $tcb = $trgb & 0xFF;
        imagesetpixel($image, floor($x), $y, imagecolorallocatealpha($image, ($tcr * $xa + $icr * $xb), ($tcg * $xa + $icg * $xb), ($tcb * $xa + $icb * $xb), $alpha));
 
        $trgb = ImageColorAt($image, ceil($x), $y);
        $tcr = ($trgb >> 16) & 0xFF;
        $tcg = ($trgb >> 8) & 0xFF;
        $tcb = $trgb & 0xFF;
        imagesetpixel ($image, ceil($x), $y, imagecolorallocatealpha($image, ($tcr * $xb + $icr * $xa), ($tcg * $xb + $icg * $xa), ($tcb * $xb + $icb * $xa), $alpha));
 
        $y ++;
      }
    }
  }
}