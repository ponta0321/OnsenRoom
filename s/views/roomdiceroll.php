<div id="dice_box">
    <p><select id="id_dice_count_number" <?=$observer_flag!=1?'':'disabled';?> >
        <option value="1">1</option>
        <option value="2" selected>2</option>
    <?php
        for($i=3;$i<=100;$i++){
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    ?>
    </select>
    D
    <select id="id_dice_surface" <?=$observer_flag!=1?'':'disabled';?> >
        <option value="2">2</option>
        <option value="4">4</option>
        <option value="6" selected>6</option>
        <option value="8">8</option>
        <option value="10">10</option>
        <option value="12">12</option>
        <option value="20">20</option>
        <option value="100">100</option>
    </select>
    <input type="button" id="id_roll_button" value="サイコロを振る" onClick="pushDiceRoll()" <?=$observer_flag!=1?'':'disabled';?> /></p>
    <div id="dice_space" class="clearfix"></div>
</div>