<div class="row d-flex justify-content-around">
    <div>
   
<?php
foreach($sets as $key => $set)
{
    if ($key == "set 4" || $key == "set 5") continue;
    echo $key;
    echo '<br>';
    foreach ($set as $cle => $pos)
    {
        echo 'Position ' . $cle . ': ' . $pos;
        echo '<br>';
    }
}
?>
</div>
<div>
<?php

foreach ($front_or_back as $set => $nb)
{
    if ($set == "set 4" || $set == "set 5") continue;
    echo $set;
    echo '<br>';
    
    echo 'avant : ' . $nb['front'];
    echo '<br>';
    echo 'arrière : ' . $nb['back'];
    echo '<br>';

}
?>
</div>