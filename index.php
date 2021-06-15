<?php 
set_time_limit(0);

function getPixelGreyScale($image, $x, $y)
{
    $rgb = imagecolorat($image, $x, $y);
    $rr = ($rgb >> 16) & 0xFF;
    $gg = ($rgb >> 8) & 0xFF;
    $bb = $rgb & 0xFF;

    return round(($rr + $gg + $bb) / 3);

}

function convolution($matrix)
{
    $gx = (-1 * $matrix[0][0]) + (-2 * $matrix[0][1]) + (-1 * $matrix[0][2]) + $matrix[2][0] + (2 * $matrix[2][1]) + $matrix[2][2];
    $gy = $matrix[0][0] + (-1 * $matrix[0][2]) + (2 * $matrix[1][0]) + (-2 * $matrix[1][2]) + $matrix[2][0] + (-1 * $matrix[2][2]);

    return sqrt( pow($gx, 2) + pow($gy, 2) );

}

$image = imagecreatefromjpeg("test.jpg");

if($image)
{
    imagepalettetotruecolor($image);
    
    list($width, $height, $type, $attr) = getimagesize("test.jpg");
    $result = imagecreatetruecolor($width, $height);

    for($i=1; $i<$width - 1; $i++)
    {
        for($j=1; $j<$height - 1; $j++)
        {
            
            $matrix[0][0] = getPixelGreyScale($image, $i-1, $j-1);
            $matrix[0][1] = getPixelGreyScale($image, $i-1, $j);
            $matrix[0][2] = getPixelGreyScale($image, $i-1, $j+1);

            $matrix[1][0] = getPixelGreyScale($image, $i, $j-1);
            $matrix[1][1] = getPixelGreyScale($image, $i, $j);
            $matrix[1][2] = getPixelGreyScale($image, $i, $j+1);

            $matrix[2][0] = getPixelGreyScale($image, $i+1, $j-1);
            $matrix[2][1] = getPixelGreyScale($image, $i+1, $j);
            $matrix[2][2] = getPixelGreyScale($image, $i+1, $j+1);
            
            $edge =(int) convolution($matrix);

            if($edge > 255)
            {
                $edge = 255;
            }

            

            $val = imagecolorallocate($result, $edge, $edge, $edge);
            
            imagesetpixel($result, $i, $j, $val);
            
        }
    }
    
    header('Content-type: image/jpeg');
    imagejpeg($result);
    
    

}else
{
    echo "FAILED OPENING IMAGE";
}


?>