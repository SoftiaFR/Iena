<?php

$careerId = required_param("career", PARAM_INT);
global $DB;
$requete = $DB->get_record_sql('SELECT * FROM {block_career} WHERE id = ?', array($careerId));

$percent = 70;
$nb_pers = 5;
$titre = $requete->name;
$presence = "En présence";
$date = "24 nov";
$intro = $requete->description;
$img = '';
$titre_module = "Introduction";

$elements = $requete->ressources;
$elements = explode(',', $elements);
$sections = array();
$ressources = array();
$i = 0;
foreach ($elements as $value) {
    $ressource = new block_career_ressource();
    $ressource->get_ressource_by_id($value);
    $sections[$i] = $ressource->section;
    $ressources[$i] = $ressource;
    $i++;
}
//var_dump($sections);
//Supprime les doublons
for($i = 0; $i < count($sections);$i++)
{
    $temp = $i;
    $temp++;

    if ($temp != count($sections))
    {
        if ($sections[$i]->id == $sections[$temp]->id)
        {
            unset($sections[$i]);
        }
    }
}

//Met dans l'orde
$keys = array();
$i = 0;
foreach ($sections as $value){
    $keys[$i] = $value->orde;
    $i++;
}
$sections = array_combine($keys,$sections);
ksort($sections);


?>


<section class="section">
    <div class="card card_block">
        <div class="heading-iena set_height">
            <div class="percent set_height">
                <?= $percent ;?> %
            </div>
           <!--  <div class="nb_pers set_height">
                <?= $nb_pers;?>
            </div> -->
            <div class="titre_section set_height">
                <p><?=$titre;?></p>
            </div>
            <!-- <div class="right_info">
                <div class="label_item">
                    <?= $presence;?>
                </div>
                <div class="label_item">
                    <?= $date;?>
                </div>
                <div class="titre_section set_height">
                    <i class="fa fa-lightbulb-o " aria-hidden="true"></i>
                </div>
                <div class="titre_section set_height">
                    <i class="fa fa-cog " aria-hidden="true" ></i>
                </div>-->
            </div>

        </div>
    <div class="wrapper">
        <div class="description">
            <div class="small">
                <p><?= $intro ;?></p>
            </div>
            <a href="#">Voir la description complète</a>
        </div>
    </div>
    <?php foreach ($sections as $section) : ?>
    <div style="margin-bottom: 0rem; margin-top: 1rem;">
    <div class="card card_block">
        <div class="heading-iena set_height" style="background-color: #009186 !important;">
            <div class="titre_section set_height">
                <p><?php echo $section->name; ?></p>
            </div>
        </div>
    </div>
        <div class="description wrapper">
            <div class="small">
                <p><?= $section->intro ;?></p>
            </div>
            <a href="#">Voir la description complète</a>

        </div>
        <div class="elements">
            <div class="list-group">
            <?php foreach ($ressources as $value) : ?>
            <?php if($value->section->id == $section->id) : ?>
            <div class="row" style="padding-bottom: 0.5rem;">
                <div class="col-md-12 col-sm-12 col-lg-12">
                <a href="<?php echo "$value->link&career=$careerId" ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><img class="" alt="" src="<?php echo $CFG->wwwroot ?>/theme/image.php/boost/<?php echo $value->type ?>/1/icon>">
                       <?php echo $value->name;?></h5></div>

                    <!--<div style="max-height:100px;overflow-y:auto;"><p class="mb-1"><?php echo $value->descrition;?></p></div>--></a>
                </div>
            </div>
                <?php endif;?>
                <?php endforeach;?>
            </div>
        </div></div>
    <?php endforeach;?>
        <!-- </ul> -->
       <!--  </div> -->



</section>
