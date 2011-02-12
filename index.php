<?php

    require_once 'lib/Image.php';

    try
    {
        echo 'Loading image...';

        $image = new Image();
        $image->load('sample.jpg');

        echo '[OK]<br/>Scaling image...';

        $image->scaleProportional(200, 200);
        $image->saveAsJPG('scaleProportional.jpg');
        $image->restore();

        $image->scaleCrop(200, 200);
        $image->saveAsJPG('scaleCrop.jpg');
        $image->restore();

        $image->scale(200, 200);
        $image->saveAsJPG('scale.jpg');

        $image->destroy();

        echo '[OK]<br/>Images saved!';
    }
    catch (Exception $e)
    {
        echo $e->getMessage();
    }
    
?>
